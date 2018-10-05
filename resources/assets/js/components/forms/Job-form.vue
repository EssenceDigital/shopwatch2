<template>
	<base-form 
		:action="action" 
		remove-action="removeJob"
		:edit-state="editState"
		:fields="form" 
		@saved="saved"
		@error="failed"
	>
		<template slot="form-fields">
			<!-- Title -->
			<v-text-field
	      label="Title"
	 			v-model="form.title.value"
	 			:error-messages="form.title.errors"
	    ></v-text-field>
			<!-- Description -->
			<v-text-field
	      label="Description"
	 			v-model="form.description.value"
	 			:error-messages="form.description.errors"
	 			multi-line
	    ></v-text-field>	    
	    <!-- Hours -->
	    <v-flex xs2>
				<v-text-field
					type="number"
					min="0"
					step="0.1"
		      label="Hours"
		 			v-model="form.hours.value"
		 			:error-messages="form.hours.errors"
		    ></v-text-field>		    	
	    </v-flex>
			<!-- Tech -->
			<v-select
        :items="techSelect"
        v-model="form.tech.value"
        :error-messages="form.tech.errors"
        label="Tech"
        single-line
        bottom
      ></v-select>	         	    	    	    		    		
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';	

	export default{
		props: ['action', 'job', 'workOrder', 'editState'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					work_order_id: {value: '', errors: []},
					title: {value: '', errors: []},
					description: {value: '', errors: []},
					hours: {value: '', errors: []},
					tech: {value: '', errors: []}
				},
				completeSelect: [
					{ text: 'No', value: 0 },
					{ text: 'Yes', value: 1 }
				]
			}
		},

		computed: {
			techSelect (){
				return this.$store.getters.techSelect;
			}
		},

		watch: {
			job (job){
				// Populate the form for editing
				if(job){
					Helpers.populateForm(this.form, job);
				}
			},

			workOrder (id){
				// Set WO ID
				if(id){
					this.form.work_order_id.value = id;
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
			// Populate form with supplied job, if needed
			if(this.job){
				Helpers.populateForm(this.form, this.job);
			}			
			// Set WO ID
			if(this.workOrder){
				this.form.work_order_id.value = this.workOrder;
			}					
		}
	}
</script>