<template>
	<base-form
		:title="title"
		:action="action"
		:edit-state="editState"
		:hide-button="editState"
		remove-action="removeWorkOrder"
		:fields="form"
		@saved="saved"
		@removed="removed"
		@error="failed"
		@close="close"
	>
		<template v-if="!editState" slot="form-fields">

			<v-container fluid slot="form-fields">
	      <!-- Existing vehicle vin search -->
	      <h4 class="grey--text mb-2">Existing vehicle...</h4>
	      <v-layout row>
	        <!-- Vehicle select -->
	        <v-text-field
						v-model="searchingVehicleVin"
	          @input="searchVehicles"
	          box
	          :items="vehicles"
						:loading="searchingVehicles"
	          label="Start typing vehicle VIN..."
	          prepend-inner-icon="search"
	        ></v-text-field>
	      </v-layout>
				<!-- Show add new vehicle OR vehicle search results -->
				<v-container fluid class="pl-0" v-if="vehicles.length == 0">
					<v-layout row>
						<span class="font-weight-light mt-3 mr-3">-OR-</span>
						<v-btn color="teal" dark @click="addVehicleDialog = true">
							<v-icon left>add</v-icon> Add Vehicle
						</v-btn>
					</v-layout>
				</v-container>
				<!-- Vehicle search results -->
				<v-container fluid v-else class="pa-0">
					<h4 class="grey--text mb-2">Results...</h4>
					<v-layout row>
						<v-radio-group
							v-model="form.vehicle_id.value"
							:mandatory="false"
						>
				      <v-flex x12
								v-for="vehicle in vehicles"
								:key="vehicle.id"
								:vehicle="vehicle"
							>
								<v-layout row>
									<v-radio
										class="mb-0"
										:value="vehicle.id"
									></v-radio>
									<v-icon>drive_eta</v-icon>
									<span class="primary--text mr-2 ml-1">
										{{ vehicle.vin }}
									</span>
									<span class="font-weight-black  grey--text mr-1">
										{{ vehicle.year + ' ' + vehicle.make + ' ' + vehicle.model }}
									</span>
								</v-layout>

								<v-layout row class="mt-0">
									<small class="ml-5 font-weight-light">
										<v-icon>person</v-icon>
										{{ vehicle.customer.first + ' ' + vehicle.customer.last }}
									</small>
								</v-layout>
							</v-flex>
					   </v-radio-group>
					</v-layout>
				</v-container>
			</v-container>
			 <!-- Add vehicle dialog -->
			<v-dialog v-model="addVehicleDialog" persistent max-width="500px">
				<vehicle-form
					title="Add Vehicle"
					action="createVehicle"
					@saved="vehicleSaved"
					@close="addVehicleDialog = false"
				></vehicle-form>
      </v-dialog>
		</template>
	</base-form>
</template>

<script>
	import BaseForm from './_Base-form';
	import Helpers from './../../app/helpers';
	import api from './../../app/api';
	import VehicleForm from './Vehicle-form';

	export default{
		props: ['title', 'action', 'wo', 'editState'],

		data (){
			return {
				form: {
					id: {value: '', errors: []},
					vehicle_id: {value: '', errors: []}
				},
				customer: '',
				searchingVehicleVin: '',
				vehicles: [],
				searchingVehicles: false,
				addVehicleDialog: false
			}
		},

		computed: {
			vehiclesSelect (){
				return this.$store.getters.selectedCustomerVehiclesSelect;
			},
			selectedVehicle (){
				return this.$store.getters.selectedVehicle;
			}
		},

		watch: {
			wo (wo){
				// Populate the form for editing
				if(wo){
					Helpers.populateForm(this.form, wo);
				}
			}
		},


		components: {
			'base-form': BaseForm,
			'vehicle-form': VehicleForm
		},

		methods: {
			searchVehicles (v){
        if(v){
          if(v.length > 4){
						// Toggle loader
						this.searchingVehicles = true;
            // Poplate this.vehicles with results from API
            api.get('/vehicles/' + v + '/search')
              .then((response) => {
                console.log(response);
                this.vehicles = response;
								// Toggle loader
								this.searchingVehicles = false;
              })
              .catch((error) =>{
								// Toggle loader
								this.searchingVehicles = false;
                console.log(error);
              });
          }
        } else {
					this.searchingVehicleVin = '';
					this.vehicles = [];
					this.form.vehicle_id.value = '';
					// Toggle loader
					this.searchingVehicles = false;					
				}
      },
			vehicleSaved (id){
				if(this.selectedVehicle.id == id){
					// Push newly saved vehicle into vehicles array
					this.vehicles.push(this.selectedVehicle);
					// Add newly saved vehicle id to form
					this.form.vehicle_id.value = id;
					// Set the vin in the search
					this.searchingVehicleVin = this.selectedVehicle.vin;
					// Close dialog
					this.addVehicleDialog = false;
				}
			},
			close (){
				// Reset fields
				this.vehicles = [];
				this.searchingVehicleVin = '';
				this.form.vehicle_id.value ='';
				// Emit event
				this.$emit('close');
			},
			saved (id){
				// Clear form
				Helpers.clearForm(this.form);
				Helpers.clearFormErrors(this.form);
				// Notify parent component
				this.$emit('saved', id);
			},
			removed (){
				// Notify parent component
				this.$emit('removed');
			},
			failed (errors){
				Helpers.populateFormErrors(this.form, errors);
			}
		},

		created (){
			// Populate the form for editing
			if(this.wo){
				Helpers.populateForm(this.form, this.wo);
			}
		}
	}
</script>
