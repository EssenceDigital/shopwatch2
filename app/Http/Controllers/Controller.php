<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

Use App\WorkOrder;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
    * A common response, cached for ease of use
    */
    protected $woGuardResponse = [
	    'result' => 'error',
	    'message' => "This work order is either closed (invoiced), or the requested job has been marked complete. It cannot be modified. If the job must me modified try changing it's status to active"
    ];

    /**
  	 * Provides a simple interface to fill a model, save it or return errors, and pass back the model
  	 *
  	 * @param $model - A model
  	 * @param $request - An optional request to fill the model with
  	 * @return $model - The saved model
  	*/
    protected function genericSave($model, $request = false)
    {
    	/* Check if the before fill function should run
    	if(is_callable($beforeFill)){
    		// Run before fill function with request passed as a reference
    		call_user_func_array($beforeFill, array(&$request));
    	}*/

    	// If there is a request then populate the model with it
    	if($request){
    		// Populate the model with request data
    		$model->fill($request->all());
    	}

    	// Save the model, if it does not save then return a failed response
    	if(! $model->save()){
    		// Failed response
	        return response()->json([
	            'result' => 'error',
	            'message' => 'Problem storing record.'
	        ], 422)->send();
    	}

    	return $model;
    }

    /**
  	 * Provides a simple interface to access basic where and where between db filters
  	 *
  	 * @param $query - The start of the query Model::[with, orderBy, etc.]
  	 * @param $whereFields - Array - ['filter', 'field', 'value' 'conditional']
  	 * @param $whereBetweenFields - Array - ['first => ['field', 'value'], 'second' => ['field', 'value']]
  	 * @return $collection - Result of the query
  	*/
    protected function genericFilter($query, $whereFields, $whereBetweenFields = false)
    {
	    // Array to hold where clauses
	    $queryArray = [];

	    // Add all where clauses to query array
	    forEach($whereFields as $current){
	    	// If the filter value is true then this field should be filtered with where clause
	    	if($current['filter']){
	    		// Special value means we want to filter based on a boolean (This lets us bypass some conditional checks)
	    		if($current['value'] == 'false-bool') $current['value'] = 0;
	    		// Add the dynamic where clause to the query array
	    		array_push($queryArray, [$current['field'], $current['conditional'], $current['value']]);
	    	}
	    }

	    // Add where clauses to query
	    $query->where($queryArray);

	    // If there are where between fields to be added to the query
	    if($whereBetweenFields){
	    	$query->whereBetween(
	    		$whereBetweenFields['first']['field'],
	    		[$whereBetweenFields['first']['value'], $whereBetweenFields['second']['value']]
	    	);
	    }

	    // Now find the collection
	    $collection = $query->get();

	    return $collection;
    }

    /**
  	 * Provides a simple interface to remove a model (record) or return an error, and return the id of
     * the removed record.
  	 *
  	 * @param $model - A populated model
  	 * @return $id - The id of the removed record
  	*/
    protected function genericRemove($model)
    {
    	// Cache id for later
    	$id = $model->id;

    	// Check if the before remove function should run
    	/*if(is_callable($beforeRemove)){
    		// Run before fremove function with model passed as a reference
    		call_user_func_array($beforeRemove, array(&$model));
    	}*/

    	// Remove the record, if a problem with removal then return a failed response
    	if(! $model->delete()){
    		// Failed response
	        return response()->json([
	            'result' => 'error',
	            'message' => 'Problem removing record.'
	        ], 422)->send();
    	}

    	// Check if the after remove function should run
    	/*if(is_callable($afterRemove)){
    		// Run after remove function
    		call_user_func_array($afterRemove);
    	}*/

    	return $id;
    }

    /**
  	 * Updates the totals an a Job with the totals from the supplied Part. If previous totals are supplied then
  	 * we must roll back the Job's totals based on these previous values. After rolling back the totals we can
  	 * update proceed on updating them.
  	 *
  	 * @param $part - App\JobPart The part with new or updated totals to be added to the Job
  	 * @param $job - App\Job The parent Job of the part. Will have it's totals updated
  	*/
  	protected function calculatePartsTotals($part, $job)
  	{
        // Parts costs -- ROLLBACK TO ZERO (IN PARENT) FOR UPDATES?
        $current_parts_total_cost = floatval($job->parts_total_cost);
	    	$current_parts_total_billed = floatval($job->parts_total_billed);
        // Grand total
	    	$current_grand_total = floatval($job->job_grand_total);

      	// Add part cost to total parts cost
      	$job->parts_total_cost = round(($current_parts_total_cost + (floatval($part['total_cost']) * $part['quantity'])), 3);

        // Billing price of part(s)
        $parts_added_billed_cost = floatval($part['billing_price']) * $part['quantity'];

      	// Add part billed price to total parts billed
      	$job->parts_total_billed = round(($current_parts_total_billed + floatval($parts_added_billed_cost) ), 3);

      	// Calculate the new grand total, adding the part billing price
      	$job->job_grand_total = round(($current_grand_total + floatval($parts_added_billed_cost)), 3);

      	return $job;
  	}

    /**
  	 * Ensures a work order is only accessible if it meets certain conditions.
  	 *
  	 * @param $work_order_id - id of the work order
  	 * @param $job_is_complete - job completed flag
  	 * @return Boolean
  	*/
    protected function guardWorkOrder($work_order_id, $job_is_complete)
    {
    	if($this->ensureWorkOrderIsOpen($work_order_id)){
    		if($this->ensureJobIsNotComplete($job_is_complete)){
    			return true;
    		}
    	}

    	return false;
    }

	/**
	 * Returns an error if the work order is closed (invoiced) and true if the work order is open (not invoiced)
	 *
	 * @param $work_order_id - The ID of the parent work order
	 * @return Boolean or json error
	*/
	protected function ensureWorkOrderIsOpen($work_order_id)
	{
		// First get the owning work order so we can make sure its still open and not invoiced
		$wo = WorkOrder::findOrFail($work_order_id);

		// If work order is closed (invoiced) then stop here
		if($wo->is_invoiced){
    		// Failed response
	        return false;
	    } else {
	    	return true;
	    }
	}

  /**
	 * Returns true or false depending on the job completion flag
	 *
	 * @param $is_complete - The job completion flag
	 * @return Boolean or json error
	*/
	protected function ensureJobIsNotComplete($is_complete)
	{
		if($is_complete){
    		// Failed response
	        return false;
		} else {
			return true;
		}
	}
}
