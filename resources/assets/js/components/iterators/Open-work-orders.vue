<template>
  <v-container fluid grid-list-md class="elevation-2 grey lighten-4">
    <v-layout row class="mb-4">
      <v-flex xs6>
        <h3 class="font-weight-thin ml-2">
          <v-icon>assignment</v-icon>
          Open Work Orders
        </h3>
      </v-flex>
      <v-spacer></v-spacer>
    </v-layout>
    <v-data-iterator
      :loading="componentLoading"
      :items="workOrders"
      :rows-per-page-items="rowsPerPageItems"
      :pagination.sync="pagination"
      content-tag="v-layout"
      row
      wrap
    >
      <v-flex
        slot="item"
        slot-scope="props"
        xs12
        sm6
        md3
        lg4
      >
        <v-card class="mb-4">
          <v-card-title>
            <h4>WRK-{{ props.item.id }}</h4>
            <v-spacer></v-spacer>
            <v-tooltip left>
              <v-btn
                @click="viewWo(props.item.id)"
                slot="activator"
                icon
                class="mr-0"
                >
                <v-icon color="teal">more</v-icon>
              </v-btn>
              <span>View work order</span>
            </v-tooltip>
          </v-card-title>
          <v-divider></v-divider>
          <v-list dense>
            <v-list-tile>
              <v-list-tile-content>Customer:</v-list-tile-content>
              <span class="text-xs-right grey--text lighten-1 font-weight-black">{{ props.item.customer.first + ' ' + props.item.customer.last }}</span>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Date:</v-list-tile-content>
              <span class="text-xs-right grey--text lighten-1 font-weight-black">{{ props.item.created_at }}</span>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Vehicle:</v-list-tile-content>
              <span class="text-xs-right grey--text lighten-1 font-weight-black">
                {{ props.item.vehicle.year + ' ' + props.item.vehicle.make + ' ' + props.item.vehicle.model }}
              </span>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>VIN:</v-list-tile-content>
              <span class="text-xs-right grey--text lighten-1 font-weight-black">
                {{ props.item.vehicle.vin }}
              </span>
            </v-list-tile>
          </v-list>
          <v-divider></v-divider>
          <v-card-text>
            <div
    					v-if="props.item.jobs.length > 0"
    					v-for="job in props.item.jobs"
    					:key="job.id"
    					class="pt-2 pb-2"
    				>
    					<span class="grey--text"><strong>{{ job.title }}</strong></span><br>
    					<span v-if="job.description" class="grey--text">{{ job.description }}</span><br v-if="job.description">
    					<span><strong>{{ job.hours }} hrs</strong></span>
    					<span v-if="job.is_complete"><v-icon class="body-2" color="success">check_circle</v-icon></span>
    				</div>
    				<div v-if="props.item.jobs.length == 0">
    					<!-- If no jobs yet show alert -->
    					<v-container fluid class="pa-0">
    						<v-alert outline color="info" icon="info" value="true">
    							No jobs yet
    						</v-alert>
    					</v-container>
    				</div>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-data-iterator>

  </v-container>
</template>

<script>

  export default {
    props: ['filter'],

    computed: {
      workOrders () {
        return this.$store.getters.workOrders;
      }
    },

    data: () => ({
      rowsPerPageItems: [3, 8, 12],
      pagination: {
        rowsPerPage: 3
      },
      componentLoading: true
    }),

    components: {

    },

    methods: {
			viewWo (id){
				this.$router.push('/work-orders/' + id);
			}
		},

    created (){

      if(!this.filter){
        var filt = {
  				created_at: '',
  				is_invoiced: 'false-bool'
  			};
      } else {
        var filt = this.filter;
      }
			// Get WOs
			this.$store.dispatch('filterWorkOrders', filt)
				.then(() => {
					// Toggle component state
					this.componentLoading = false;
				});
		}
  }
</script>
