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
			// Can only add parts to hourly jobs
			if(! $job->is_flat_rate){
				// Check if parent work order is still open (can only create a part on an open (not invoiced) work order)
				// Check if parent job is marked as complete (can only create a part if the job is NOT marked complete)
				if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
						// Extract current parts array from Job
						$parts = $job->parts;
						// New part to add
						$part = $request->all();
						// Add unique id to part
						$id = uniqid();
						$part['id'] = $id;
						// Push new part to parts array
						$parts[$id] = $part;
						// Update parts in Job model
						$job->parts = $parts;
			    	// Update the parent job with new totals based on added part
			    	$job = $this->calculateUpdatedJobTotals($part, $job);
						// Save the job with updated totals
		      	$job = $this->genericSave($job);

						// Find and return parent work order
						return WorkOrder::with(['customer', 'vehicle', 'jobs'])->findOrFail($job->work_order_id);
					} else {
						// Failed response. Work order is closed or job is complete
						return response()->json($this->woGuardResponse, 422);
					}
			} else {
				// Failed response. Flat rate job cant have parts
				return response()->json("Can't add parts to a flat rate job", 422);
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
				// Get parts from job
				$parts = $job->parts;
				// Find part to be updated
				$partToUpdate = $parts[$request->id];
				// Cache the previous part's cost and billing price
	    	$part_cost = floatval($partToUpdate['total_cost']) * $partToUpdate['quantity'];
	    	$part_billing = floatval($partToUpdate['billing_price']) * $partToUpdate['quantity'];
				// Replace part with updated part in parts array
				$parts[$request->id] = $request->all();
				// Replace updated parts in job
				$job->parts = $parts;

	    	// Update the parent job with new totals and added part
	    	$job = $this->calculateUpdatedJobTotals($request->all(), $job,
	    		// Previous totals for rolling backing job totals
	    		['cost'=> $part_cost, 'billing'=> $part_billing]
	    	);
				// Save the job with updated totals
				$job = $this->genericSave($job);

				// Find and return parent work order
				return WorkOrder::with(['customer', 'vehicle', 'jobs'])->findOrFail($job->work_order_id);
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
    public function remove($part_id, $job_id)
    {
    	// Get the parent job
    	$job = Job::findOrFail($job_id);

			// Check if parent work order is still open (can only remove a part from an open (not invoiced) work order)
			// Check if parent job is marked as complete (cannot remove a part from a job marked complete)
			if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
					$part = $job->parts[$part_id];
		    	// Cache the part cost and total billed
		    	$part_total_cost = floatval($part['total_cost']) * $part['quantity'];
		    	$part_total_billed = floatval($part['billing_price']) * $part['quantity'];;

					// Part is allowed to be removed, so remove it
					$parts = $job->parts;
					// Remove part from array
					unset($parts[$part_id]);
					// Add parts with removed part back to job
					$job->parts = $parts;

					// Update job totals by removing the deleted part costs
	    		$job->parts_total_cost = floatval($job->parts_total_cost) - floatval($part_total_cost);
	    		$job->parts_total_billed = floatval($job->parts_total_billed) - floatval($part_total_billed);
		    	$job->job_grand_total = floatval($job->job_grand_total) - floatval($part_total_billed);
		    	// Save job
		    	$job = $this->genericSave($job);

				// Find and return parent work order
				return WorkOrder::with(['customer', 'vehicle', 'jobs'])->findOrFail($job->work_order_id);
			} else {
				// Failed response. Work order is closed or job is complete
				return response()->json($this->woGuardResponse, 422);
			}
    }
}
