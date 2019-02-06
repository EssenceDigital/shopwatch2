<template>
	<v-form>
		<v-layout row>
			<v-flex xs10>
				<!-- Supplier -->
				<v-select
	        :items="suppliersSelect"
	        v-model="form.supplier.value"
	        :error-messages="form.supplier.errors"
	        label="Supplier"
	        single-line
	        menu-props="bottom"
    		></v-select>
			</v-flex>
			<v-flex xs2 class="text-xs-right">
				<v-tooltip top>
			    <v-btn icon slot="activator" class="ml-4 mt-3" @click="addSupplierDialog = true">
			      <v-icon color="green">add_circle_outline</v-icon>
			    </v-btn>
		      <span>Add supplier</span>
		    </v-tooltip>
			</v-flex>
		</v-layout>

		<!-- Title -->
		<v-text-field
      label="Title"
 			v-model="form.title.value"
 			:error-messages="form.title.errors"
    ></v-text-field>
		<!-- Part invoice number -->
		<v-text-field
      label="Part number"
 			v-model="form.part_number.value"
 			:error-messages="form.part_number.errors"
    ></v-text-field>
		<v-layout row class="mt-3">
			<!-- Quantity -->
	    <v-flex xs3>
				<v-text-field
					type="number"
					min="1"
					step="1"
		      label="Quantity"
		 			v-model="form.quantity.value"
		 			:error-messages="form.quantity.errors"
		    ></v-text-field>
	    </v-flex>
		</v-layout>
		<!-- Billing price -->
		<v-text-field
      label="Billing price"
 			v-model="form.billing_price.value"
 			:error-messages="form.billing_price.errors"
    ></v-text-field>
		<!-- Cost -->
		<v-text-field
      label="Cost"
 			v-model="form.total_cost.value"
 			:error-messages="form.total_cost.errors"
    ></v-text-field>

		<v-divider></v-divider>
		<v-layout row>
			<v-spacer></v-spacer>
			<v-btn
				@click="addPart"
				dark
				color="teal"
			>
				Add
			</v-btn>
		</v-layout>

		<!-- Add supplier dialog -->
		<v-dialog v-model="addSupplierDialog" persistent max-width="500px">

    	<supplier-form
				action="createSupplier"
				@saved="addSupplierDialog = false"
			></supplier-form>

    </v-dialog>
	</v-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';
	import SupplierForm from './Supplier-form';

	export default{
		props: ['part', 'job'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					job_id: {value: '', errors: []},
					title: {value: '', errors: []},
					part_number: {value: '', errors: []},
					supplier: {value: '', errors: []},
					quantity: {value: 1, errors: []},
					total_cost: {value: '', errors: []},
					billing_price: {value: '', errors: []}
				},
				addSupplierDialog: false
			}
		},

		computed: {
			suppliersSelect (){
				return this.$store.getters.suppliersSelect;
			}
		},

		watch: {
			part (part){
				// Populate the form for editing
				if(part){
					Helpers.populateForm(this.form, part);
				}
			},

			job (id){
				// Set job ID
				if(id){
					this.form.job_id.value = id;
				}
			}
		},

		components: {
			'base-form': BaseForm,
			'supplier-form': SupplierForm
		},

		methods: {

			addPart (){
				// Validate form
				for(let field in this.form){
					if(field != 'id'){
						if(this.form[field].value == ''){
							this.form[field].errors.push('Field is required.');
							return false;
						}
					}
				}

				// Construct part object
				var part = {};
				for(let field in this.form){
					part[field] = this.form[field].value;
				}

				this.$emit('part-added', part);
			},

			saved (){
				if(! this.editState){
					// Clear form
					Helpers.clearForm(this.form, 'job_id', { quantity: 1 });
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
			// Populate form with supplied part, if needed
			if(this.part){
				Helpers.populateForm(this.form, this.part);
			}
			// Set job ID
			if(this.job){
				this.form.job_id.value = this.job;
			}
		}
	}
</script>
