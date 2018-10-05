<template>
 	<v-card class="elevation-0">
    <v-container fluid>
      <v-layout row>
        <v-flex xs3 class="pl-3 pr-3">
          <v-menu
            lazy
            :close-on-content-click="true"
            v-model="fromDateMenu"
            transition="scale-transition"
            offset-y
            full-width
            :nudge-right="40"
            max-width="290px"
            min-width="290px"
          >
            <v-text-field
              slot="activator"
              label=" From date..."
              @input="updateFromDateFilter"
              :value="workOrdersFilter.from_date"
              prepend-icon="event"
              readonly
              clearable
            ></v-text-field>
            <v-date-picker @input="updateFromDateFilter" :value="workOrdersFilter.from_date" no-title scrollable autosave></v-date-picker>
          </v-menu>
        </v-flex>
        <v-flex xs3 class="pl-3 pr-3">
          <v-menu
            lazy
            :close-on-content-click="true"
            v-model="toDateMenu"
            transition="scale-transition"
            offset-y
            full-width
            :nudge-right="40"
            max-width="290px"
            min-width="290px"
          >
            <v-text-field
              slot="activator"
              label=" To date..."
              @input="updateToDateFilter"
              :value="workOrdersFilter.to_date"
              prepend-icon="event"
              readonly
              clearable
            ></v-text-field>
            <v-date-picker @input="updateToDateFilter" :value="workOrdersFilter.to_date" no-title scrollable autosave></v-date-picker>
          </v-menu>
        </v-flex>

        <v-flex xs3 class="pl-3 pr-3">
          <v-select
            :items="isInvoicedOptions"
            @input="updateIsInvoicedFilter"
            :value="workOrdersFilter.is_invoiced"
            label="Invoice status..."
            single-line
            bottom
            clearable
          ></v-select>          
        </v-flex>

        <v-flex xs3>
          <v-select
            :items="customersSelect"
              @input="updateCustomerFilter"
              :value="workOrdersFilter.customer_id"
            label="Customer..."
            autocomplete
            clearable
          ></v-select>          
        </v-flex>
                
      </v-layout>
      <v-layout row>
        <v-spacer></v-spacer>
        <v-tooltip left>
          <v-btn @click="filter" :loading="isFiltering" color="primary" slot="activator">
            <v-icon left>pageview</v-icon> Filter
          </v-btn>          
          <span>Filter customers</span>
        </v-tooltip>
      </v-layout>
      <v-divider class="mt-3"></v-divider>
    </v-container>

    <!-- Title -->
    <v-card-title>
      Work Orders
      <v-spacer></v-spacer>
      <v-text-field
        append-icon="search"
        label="Search"
        single-line
        hide-details
        v-model="search"
      ></v-text-field>
    </v-card-title>

    <!-- Table -->
    <v-data-table
      :headers="headers"
      :items="workOrders"
      :search="search"
      :loading="dataLoading"
    >
      <template slot="items" slot-scope="props">
        <tr>
          <td class="text-xs-left">{{ props.item.id }}</td>
          <td class="text-xs-left">{{ props.item.date | date}}</td>
          <td class="text-xs-left">{{ props.item.customer.first }}</td>
          <td class="text-xs-left">{{ props.item.customer.last }}</td>
          <td class="text-xs-left">{{ props.item.vehicle.year }}</td>
          <td class="text-xs-left">{{ props.item.vehicle.make }}</td>
          <td class="text-xs-left">{{ props.item.vehicle.model }}</td>
          <td>
            <span v-if="props.item.is_invoiced" class="success--text">Yes</span>
            <span v-else class="error--text">No</span>
          </td>
          <td>{{ props.item.jobs.length }}</td>
          <td class="text-xs-right">          
            <v-tooltip left v-if="props.item.is_invoiced">
              <v-btn icon slot="activator" @click="$router.push('/invoices/'+props.item.invoice_id)">
                <v-icon>payment</v-icon>
              </v-btn>   
              <span>View invoice</span>           
            </v-tooltip>
            <v-tooltip left>
              <v-btn icon slot="activator" @click="$router.push('/work-orders/'+props.item.id)">
                <v-icon>launch</v-icon>
              </v-btn>   
              <span>View WO</span>           
            </v-tooltip>         
          </td>
        </tr>
      </template>      
      <template slot="pageText" slot-scope="{ pageStart, pageStop }">
        From {{ pageStart }} to {{ pageStop }}
      </template>
    </v-data-table>
  </v-card>		
</template>

<script>

	export default{
		data (){
			return {
				search: '',
				dataLoading: true,
        toDateMenu: false,
        fromDateMenu: false,  
        isInvoicedOptions: [
          { text: 'Invoiced', value: 1  },
          { text: 'Open', value: 'false-bool'  }            
        ], 
        isFiltering: false,             
 				headers: [
          {
            text: '#',
            align: 'left',
            sortable: true,
            value: 'customer.last'
          }, 
          {
            text: 'Date',
            align: 'left',
            sortable: true,
            value: 'date'
          },                 
          {
            text: 'First',
            align: 'left',
            sortable: true,
            value: 'customer.first'
          },          
          {
            text: 'Last',
            align: 'left',
            sortable: true,
            value: 'customer.last'
          },        
          {
            text: 'Year',
            align: 'left',
            sortable: true,
            value: 'vehicle.year'
          },
          {
            text: 'Make',
            align: 'left',
            sortable: true,
            value: 'vehicle.make'
          },          
          {
            text: 'Model',
            align: 'left',
            sortable: true,
            value: 'vehicle.model'
          }, 
          {
            text: 'Invoiced?',
            align: 'left',
            sortable: true,
            value: 'is_invoiced'
          },
          {
            text: 'Jobs',
            align: 'left'
          },
          {
            text: '',
            align: 'right'
          }                                                                     
        ],
        editDialog: false,
        addVehicleDialog: false,
        viewVehiclesDialog: false,
			}
		},

		computed: {
			workOrders (){
				return this.$store.getters.workOrders;
			},

      workOrdersFilter (){
        return this.$store.getters.workOrdersFilter;
      },

      customersSelect (){
        return this.$store.getters.customersSelect;
      }      
		},

    components: {

    },

    methods: {
      updateFromDateFilter (value){
        return this.$store.commit('updateWorkOrderFromDateFilter', value);
      },
      updateToDateFilter (value){
        return this.$store.commit('updateWorkOrderToDateFilter', value);
      },
      updateIsInvoicedFilter (value){
        return this.$store.commit('updateWorkOrderIsInvoicedFilter', value);
      },
      updateCustomerFilter (value){
        return this.$store.commit('updateWorkOrderCustomerIdFilter', value);
      },

      filter (){
        // Toggle loader
        this.isFiltering = true;
        // Dispatch action
        this.$store.dispatch('filterWorkOrders', this.workOrdersFilter)
          .then(() => {
            // Toggle loader
            this.isFiltering = false;
          });
      }   
    },

		created (){
			this.$store.dispatch('filterWorkOrders', this.workOrdersFilter)
				.then((response) => {
					// Toggle table loader
					this.dataLoading = false;
				});

      // Get customers (For filter)
      this.$store.dispatch('filterCustomers');
		}		
	}
</script>