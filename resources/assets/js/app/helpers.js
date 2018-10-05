/**
 * Contains some useful methods that can be used by components to do some mundane and repetitive tasks. 
 *
*/
export default {
	/** 
	 * Searches an array of objects for an 'id' and returns the matched object's index.
	 * Each object in the array should have an
	 *
	 * @param Array arrayToSearch - The array of objects to search (haystack)
	 * @param String idKey - The field on each object that identifies it (Ex. 'id' or 'project_id')
	 * @param Int idToMatch - The value of the id to match (needle)
	 * @return Int - The index of the matched object in the array
	*/
	pluckObjectById (arrayToSearch, idKey, idToMatch){
		// Search array and match object id
		var item = arrayToSearch.find(elem => elem[idKey] == idToMatch),
				index = arrayToSearch.indexOf(item);
		// Return the plucked index
		return index;
	},

	/** 
	 * Populates an object with data from the supplied form.
	 *
	 * @param Obj form - An object that contains objects for each form field. { val: , error: , errorMsg: , dflt: }.
	 * @return Promise - Resolves a function with the populated data array.
	*/
	populatePostData (form) {
		return new Promise((resolve, reject) => {
			var data = {};
			for(var key in form){
				data[key] = form[key].val;
			}
			resolve(data);
		});
	},

	/** 
	 * Populates the supplied form with the supplied data
	 *
	 * @param Obj form - An object that contains objects for each form field. { val: , error: , errorMsg: , dflt: }.
	 * @param Obj data - An object with fields that correspond to the form.
	 * @return Promise - Resolves a function with the populated form.
	*/
	populateForm (form, data) {
		// Populate form
		for(var key in form) {
			form[key].value = data[key];
		}
	},

	/** 
	 * Resets the supplied form fields to their default state, including removing error messages.
	 *
	 * @param Obj form - An object that contains objects for each form field. { val: , error: , errorMsg: , dflt: }.
	 * @return Promise - Resolves a function with the now cleared form.
	*/
	clearForm (fields) {
		// Clear all form fields
		for(var key in fields) {
			fields[key].value = ''; 
		}		
	},

	/** 
	 * Sets all form fields to an error state if they are present in the errors prop.
	 *
	 * @param Obj form - An object that contains objects for each form field. { val: , error: , errorMsg: , dflt: }.
	 * @param Obj errors - Each field in error state will have an object here. Returned by Laravel validate method.
	 * @param Promise - Resolves a function
	*/
	populateFormErrors (fields, errors){
		// Populate form errors
		for(var key in errors) {
			fields[key].errors = errors[key]; 
		}
	},

	/**
	 * Clears the supplied form fields of any possible error state.
	 *
	 * @param Obj form - An object that contains objects for each form field. { val: , error: , errorMsg: , dflt: }.	 
	*/
	clearFormErrors (fields) {
		// Clear are form error fields
		for(var key in fields) {
			fields[key].errors = []; 
		}
	},

	/**
	 * Creates a date time string compatible with the sql db. Takes a date, and a time in am/pm form.
	 *
	 * @param date - String - YYYY-MM-DD
	 * @param time - String - Ex: 9:30am 
	*/
	constructDateTimeString (date, time) {
		// Cache some vars to start
		let rawTime = time.slice(0, -2),
				ampm = '',
				time24hr = '';

		// 6 digit time means the hour is one digit, so splice accordingly
		if(time.length === 6){
			// Single digit time, get am or pm
			ampm = time.slice(4, 6);
			// Get hours and mins, splice for 1 digit hour
			let hour = rawTime.slice(0, 1);
			let mins = rawTime.slice(2, 4);        		
			// If its pm then we need to add 12 hours to create the 24 hr time
	  	if(ampm === 'pm'){
	  		// Create 24 hr hour
	  		let hour24hr = parseInt(hour) + 12;
	  		// Reconstruct time as 24 hr
	  		time24hr = hour24hr + ':' + mins + ':00'; 
	  	} 
	  	// If its am we need to do some checks and create the 24 hr time
	  	else if(ampm === 'am'){
	  		// Construct 24 hr time
	  		time24hr = '0' + rawTime + ':00';
	  	}
		} 
		// 7 digit time means the hour is two digit, so the splicing changes
		else if(time.length === 7) {
			// Two digit time, get am or pm
			ampm = time.slice(5, 7);
			// Get hours and mins, splice for 2 digit hour
			let hour = rawTime.slice(0, 2);
			let mins = rawTime.slice(3, 5);        		
			// If its pm then we need to add 12 hours to create the 24 hr time
	  	if(ampm == 'pm'){	        		
	  		// Create 24 hr hour
	  		let hour24hr = hour;
	  		// If the hour is not 12 pm we have to add 12 to it
	  		if(parseInt(hour) !== 12){
					hour24hr = parseInt(hour) + 12;
	  		} 
	  		// Reconstruct time as 24 hr
	  		time24hr = hour24hr + ':' + mins + ':00'; 
	  	}	
	  	// If its am we need to do some checks and create the 24 hr time
	  	else if(ampm === 'am'){
	  		// If its 12 am then replace the 12 with 0
	  		if(parseInt(hour) === 12){
	  			hour = '00';
	  		}
	  		// Construct 24 hr time
	  		time24hr = hour + ':' + mins +':00';
	  	}      		
		}		

		return date + ' ' + time24hr;
	}
	
}