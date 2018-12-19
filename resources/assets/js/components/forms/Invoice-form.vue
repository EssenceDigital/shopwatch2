<template>
	<base-form
		:title="title"
		:action="action"
		:edit-state="editState"
		:hide-save-button="hideSaveButton"
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

	export default{

    methods: {
      createInvoice (){
				// Toggle loader
				this.invoiceCreating = true;
				// Dispatch action
				this.$store.dispatch('createInvoice', {
					work_order_id: this.id
				})
					.then((response) => {
						// Toggle loader
						this.invoiceCreating = false;
						// Toggle dialog
						this.confirmInvoiceDialog = false;
						// Redirect
						this.$router.push('/invoices/' + response.id);
					})
					.catch((error) => {
						this.invoiceCreating = false;
						// Toggle dialog
						this.confirmInvoiceDialog = false;
						// Error response
		  			if(error.response){
		    			// Form validation errors
		    			if(error.response.data){
								// Check for error response from Laravel controller
								if(error.response.data.result == 'error'){
									this.$router.app.$emit('snackbar', {
										text: error.response.data.message
									});
								}
							}
						}
					});
			},
    }
	}
</script>
