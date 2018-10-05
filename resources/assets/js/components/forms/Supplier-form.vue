<template>
	<base-form 
		:action="action" 
		:edit-state="editState"
		:fields="form" 
		@saved="saved"
		@error="failed"
	>
		<template slot="form-fields">
			<!-- Name -->
			<v-text-field
	      label="Supplier name"
	 			v-model="form.name.value"
	 			:error-messages="form.name.errors"
	    ></v-text-field>	    	    	    	    	    		    		
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';	

	export default{
		props: ['action', 'supplier', 'editState'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					name: {value: '', errors: []}
				}
			}
		},

		watch: {
			supplier (supplier){
				// Populate the form for editing
				if(supplier){
					Helpers.populateForm(this.form, supplier);
				}
			}
		},

		components: {
			'base-form': BaseForm
		},

		methods: {
			saved (){
				if(! this.editState){
					// Clear form 
					Helpers.clearForm(this.form);
				}
				// Clear form errors
				Helpers.clearFormErrors(this.form);
				// Notify parent component
				this.$emit('saved');
			},

			failed (errors){
				Helpers.populateFormErrors(this.form, errors);
			}
		},

		created (){
			// Populate form, if needed
			if(this.supplier){
				Helpers.populateForm(this.form, this.supplier);
			}			
		}
	}
</script>