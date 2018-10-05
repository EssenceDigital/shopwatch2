<template v-if="wo">
	<v-card flat>
		<v-container fluid class="pa-0">
			<v-card-title>
				<v-icon>event</v-icon>
				<span class="ml-2">{{ wo.date | date }}</span>
			</v-card-title>
			<v-divider></v-divider>
			<v-card-title>
				<div>
					<v-icon class="mr-2">drive_eta</v-icon>
					<span><strong>{{ wo.vehicle.year + ' ' + wo.vehicle.make + ' ' + wo.vehicle.model }}</strong></span>
					<br>
					<v-icon class="mr-2">account_box</v-icon>
					<span>{{ wo.customer.first + ' ' + wo.customer.last }}</span>									
				</div>				
			</v-card-title>	
			<v-divider></v-divider>
			<v-card-text>
				<div 
					v-if="wo.jobs.length > 0" 
					v-for="job in wo.jobs" 
					:key="job.id" 
					class="pt-2 pb-2"
				>
					<span class="grey--text"><strong>{{ job.title }}</strong></span><br>
					<span v-if="job.description" class="grey--text">{{ job.description }}</span><br v-if="job.description">
					<span><strong>{{ job.hours }} hrs</strong></span>
					<span v-if="job.is_complete"><v-icon class="body-2" color="success">check_circle</v-icon></span>
				</div>
				<div v-if="wo.jobs.length == 0">
					<!-- If no jobs yet show alert -->
					<v-container fluid class="pa-0">
						<v-alert outline color="info" icon="info" value="true">
							No jobs yet
						</v-alert>
					</v-container>					
				</div>
			</v-card-text>
			<v-divider></v-divider>
    	<v-card-actions>
        <v-spacer></v-spacer>
				<v-tooltip left>
	        <v-btn icon slot="activator" @click="viewWo(wo.id)">
	          <v-icon>more</v-icon>
	        </v-btn>				
		      <span>View WO</span>
		    </v-tooltip>        

      </v-card-actions>		
		</v-container>

	</v-card>
</template>

<script>
	export default{
		props: ['wo'],

		methods: {
			viewWo (id){
				this.$router.push('/work-orders/' + id);
			}
		},

		mounted (){
			console.log(this.wo);
		}
	}
</script>