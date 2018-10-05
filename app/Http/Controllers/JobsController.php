<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

Use App\Http\Requests\SaveJob;
Use App\Http\Requests\UpdateJob;
Use App\Http\Requests\CompleteJob;
Use App\WorkOrder;
Use App\Job;
Use App\User;
Use App\BusSetting;

class JobsController extends Controller
{

	/** 
	 * Calculates and sets the totals that will be saved in a job. 
	 * Merges new values with the supplied request so it can be easily saved into a model.
	 *
	 * @param $request - The job request
	 * @param $job - The parent job. Should be set when a job has been updated.
	 * @return The merged request
	*/
	private function calculateJobTotals($request, $job = false)
	{
		// Get the tech (user) first
		$tech = User::where('name', $request->tech)->first();

		// If a shop rate was with the request then assign that
		if($request->has('shop_rate')){
			// Set custom shop rate for job
			$shop_rate = $request->shop_rate;
		} else {
			// No shop rate present in request then get the current shop rate from business settings
			$shop_rate = BusSetting::findOrFail(1)->shop_rate;			
		}

		// Calculate the current labour total 
		$labour_total = round((floatval($request->hours) * floatval($shop_rate)), 3);
		// Calculate the current tech pay total
		$tech_pay_total = round(floatval($request->hours) * floatval($tech->hourly_wage), 3);

		// Determine if the grand total should be calculated, or assinged the labour total
		if(! $job){
			$grand_total = $labour_total;
		} else {
			// First, rollback the grand total based on the job old labour total
			// Next, add the updated labour total to get the new grand total
			$grand_total = (floatval($job->job_grand_total) - floatval($job->job_labour_total)) + floatval($labour_total);
		}

		// Merge the calculated data with the request
		$request->merge([
			'tech' => $tech->name, 
			'tech_hourly_rate' => $tech->hourly_wage,
			'tech_pay_total' => $tech_pay_total,
			'shop_rate' => $shop_rate,
			'job_labour_total' => $labour_total,
			// Will get updated later if there is parts on this job (likely)
			'job_grand_total' => $grand_total
		]);

		return $request;	
	}

	/** 
	 * Get a job based on ID.
	 *
	 * @param $id - The ID of the job
	 * @return Json App\Job - The requested job
	*/
	public function get($id)
	{
		return Job::with(['parts'])->findOrFail($id);
	}

	/** 
	 * Create a new job in the db associated with a work order. 
	 * Only creates the job if the parent work order is open.
	 *
	 * @param $request - SaveJob custom request 
	 * @return Json App\Job - The created job
	*/
	public function create(SaveJob $request)
	{
		// Check if parent work order is still open
		if($this->ensureWorkOrderIsOpen($request->work_order_id)){
			// Calculate totals before saving
			$request = $this->calculateJobTotals($request);
			// Save job
			$job = $this->genericSave(new Job, $request);
			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($job->work_order_id);

		} else {
    		// Failed response
	        return response()->json($this->woGuardResponse, 422);			
		}
	}

	/** 
	 * Update an existing job in the db associated with a work order.
	 * Only updates the job if the parent work order is open.	 
	 *
	 * @param $request - UpdateJob custom request 
	 * @return Json App\Job - The updated job
	*/
	public function update(UpdateJob $request)
	{
		// Get the job
		$job = Job::with(['parts'])->findOrFail($request->id);

		// Check if parent work order is still open (can only modify a part on an open (not invoiced) work order)
		// Check if parent job is marked as complete (cannot modify a part on a job marked complete)
		if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
			// Calculate totals before saving
			$request = $this->calculateJobTotals($request, $job);
			// Save job
			$job = $this->genericSave($job, $request);		
			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($job->work_order_id);				
		} else {
    		// Failed response
	        return response()->json($this->woGuardResponse, 422);			
		}
	}

	public function markComplete(CompleteJob $request)
	{
		// Get the job
		$job = Job::with(['parts'])->findOrFail($request->id);

		// Check if parent work order is still open (can only modify a part on an open (not invoiced) work order)
		if($this->ensureWorkOrderIsOpen($job->work_order_id)){
			// Update job
			$job->is_complete = $request->is_complete;
			// Save job
			$job = $this->genericSave($job);		
			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($job->work_order_id);				
		} else {
    		// Failed response
	        return response()->json($this->woGuardResponse, 422);			
		}		
	}

	/** 
	 * Removes a job from the db. 
	 * Only removes the job if the parent work order is open and the job has not been marked complete.
	 *
	 * @param $id - The id of the job to remove 
	 * @return Int - The id of the removed job
	*/
	public function remove($id)
	{
		// Find the job
		$job = Job::findOrFail($id);

		// Check if parent work order is still open (can only modify a part on an open (not invoiced) work order)
		// Check if parent job is marked as complete (cannot modify a part on a job marked complete)
		if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
			// Get wo ID
			$wo_id = $job->work_order_id;
			// Job is allowed to be removed
			$id =  $this->genericRemove($job);
			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs', 'jobs.parts', 'jobs.parts.supplier'])->findOrFail($wo_id);				
		} else {
    		// Failed response
	        return response()->json($this->woGuardResponse, 422);			
		}
	}
}
