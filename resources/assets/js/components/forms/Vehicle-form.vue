<template>
	<base-form
		:title="title"
		:action="action"
		remove-action="removeVehicle"
		:edit-state="editState"
		:fields="form"
		@saved="saved"
		@error="failed"
		@close="close"
	>
		<template slot="form-fields">
			<!-- Customer -->
			<v-autocomplete
        v-model="form.customer_id.value"
        hint="Type customer name"
        :items="customersSelect"
        label="Customer"
        persistent-hint
        prepend-inner-icon="person"
				append-outer-icon="person_add"
				@click:append-outer="openCustomerDialog"
				class="mb-2"
      >
      </v-autocomplete>
			<!-- Make -->
			<v-text-field
	      label="Make"
	 			v-model="form.make.value"
	 			:error-messages="form.make.errors"
	    ></v-text-field>
	    <!-- Model -->
			<v-text-field
	      label="Model"
	      v-model="form.model.value"
	      :error-messages="form.model.errors"
	    ></v-text-field>
	    <!-- Year -->
			<v-text-field
	      label="Year"
	      v-model="form.year.value"
	      :error-messages="form.year.errors"
	    ></v-text-field>
	    <!-- VIN -->
			<v-text-field
	      label="VIN"
	      v-model="form.vin.value"
	      :error-messages="form.vin.errors"
	    ></v-text-field>
	    <!-- Plate Number -->
			<v-text-field
	      label="Plate Number"
	      v-model="form.plate_number.value"
	      :error-messages="form.plate_number.errors"
	    ></v-text-field>

			<!-- Add customer dialog -->
		 <v-dialog v-model="addCustomerDialog" persistent max-width="500px">
			 <customer-form
			 	title="Add Customer"
			 	action="createCustomer"
				@saved="customerSaved"
				@close="addCustomerDialog = false"
				></customer-form>
		 </v-dialog>
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';
	import CustomerForm from './Customer-form';

	export default{
		props: ['title', 'action', 'vehicle', 'customer', 'editState'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					customer_id: {value: '', errors: []},
					make: {value: '', errors: []},
					model: {value: '', errors: []},
					year: {value: '', errors: []},
					vin: {value: '', errors: []},
					plate_number: {value: '', errors: []}
				},
				addCustomerDialog: false
			}
		},

		watch: {
			vehicle (vehicle){
				if(vehicle){
					Helpers.populateForm(this.form, vehicle);
				}
			},

			customer (id){
				if(id){
					this.form.customer_id.value = id;
				}
			}
		},

		computed: {
			customersSelect (){
				return this.$store.getters.customersSelect;
			},
		},

		components: {
			'base-form': BaseForm,
			'customer-form': CustomerForm
		},

		methods: {
			openCustomerDialog (){
				this.addCustomerDialog = true;
			},
			customerSaved (id){
				// Set customer id to saved customer
				this.form.customer_id.value = id;
				// Close dialog-
				this.addCustomerDialog = false;
			},
			close (){
				// Clear form
				Helpers.clearForm(this.form);
				Helpers.clearFormErrors(this.form);
				// Notify parent component
				this.$emit('close');
			},
			saved (id){
				// Clear form
				Helpers.clearForm(this.form);
				Helpers.clearFormErrors(this.form);
				// Alert user to success
				this.$router.app.$emit('snackbar', {
					text: 'Vehicle saved!'
				});
				// Notify parent component
				this.$emit('saved', id);
			},
			failed (errors){
				Helpers.populateFormErrors(this.form, errors);
			}
		},

		created (){
			if(this.customer){
				this.form.customer_id.value = this.customer;
			} else {
				this.$store.dispatch('allCustomers');
			}
		}
	}
</script>
