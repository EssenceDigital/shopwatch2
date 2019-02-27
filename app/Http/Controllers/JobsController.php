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
	 * Calculates labour, tech pay totals and populates totals and related fields in a job model
	 *
	 * @param $request - The job request
	 * @param $job - A job model
	 * @return $job - Populated job model
	*/
	private function calculateLabourTotals($request, $job)
	{
		// Get the tech (user) first
		$tech = User::findOrFail($request->tech_id);

		/*
		 * For regular hourly jobs use the shop rate and total hours to calculate
		 * labour and tech pay totals
		*/
		if(! $request->is_flat_rate){
			// If a shop rate was with the request then assign that
			if($request->has('shop_rate')){
				// Set custom shop rate for job
				$shop_rate = $request->shop_rate;
			} else {
				// No shop rate present in request then get the current shop rate from business settings
				$shop_rate = BusSetting::findOrFail(1)->shop_rate;
			}
			// Calculate the labour total
			$labour_total = round((floatval($request->hours) * floatval($shop_rate)), 3);
			// Calculate the tech pay total
			$tech_pay_total = round(floatval($request->hours) * floatval($tech->hourly_wage), 3);

		} else {
			/*
			 * For flat rate jobs assign the flat rate total as the labour total
			*/
			$labour_total = round(floatval($request->flat_rate), 3);
		}

		// Add labour total to grand total
		$grand_total = floatval($job->job_grand_total) + floatval($labour_total);

		/*
		 * Populate job model for hourly jobs
		*/
		if(! $request->is_flat_rate){
			$job->tech = $tech->name;
			$job->tech_id = $tech->id;
			$job->tech_hourly_rate = $tech->hourly_wage;
			$job->tech_pay_total = $tech_pay_total;
			$job->hours = $request->hours;
			$job->shop_rate = $shop_rate;
			$job->job_labour_total = $labour_total;
			$job->job_grand_total = $grand_total;
		} else {
			/*
			 * Populate job model for flat rate jobs
			*/
			$job->tech = $tech->name;
			$job->tech_id = $tech->id;
			$job->flat_rate = $request->flat_rate;
			$job->flat_rate_cost = $request->flat_rate_cost;
			$job->job_labour_total = $labour_total;
			$job->job_grand_total = $grand_total;
		}

		return $job;
	}

	/**
	 * Completes the population of a job model including parsing out all parts and
	 * delegating the calculation of parts totals and adding to the job model
	 *
	 * @param $job - A job model
	 * @param $request - The job request
	 * @return $job - Populated job model
	*/
	private function prepJobForSave($job, $request)
	{
		$job->work_order_id = $request->work_order_id;
		$job->title = $request->title;
		$job->description = $request->description;
		$job->is_flat_rate = $request->is_flat_rate;
		$job->is_premade = $request->is_premade;

		// Add parts from request to job - request must be formatted properly
		$job->parts = $request->parts;
		// Tally parts totals and update corresponding job totals
		foreach($request->parts as $part){
			// Update totals in job as we go
			$job = $this->calculatePartsTotals($part, $job);
		}

		// Next, tally labour totals and update job totals
		$job = $this->calculateLabourTotals($request, $job);

		return $job;
	}

	/**
	 * Get a job based on ID.
	 *
	 * @param $id - The ID of the job
	 * @return Json App\Job - The requested job
	*/
	public function get($id)
	{
		return Job::findOrFail($id);
	}

	/**
	 * Create a new job and associated parts in the db
	 * Parts are stored in the db as JSON in the parts field.
	 * Only creates the job if the parent work order is open.
	 *
	 * @param $request - SaveJob custom request
	 * @return Json App\Job - The created job
	*/
	public function create(SaveJob $request)
	{
		// Check if parent work order is still open
		if($this->ensureWorkOrderIsOpen($request->work_order_id)){
			// Calclate job totals
			$job = $this->prepJobForSave(new Job, $request);

			// Save job
			$job = $this->genericSave($job);

			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs'])->findOrFail($job->work_order_id);

		} else {
    		// Failed response
	      return response()->json($this->woGuardResponse, 422);
		}
	}

	/**
	 * Update an existing job and associated parts in the db
	 * Parts are stored in the db as JSON in the parts field.
	 * Only updates the job if the parent work order is open.
	 *
	 * @param $request - UpdateJob custom request
	 * @return Json App\Job - The updated job
	*/
	public function update(UpdateJob $request)
	{
		$job = Job::findOrFail($request->id);
		// Check if parent work order is still open (can only modify a part on an open (not invoiced) work order)
		// Check if parent job is marked as complete (cannot modify a part on a job marked complete)
		if($this->guardWorkOrder($job->work_order_id, $job->is_complete)){
			// Rollback job totals
			$job->tech_pay_total = 0;
			$job->job_labour_total = 0;
			$job->parts_total_cost = 0;
			$job->parts_total_billed = 0;
			$job->job_grand_total = 0;
			// Calclate job totals
			$job = $this->prepJobForSave($job, $request);
			// Save job
			$job = $this->genericSave($job);

			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs'])->findOrFail($job->work_order_id);
		} else {
    		// Failed response
	      return response()->json($this->woGuardResponse, 422);
		}
	}

	/**
	 * Flags the specified job as complete in the db
	 *
	 * @param $id - The id of the job to remove
	 * @return Int - The id of the removed job
	*/
	public function markComplete(CompleteJob $request)
	{
		// Get the job
		$job = Job::findOrFail($request->id);

		// Check if parent work order is still open (can only modify a part on an open (not invoiced) work order)
		if($this->ensureWorkOrderIsOpen($job->work_order_id)){
			// Update job
			$job->is_complete = $request->is_complete;
			// Save job
			$job = $this->genericSave($job);
			// Find and return parent work order
			return WorkOrder::with(['customer', 'vehicle', 'jobs'])->findOrFail($job->work_order_id);
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
			return WorkOrder::with(['customer', 'vehicle', 'jobs'])->findOrFail($wo_id);
		} else {
    		// Failed response
	        return response()->json($this->woGuardResponse, 422);
		}
	}
}
