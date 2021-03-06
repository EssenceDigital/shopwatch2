<template v-if="job">
	<v-container fluid class="full-border mt-4 pa-0">
		<!-- Job titles -->
		<v-layout row class="grey lighten-2 mt-0 pl-0 pr-0 mb-3">
			<!-- Job title and description -->
			<v-flex xs8>
				<h3 class="pt-2 pb-2 pr-2 pl-2 mb-0">JOB</h3>
			</v-flex>

			<v-spacer></v-spacer>
			<!-- Show for all states -->
			<v-flex
				v-if="!job.is_flat_rate"
				xs1 class="text-xs-right"
			>
				<h3 class="pt-2 pb-2 pr-0 pl-2 mb-0">HOURS</h3>
			</v-flex>
			<!-- Only show spacer if NOT invoice state -->
			<v-flex v-if="!invoiceState" xs1 class="text-xs-right">

			</v-flex>
			<!-- Show only for invoice state -->
			<v-flex v-if="invoiceState" xs1 class="text-xs-right">
				<h3 class="pt-2 pb-2 pr-2 pl-2 mb-0">RATE</h3>
			</v-flex>
		</v-layout>

		<!-- Job Overview -->
		<v-layout row>
			<!-- Show for all states, Job status -->
			<v-flex xs1 v-if="!invoiceState">
				<p class="pl-1 pt-1 mb-0">
					<span>
						<v-tooltip top v-if="job.is_complete" class="ml-0">
				  		<v-btn
				  			icon
				  			slot="activator"
				  			class="body-2 ml-0 mt-0"
				  			@click="markComplete(false)"
				  			:loading="markingComplete"
				  		>
								<v-icon color="green">check_circle</v-icon>
				  		</v-btn>
				      <span>Job complete</span>
				    </v-tooltip>
						<v-tooltip top v-if="!job.is_complete" class="ml-0">
				  		<v-btn
				  			icon
				  			slot="activator"
				  			class="body-2 ml-0 mt-0"
				  			@click="markComplete(true)"
				  			:loading="markingComplete"
				  		>
								<v-icon color="primary">feedback</v-icon>
				  		</v-btn>
				      <span>Job pending</span>
				    </v-tooltip>
					</span>
				</p>
			</v-flex>
			<!-- Job title and description -->
			<v-flex xs7 class="pl-2">
				<strong>{{ job.title }}</strong>
				<p class="pt-0">
					{{ job.description }}
				</p>
			</v-flex>
			<!-- Only show spacer if NOT invoice state -->
			<v-spacer></v-spacer>

			<!-- Show for all states -->
			<v-flex
				v-if="!job.is_flat_rate"
				xs1
				class="text-xs-right"
			>
				<p class="pt-2 pr-2 mb-0">
					{{ job.hours }}
				</p>
			</v-flex>
			<!-- Show only for invoice state -->
			<v-flex v-if="invoiceState" xs1 class="text-xs-right">
				<p class="pt-2 pb-2 pr-2 mb-0">
					{{ job.shop_rate | money }}
				</p>
			</v-flex>
			<!-- Only show spacer if NOT invoice state -->
			<v-flex v-if="!invoiceState" xs1 class="text-xs-right">
				<!-- Job tool menu -->
				<v-tooltip top>
					<v-btn
						slot="activator"
						@click="editJobDialog = true"
						icon
						class="mt-0"
					>
						<v-icon>edit</v-icon>
					</v-btn>
					<span>Edit job and parts</span>
				</v-tooltip>
			</v-flex>

			<!-- Dialogs triggered by tool buttons -->
			<v-dialog v-model="editJobDialog" fullscreen hide-overlay transition="dialog-bottom-transition">
				<v-card>
					<v-toolbar dark color="primary">
						<v-btn icon dark @click="editJobDialog = false">
							<v-icon>close</v-icon>
						</v-btn>
						<v-toolbar-title>Add Work</v-toolbar-title>
						<v-spacer></v-spacer>
						<v-toolbar-items>
							<v-btn
								@click="triggerSaveJob"
								:loading="savingJob"
								dark
								flat
							>Save</v-btn>
						</v-toolbar-items>
					</v-toolbar>
					<v-container fluid>
							<job-form
								action="updateJob"
								:save-form="saveJob"
								:job="job"
								edit-state="true"
								@saved="jobSaved"
								@close="addJobDialog = false"
							></job-form>
					</v-container>
				</v-card>
			 </v-dialog>

		</v-layout>

		<!-- Job parts -->
		<v-layout
			v-if="job.parts.length != 0 && !job.is_premade"
			row
			class="mb-3"
		>
			<v-flex xs12>
				<!-- Part headings -->
				<v-layout row class="grey lighten-4 mt-2 caption">
					<v-flex xs3>
						<p class="pa-2 pl-3 mb-0">
							<strong>PART #</strong>
						</p>
					</v-flex>
					<v-flex xs7>
						<p class="pt-2 pb-2 mb-0">
							<strong>PART</strong>
						</p>
					</v-flex>
					<v-spacer></v-spacer>
					<v-flex xs1>
						<p class="pt-2 pb-2 mb-0">
							<strong>QUANTITY</strong>
						</p>
					</v-flex>
					<v-flex xs2>
						<p class="pt-2 pb-2 mb-0 text-xs-center">
							<strong>EA.</strong>
						</p>
					</v-flex>
					<v-flex xs2>
						<p class="pt-2 pb-2 pr-2 mb-0 text-xs-right">
							<strong>LINE TOTAL</strong>
						</p>
					</v-flex>
				</v-layout>
				<part-row
					v-for="part in job.parts"
					:part="part"
					:key="part.id"
					:invoice-state="invoiceState"
				></part-row>
			</v-flex>
		</v-layout>
		<v-divider></v-divider>
		<v-layout row>
			<v-spacer></v-spacer>
			<v-flex
				v-if="!job.is_flat_rate && !job.is_premade"
				xs3
				class="text-xs-right"
			>
				<p class="pa-2 mb-0 caption grey--text">
					<v-icon small left>build</v-icon>
					<strong>LABOUR:</strong> {{ job.job_labour_total | money }}
				</p>
			</v-flex>
			<v-flex
				v-if="!job.is_flat_rate && !job.is_premade"
				xs3
				class="text-xs-right"
			>
				<p class="pa-2 mb-0 caption grey--text">
					<v-icon small left>settings</v-icon>
					<strong>PARTS:</strong> {{ job.parts_total_billed | money }}
				</p>
			</v-flex>
			<v-flex xs3 class="text-xs-right">
				<p class="pa-2 mb-0 caption grey--text">
					<v-icon small left>shopping_cart</v-icon>
					<strong>SUB TOTAL:</strong> {{ job.job_grand_total | money }}
				</p>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
	import JobForm from './../forms/Job-form';
	import PartRow from './../tickets/Part-row';

	export default{
		props: {
			job: {
				required: true
			},
			invoiceState: {
				default: false
			}
		},

		data (){
			return {
				editJobDialog: false,
				addPartsDialog: false,
				markingComplete: false,
				savingJob: false,
				saveJob: false,
				addPart: false
			}
		},

		components: {
			'job-form': JobForm,
			'part-row': PartRow
		},

		methods: {
			markComplete (is_complete){
				// Toggle loader
				this.markingComplete = true;
				// Dispatch action
				this.$store.dispatch('markJobComplete', {
					id: this.job.id,
					is_complete: is_complete
				})
					.then(() => {
						// Toggle loader
						this.markingComplete = false;
					})
					.catch((error) => {
						// Toggle loader
						this.markingComplete = false;
						// Handle errors
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

			triggerSaveJob (){
				// Trigger loader
				this.savingJob = true;
				// Trigger save
				this.saveJob = true;
			},

			jobSaved (){
				// Reset trigger Boolean
				this.savingJob = false;
				this.saveJob = false;
				this.editJobDialog = false;
			},
		}
	}
</script>
