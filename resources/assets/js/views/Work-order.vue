<template v-if="id">
	<v-container fluid class="pa-0">
		<!-- Data loaded... -->
		<div v-if="!componentLoading">
			<layout v-if="workOrder">
				<div slot="title">Work Order</div>

				<div v-if="!workOrder.is_invoiced" slot="tools">
					<v-tooltip top>
				    <v-btn color="primary" left slot="activator" @click="woOptionsDialog = true">
				      <v-icon left>info</v-icon> Options
				    </v-btn>
			      <span>Options</span>
			    </v-tooltip>
					<v-tooltip top>
				    <v-btn color="primary" left slot="activator" @click="addJobDialog = true">
				      <v-icon left>add_circle_outline</v-icon> Job
				    </v-btn>
			      <span>Add job</span>
			    </v-tooltip>
					<v-tooltip top>
				    <v-btn color="teal" dark left slot="activator" @click="confirmInvoiceDialog = true">
				      <v-icon left>credit_card</v-icon> Invoice
				    </v-btn>
			      <span>Create Invoice</span>
			    </v-tooltip>
				</div>

				<div slot="content">
					<v-card flat>
						<!-- Top overview container -->
						<v-container fluid>
							<!-- WO status row -->
							<v-layout row class="mb-3">
								<v-spacer></v-spacer>
								<router-link v-if="workOrder.is_invoiced" :to="'/invoices/' + workOrder.invoice_id" class="mt-1">
									<em class="success--text mr-1">
										Invoiced
									</em>
								</router-link>

								<v-tooltip v-if="workOrder.is_invoiced" top>
							    <v-icon color="success" slot="activator">check_circle</v-icon>
						      <span>WO has been invoiced</span>
						    </v-tooltip>
								<v-tooltip v-else top>
							    <v-icon color="info" slot="activator">info</v-icon>
						      <span>WO is open</span>
						    </v-tooltip>
							</v-layout>

							<!--Business headline -->
							<v-layout row>
								<v-flex xs6>
										<h1>UNA AUTO SERVICE</h1>
										<h4 class="ml-1">Repairs, detailing, maintenance</h4>
								</v-flex>
								<v-flex xs6 class="text-xs-right">
										<h1 class="grey--text">WORK ORDER</h1>
										<small class="grey--text">(#{{ workOrder.id }})</small>
								</v-flex>
							</v-layout>

							<v-divider class="mt-2 mb-2"></v-divider>

							<!--WO date -->
							<v-layout row>
								<v-flex xs4>
									<strong>WO DATE:</strong> {{ workOrder.date | date }}
								</v-flex>
							</v-layout>
						</v-container>

						<!-- Customer and vehicle container -->
						<v-container fluid class="pa-2">
							<!-- Customer and vehicle headings -->
							<v-layout row class="red darken-4 mt-2">
								<v-flex xs6>
									<h3 class="white--text pa-2">CUSTOMER</h3>
								</v-flex>
								<v-flex xs6>
									<h3 class="white--text pa-2">VEHICLE</h3>
								</v-flex>
							</v-layout>

							<v-layout row class="red lighten-4 pt-2">
								<!-- Customer info -->
								<v-flex xs6>
									<v-container fluid>
										<v-layout row>
											<v-flex xs4>
												<strong>NAME:</strong>
											</v-flex>
											<v-flex xs6>
												{{ workOrder.customer.first }} {{ workOrder.customer.last }}
											</v-flex>
										</v-layout>
										<v-layout row>
											<v-flex xs4>
												<strong>PRIMARY:</strong>
											</v-flex>
											<v-flex xs6>
												{{ workOrder.customer.phone_one }}
											</v-flex>
										</v-layout>
										<v-layout row v-if="workOrder.customer.phone_two">
											<v-flex xs4>
												<strong>SECONDARY:</strong>
											</v-flex>
											<v-flex xs6>
												{{ workOrder.customer.phone_two }}
											</v-flex>
										</v-layout>
									</v-container>
								</v-flex>
								<!-- Vehicle info -->
								<v-flex xs6>
									<v-container fluid>
										<v-layout row>
											<v-flex xs4>
												<strong>YEAR:</strong>
											</v-flex>
											<v-flex xs6>
												{{ workOrder.vehicle.year }}
											</v-flex>
										</v-layout>
										<v-layout row>
											<v-flex xs4>
												<strong>MAKE:</strong>
											</v-flex>
											<v-flex xs6>
												{{ workOrder.vehicle.make }}
											</v-flex>
										</v-layout>
										<v-layout row>
											<v-flex xs4>
												<strong>MODEL:</strong>
											</v-flex>
											<v-flex xs6>
												{{ workOrder.vehicle.model }}
											</v-flex>
										</v-layout>
										<v-layout row>
											<v-flex xs4>
												<strong>VIN:</strong>
											</v-flex>
											<v-flex xs6>
												{{ workOrder.vehicle.vin }}
											</v-flex>
										</v-layout>
									</v-container>
								</v-flex>
							</v-layout>
						</v-container>

						<!-- Jobs heading container -->
						<v-container fluid class="pa-2">
							<!-- Job headings -->
							<v-layout v-if="workOrder.jobs.length > 0" row class="red darken-4 mt-2">
								<v-flex xs8>
									<h3 class="white--text pa-2">JOB REQUESTED</h3>
								</v-flex>
								<v-spacer></v-spacer>
								<v-flex xs1 class="text-xs-right">
									<h3 class="white--text pa-2">HOURS</h3>
								</v-flex>
								<v-flex xs1></v-flex>
							</v-layout>

							<!-- If no jobs yet show alert -->
							<v-container v-else fluid class="pa-0 mt-5">
								<v-alert outline color="info" icon="info" value="true">
									No jobs have been added to this work order yet
								</v-alert>
							</v-container>

							<!-- All jobs -->
							<job-row v-for="job in workOrder.jobs" :job="job" :key="job.id"></job-row>
						</v-container>

						<v-container fluid class="mt-5">
							<v-layout row>
								<v-flex xs12 class="text-xs-center">
									<small>
										This is only a work order.
									</small>
								</v-flex>
							</v-layout>
						</v-container>

					</v-card>


					<!-- Dialogs triggered by tool buttons -->

					<!-- Add job dialog -->
					<v-dialog v-model="addJobDialog" persistent max-width="500px">
		      	<job-form
							action="createJob"
							:work-order="workOrder.id"
							@saved="addJobDialog = false"
							@close="addJobDialog = false"
						></job-form>
		      </v-dialog>

					<!-- WO options (edit) dialog -->
					<v-dialog v-model="woOptionsDialog" persistent max-width="500px">
	        	<wo-form
	        		:wo="workOrder"
	        		:edit-state="true"
							:hide-save-button="true"
	        		@saved="woOptionsDialog = false"
							@close="woOptionsDialog = false"
	        		@removed="removed"
	        	></wo-form>
		      </v-dialog>

			    <!-- Create invoice dialog -->
			    <v-dialog v-model="confirmInvoiceDialog" persistent max-width="300px">
			      <v-card>
			        <v-card-text class="text-xs-center">
			          Create invoice?
			        </v-card-text>
			        <v-card-actions>
			        	<v-spacer></v-spacer>
								<v-btn flat @click.native="confirmInvoiceDialog = false">Cancel</v-btn>
			          <v-btn color="green darken-1" flat :loading="invoiceCreating" @click.native="createInvoice">Yes</v-btn>
			          <v-spacer></v-spacer>
			        </v-card-actions>
			      </v-card>
			    </v-dialog>
				</div><!--/ Content slot -->
			</layout><!-- / Work order row -->

			<v-layout v-else row>
				<v-flex xs6 offset-xs3 class="text-xs-center">
					<v-alert outline color="error" icon="error" value="true">
							The requested work order was not found. It may have been deleted
					</v-alert>
					<v-btn flat @click="$router.push('/')">
						<v-icon left>replay</v-icon> Go to dashboard
					</v-btn>
				</v-flex>
			</v-layout>
		</div>
		<!-- Data loading -->
		<div v-else class="text-xs-center">
			<v-progress-circular indeterminate color="primary"></v-progress-circular>
		</div>

	</v-container>

</template>

<script>
	import Layout from './_Layout';
	import WoForm from './../components/forms/Work-order-form';
	import JobForm from './../components/forms/Job-form';
	import JobRow from './../components/tickets/Job-row';
	import PartRow from './../components/tickets/Part-row';

	export default{
		props: ['id'],

		data () {
			return {
				componentLoading: true,
				addJobDialog: false,
				removeWoDialog: false,
				woOptionsDialog: false,
				confirmInvoiceDialog: false,
				invoiceCreating: false
			}
		},

		computed: {
			workOrder (){
				return this.$store.getters.selectedWorkOrder;
			},

			parts (){
				let parts = [];

				this.workOrder.jobs.forEach((job) => {
					if(job.parts.length > 0){
							parts.push(job.parts);
					}
				});

				return parts;
			},

			estGrandTotal (){
				if(this.workOrder.jobs){
					let total = 0;

					this.workOrder.jobs.forEach((job) => {
						total += parseFloat(job.job_grand_total);
					});

					return total;
				}
			}
		},

		watch: {
			id (newId){
				// If ID prop does not match the selected work order then update selected work order
				// ID prop is final source of truth
				if(this.workOrder.id != newId) {
					// Component loading new data
					this.componentLoading = true;
					// Fetch data
					this.$store.dispatch('getWorkOrder', newId)
						.then(() => {
							// Toggle component loading
							this.componentLoading = false;
						});
				}
			}
		},

		components: {
			'layout': Layout,
			'job-form': JobForm,
			'job-row': JobRow,
			'part-row': PartRow,
			'wo-form': WoForm
		},

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

			removed (){
				console.log("removed");
				// Toggle dialog
				this.woOptionsDialog = false;
				// Redirect
				this.$router.push('/');
			}
		},

		created (){
			// If ID prop does not match the selected work order then update selected work order
			// ID prop is final source of truth
			if(! this.workOrder.id || this.workOrder.id != this.id) {
				this.$store.dispatch('getWorkOrder', this.id)
					.then(() => {
						// Toggle component loading
						this.componentLoading = false;
					});
			} else {
				// Toggle component loading
				this.componentLoading = false;
			}

			// Work order 'children' forms need some resources. Load them
			this.$store.dispatch('getSuppliers');
			this.$store.dispatch('getUsers');
		}
	}
</script>
