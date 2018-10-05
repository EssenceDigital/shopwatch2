<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


Use App\Http\Requests\SaveBusConfig;
use App\BusSetting;

class BusConfigController extends Controller
{
	/** 
	 * Retrieves the single business config entry
	 *
	 * @return - The single business config entry
	*/
    public function get()
    {
    	return BusSetting::find(1);
    }

	/** 
	 * Updates the single business config entry
	 *
	 * @param - $request - SaveBusConfig custom request
	 * @return - The single business config entry
	*/
    public function update(SaveBusConfig $request)
    {
    	return $this->genericSave(BusSetting::find(1), $request);
    }
}
