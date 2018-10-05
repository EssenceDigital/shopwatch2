<template>
	<base-form 
		:action="action" 
		remove-action="removeJobPart"
		:edit-state="editState"
		:fields="form" 
		@saved="saved"
		@error="failed"
	>
		<template slot="form-fields">
			<v-layout row>
				<v-flex xs10>
					<!-- Supplier -->
					<v-select
		        :items="suppliersSelect"
		        v-model="form.supplier_id.value"
		        :error-messages="form.supplier_id.errors"
		        label="Supplier"
		        single-line
		        bottom
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
	      label="Part invoice number"
	 			v-model="form.part_invoice_number.value"
	 			:error-messages="form.part_invoice_number.errors"
	    ></v-text-field>	
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

			<!-- Add supplier dialog -->
			<v-dialog v-model="addSupplierDialog" persistent max-width="500px">
	      <v-card>
				 	<v-system-bar window class="blue darken-4">
			      <v-spacer></v-spacer>
						<v-tooltip top>
				      <v-btn icon class="mr-0" slot="activator" @click="addSupplierDialog = false">
				      	<v-icon class="white--text mr-0">close</v-icon>
				      </v-btn>											
				      <span>Close dialog</span>
				    </v-tooltip>			      
			    </v-system-bar>
	        <v-card-text>
	        	<supplier-form action="createSupplier" @saved="addSupplierDialog = false"></supplier-form>
	        </v-card-text>
	      </v-card>
	    </v-dialog>		
	        			    	    	    	    		    		
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';	
	import SupplierForm from './Supplier-form';

	export default{
		props: ['action', 'part', 'job', 'editState'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					job_id: {value: '', errors: []},
					title: {value: '', errors: []},
					part_invoice_number: {value: '', errors: []},
					supplier_id: {value: '', errors: []},
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