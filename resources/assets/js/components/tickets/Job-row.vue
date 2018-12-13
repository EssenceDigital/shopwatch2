<template v-if="job">
	<v-container fluid>
		<v-layout row>
			<!-- Show for all states -->
			<v-flex xs8>
				<p class="pl-2 pt-1 mb-0">
					<!-- Only show spacer if NOT invoice state -->
					<span v-if="!invoiceState">
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
					<!-- Job title -->
					<strong>{{ job.title }}</strong>
				</p>
				<p class="pt-0 pl-3">
					{{ job.description }}
				</p>
			</v-flex>
			<!-- Only show spacer if NOT invoice state -->
			<v-spacer v-if="!invoiceState"></v-spacer>
			<!-- Show for all states -->
			<v-flex xs1 class="text-xs-right">
				<p class="pt-2 mb-0">
					{{ job.hours }}
				</p>
			</v-flex>
			<!-- Only show spacer if NOT invoice state -->
			<v-flex v-if="!invoiceState" xs1 class="text-xs-right">
				<!-- Job tool menu -->
				<v-menu bottom left>
		      <v-btn icon slot="activator" class="mt-0 mr-0">
		        <v-icon>settings</v-icon>
		      </v-btn>
		      <v-list>
		        <v-list-tile @click="editJobDialog = true">
		          <v-list-tile-title>Edit</v-list-tile-title>
		        </v-list-tile>
		        <v-list-tile @click="addPartsDialog = true">
		          <v-list-tile-title>Parts</v-list-tile-title>
		        </v-list-tile>
		      </v-list>
		    </v-menu>
			</v-flex>
			<!-- Show only for invoice state -->
			<v-flex v-if="invoiceState" xs1 class="text-xs-right">
				<p class="pa-2 mb-0">
					{{ job.shop_rate | money }}
				</p>
			</v-flex>
			<!-- Show only for invoice state -->
			<v-flex v-if="invoiceState" xs2 class="text-xs-right">
				<p class="pa-2 mb-0">
					{{ job.job_labour_total | money }}
				</p>
			</v-flex>

			<!-- Edit job dialog -->
			<v-dialog v-model="editJobDialog" persistent max-width="500px">

	    	<job-form action="updateJob" :job="job" edit-state="true" @saved="editJobDialog = false"></job-form>

	    </v-dialog>

			<!-- Add parts dialog -->
			<v-dialog v-model="addPartsDialog" persistent max-width="500px">
	    	<parts-form
					action="createJobPart"
					:job="job.id"
					@saved="addPartsDialog = false"
					@close="addPartsDialog = false"
				></parts-form>
	    </v-dialog>
		</v-layout>

		<!-- Job parts -->
		<v-layout row class="mb-2">
			<v-flex xs12>
				<part-row v-for="part in job.parts" :part="part" :key="part.id"></part-row>
			</v-flex>
		</v-layout>

		<v-divider></v-divider>
	</v-container>
</template>

<script>
	import JobForm from './../forms/Job-form';
	import PartsForm from './../forms/Job-parts-form';
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
				markingComplete: false
			}
		},

		components: {
			'job-form': JobForm,
			'parts-form': PartsForm,
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
			}
		}
	}
</script>