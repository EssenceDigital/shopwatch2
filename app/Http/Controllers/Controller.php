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

    protected $woGuardResponse = [
	    'result' => 'error',
	    'message' => "This work order is either closed (invoiced), or the requested job has been marked complete. It cannot be modified. If the job must me modified try changing it's status to active"
    ];

    protected function genericSave($model, $request = false, $beforeSave = null, $afterSave = null)
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

    protected function genericRemove($model, $beforeRemove = null, $afterRemove = null)
    {
    	// Cache id for later
    	$id = $model->id;

    	// Check if the before remove function should run
    	if(is_callable($beforeRemove)){
    		// Run before fremove function with model passed as a reference
    		call_user_func_array($beforeRemove, array(&$model));
    	}

    	// Remove the record, if a problem with removal then return a failed response
    	if(! $model->delete()){
    		// Failed response
	        return response()->json([
	            'result' => 'error',
	            'message' => 'Problem removing record.'
	        ], 422)->send();      		
    	}

    	// Check if the after remove function should run
    	if(is_callable($afterRemove)){
    		// Run after remove function
    		call_user_func_array($afterRemove);
    	}    	

    	return $id;
    }

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
	 * Returns and error if the work order is closed (invoiced) and true if the work order is open (not invoiced)
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
