<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\SaveJobPart;

use App\JobPart;
use App\Job;
use App\WorkOrder;

class JobPartsController extends Controller
{
	/** 
	 * Updates the totals an a Job with the totals from the supplied Part. If previous totals are supplied then
	 * we must roll back the Job's totals based on these previous values. After rolling back the totals we can 
	 * update proceed on updating them.
	 *
	 * @param $part - App\JobPart The part with new or updated totals to be added to the Job
	 * @param $job - App\Job The parent Job of the part. Will have it's totals updated
	 * @param $prev_totals - Array['cost','billing'] The totals of the part BEFORE the part was updated. Must be set when a part has been updated
	*/
	private function calcAndSaveJobTotals($part, $job, $prev_totals = false)
	{
    	// If the prev_totals is set then the part was just updated and requires job totals to be rolled back
    	if(! $prev_totals){
	    	// prev_totals is NOT set then simply Cache job totals (for calculations that follow)
	    	$current_parts_total_cost = floatval($job->parts_total_cost);  
	    	$current_parts_total_billed = floatval($job->parts_total_billed);  
	    	$current_grand_total = floatval($job->job_grand_total);    		   		

    	} else {
    		// prev_totals IS set then rollback job totals by subtracting the previous part totals
    		// Cache rolled back totals for calculations that follow
    		$current_parts_total_cost = floatval($job->parts_total_cost) - floatval($prev_totals['cost']);
    		$current_parts_total_billed = floatval($job->parts_total_billed) - floatval($prev_totals['billing']);
	    	$current_grand_total = floatval($job->job_grand_total) - floatval($prev_totals['billing']); 	    		    	 		
    	}

    	// Calculate new parts total cost based on the part just saved
    	$job->parts_total_cost = round(($current_parts_total_cost + floatval($part->total_cost)), 3); 

    	// Calculate new parts total billed based on the part just created
    	$job->parts_total_billed = round(($current_parts_total_billed + floatval($part->billing_price)), 3);  

    	// Calculate the new grand total using new total billed amount
    	$job->job_grand_total = round(($current_grand_total + floatval($part->billing_price)), 3);

    	// Save the job with updated totals
    	$job = $this->genericSave($job);

    	// Load parts into job model so we can return the full updated job
    	$job->load('parts');	

    	return $job;	
	}

	/** 
	 * Create a new part in the db associated with a job. 
	 * - Only creates part if the job is NOT complete and the work order is open (not invoiced).
	 * - After creating the part the parent job is loaded and it's totals are updated with the newly created part totals.
	 *
	 * @param $request - SaveJobPart custom request 
	 * @return Json App\Job - The parent job with updated totals
	*/
    public function create(SaveJobPart $request)
    {
    	// First get the job so we can update the totals and run some checks
    	$job = Job::findOrFail($request->job_id);

		// Check if parent work order is still open (can only create a part on an open (not invoiced) work order)
		// Check if parent job is marked as complete (can only create a part if the job is NOT marked complete)
		if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
	    	// Save the part
	    	$part = $this->genericSave(new JobPart, $request);
	    	// Update the parent job with new totals based on added part
	    	$job = $this->calcAndSaveJobTotals($part, $job);	
	    	
			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($job->work_order_id);				
		} else {
			// Failed response. Work order is closed or job is complete
			return response()->json($this->woGuardResponse, 422);	
		}
    }

	/** 
	 * Update a part in the db associated with a job. 
	 * - Only updates the part if the job is NOT complete and the work order is open (not invoiced).
	 * - After updating the part the parent job is loaded and it's totals are updated with the newly created part totals.
	 *
	 * @param $request - SaveJobPart custom request 
	 * @return Json App\Job - The parent job with updated totals
	*/
    public function update(SaveJobPart $request)
    {
    	// First get the job so we can update the total
    	$job = Job::findOrFail($request->job_id); 
    	   	
		// Check if parent work order is still open (can only update a part on an open (not invoiced) work order)
		// Check if parent job is marked as complete (can only update a part if the job is NOT marked complete)
		if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
	    	// Get the part
	    	$part = JobPart::findOrFail($request->id);
	    	// Cache the previous part's cost and billing price
	    	$part_cost = $part->total_cost;
	    	$part_billing = $part->billing_price;

	    	// Save the part and cache result
	    	$part = $this->genericSave($part, $request);

	    	// Update the parent job with new totals based on added part
	    	$job = $this->calcAndSaveJobTotals($part, $job, 
	    		// Previous totals for rolling backing job totals
	    		['cost'=> $part_cost, 'billing'=> $part_billing]
	    	);

			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($job->work_order_id);				
		} else {
			// Failed response. Work order is closed or job is complete
			return response()->json($this->woGuardResponse, 422);	
		}
    }

	/** 
	 * Finds all parts associated with a supplier
	 *
	 * @param $id - The ID of the supplier
	 * @return Json Collection - The parts
	*/
    public function getSuppliers($id)
    {
    	return JobPart::where('supplier_id', '=', $id)->get();
    }

	/** 
	 * Removes a part from the db
	 * - Only removes the part if the job is NOT complete and the work order is open (not invoiced).
	 * - After removing the part the parent job is loaded and it's totals are updated with the newly created part totals.
	 *
	 * @param $id - The ID of the job to remove 
	 * @return Json App\Job - The parent job with updated totals
	*/
    public function remove($id)
    {
    	// Get the part
    	$part = JobPart::findOrFail($id);
    	// Get the parent job
    	$job = Job::findOrFail($part->job_id);

		// Check if parent work order is still open (can only remove a part from an open (not invoiced) work order)
		// Check if parent job is marked as complete (cannot remove a part from a job marked complete)
		if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
	    	// Cache the part cost and total billed
	    	$part_total_cost = $part->total_cost;
	    	$part_total_billed = $part->billing_price;	
	    			
			// Part is allowed to be removed, so remove it
			$id = $this->genericRemove($part);

			// Update job totals by removing the deleted part costs
    		$job->parts_total_cost = floatval($job->parts_total_cost) - floatval($part_total_cost);
    		$job->parts_total_billed = floatval($job->parts_total_billed) - floatval($part_total_billed);
	    	$job->job_grand_total = floatval($job->job_grand_total) - floatval($part_total_billed);  
	    	// Save job
	    	$job = $this->genericSave($job);

			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($job->work_order_id);	
		} else {
			// Failed response. Work order is closed or job is complete
			return response()->json($this->woGuardResponse, 422);	
		}   	
    }
}
