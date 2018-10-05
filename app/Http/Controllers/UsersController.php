<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

Use App\Http\Requests\SaveUser;
Use App\Http\Requests\UpdateUser;
Use App\Http\Requests\ChangePassword;
use App\User;

class UsersController extends Controller
{

	/** 
	 * Get all users.
	 *
	 * @return Json Collection
	*/
	public function all()
	{
		return User::all();
	}

	/** 
	 * Get a user based on ID.
	 *
	 * @param $id - The ID of the user
	 * @return Json App\User - The requested user
	*/
	public function get($id)
	{
		return User::findOrFail($id);
	}

	/** 
	 * Get the authenticated user.
	 *
	 * @return Json App\User - The authenticated user
	*/
	public function getAuth()
	{
		return \Auth::user();
	}

	/** 
	 * Create a new user in the db with bcrypted password
	 *
	 * @param $request - SaveUser custom request 
	 * @return Json App\User - The created user
	*/
    public function create(SaveUser $request)
    {
		// Bcrpt password
		$crypted = bcrypt($request->password);
		// Merge crypted password into request
		$request->merge(['password' => $crypted]); 
		  
    	return $this->genericSave(New User, $request);
    }

	/** 
	 * Updates a user in the db. Does not touch the password.
	 *
	 * @param $request - UpdateUser custom request 
	 * @return Json App\User - The updated user
	*/
    public function update(UpdateUser $request)
    {
    	return $this->genericSave(User::findOrFail($request->id), $request);
    }

	/** 
	 * Changes a specific users password in the db. Uses bcrypt.
	 *
	 * @param $request - ChangePassword custom request 
	 * @return Json App\User - The updated user
	*/
    public function changePassword(ChangePassword $request)
    {
		// Bcrpt password
		$crypted = bcrypt($request->password);
		// Merge crypted password into request
		$request->merge(['password' => $crypted]); 
		  
    	return $this->genericSave(User::findOrFail($request->id), $request);    	
    }

	/** 
	 * Changes the authenticated users password in the db. Uses bcrypt.
	 *
	 * @param $request - ChangePassword custom request 
	 * @return Json App\User - The updated user
	*/
    public function changeAuthPassword(ChangePassword $request)
    {
		// Bcrpt password
		$crypted = bcrypt($request->password);
		// Merge crypted password into request
		$request->merge(['password' => $crypted]); 
		  
    	return $this->genericSave(User::findOrFail(Auth::user()->id), $request);    	
    }    

	/** 
	 * Removes a user from the db.
	 *
	 * @param $id - The id of the user to remove 
	 * @return Int - The id of the removed user
	*/
    public function remove($id)
    {
    	return $this->genericRemove(User::findOrFail($id));
    }
}
