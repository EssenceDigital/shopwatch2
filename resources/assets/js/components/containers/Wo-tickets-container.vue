<template>
	<v-container fluid>
		<!-- Data loaded... -->
		<div v-if="!componentLoading">
			<!-- Row for headings -->
			<v-layout row>
				<v-flex xs12 class="pa-1">
					<span class="subheading"><strong>Open Work Orders</strong></span>
				</v-flex>
			</v-layout>
			<!-- Row for work order tickets -->
			<v-layout v-if="workOrders.length > 0" row wrap>
				<v-flex xs4 v-for="wo in workOrders" :key="wo.id" class="pl-2 pr-2 mt-4">
					<wo-ticket :wo="wo"></wo-ticket>
				</v-flex>
			</v-layout>
			<!-- No work orders alert -->
			<v-layout v-else row class="mt-3">
				<v-flex xs4>
					<v-alert outline color="info" icon="info" value="true">
						No open work orders
					</v-alert>
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
	import WoTicket from './../tickets/Wo-ticket';

	export default{
		data (){
			return {
				componentLoading: true
			}
		},

		computed: {
			workOrders (){
				return this.$store.getters.workOrders;
			}
		},

		components: {
			'wo-ticket': WoTicket
		},

		created (){
			// Get WOs
			this.$store.dispatch('filterWorkOrders', {
				created_at: '',
				is_invoiced: 'false-bool'
			})
				.then(() => {
					// Toggle component state
					this.componentLoading = false;
				});
		}
	}
</script>
