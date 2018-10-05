<template>
 	<v-card class="elevation-0">
    <!-- Title -->
    <v-card-title>
      Customers
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
      :items="customers"
      :search="search"
    >
      <template slot="items" slot-scope="props">
        <tr>
          <td class="text-xs-left">{{ props.item.first }}</td>
          <td class="text-xs-left">{{ props.item.last }}</td>
          <td class="text-xs-left">{{ props.item.phone_one }}</td>
          <td class="text-xs-left">
            <span v-if="props.item.phone_two">
              {{ props.item.phone_two }}
            </span>
            <span v-else>
              <em>N/A</em>
            </span>
          </td>
          <td class="text-xs-right">
            <v-tooltip left>
              <v-btn
                slot="activator"
                @click="$router.push('/customers/' + props.item.id + '/account')"
                dark
                icon
              >
                <v-icon color="teal" >more</v-icon>
              </v-btn>
              <span>View account</span>
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
  import CustomerForm from './../forms/Customer-form';
  import VehicleForm from './../forms/Vehicle-form';
  import VehiclesTable from './../tables/Vehicles-table';

	export default{
		data (){
			return {
				search: '',
				loading: true,
 				headers: [
          {
            text: 'First Name',
            align: 'left',
            sortable: true,
            value: 'first'
          },
          {
            text: 'Last Name',
            align: 'left',
            sortable: true,
            value: 'last'
          },
          {
            text: 'Primary Phone',
            align: 'left',
            sortable: true,
            value: 'phone_one'
          },
          {
            text: 'Secondary Phone',
            align: 'left',
            sortable: true,
            value: 'phone_two'
          },
          {
            text: '',
            align: 'right',
            sortable: false,
          }
        ],
        editDialog: false,
        addVehicleDialog: false,
        viewVehiclesDialog: false

			}
		},

		computed: {
			customers (){
				return this.$store.getters.customers;
			},

      selectedCustomer (){
        return this.$store.getters.selectedCustomer;
      }
		},

    components: {
      'customer-form': CustomerForm,
      'vehicle-form': VehicleForm,
      'vehicles-table': VehiclesTable
    },

    methods: {
      openDialog (customer, dialog){
        // The the customer for editing
        this.$store.commit('updateSelectedCustomer', customer);
        // Toggle Dialog
        this[dialog] = true;
      },

      closeDialog (dialog){
        // Toggle Dialog
        this[dialog] = false;
      },

      viewCustomerWorkOrders (id){
        // Update work orders filter with supplied ID
        this.$store.commit('updateWorkOrderCustomerIdFilter', id);
        // Forward to work orders table
        this.$router.push('/work-orders')
      },

      viewCustomerInvoices (id){
        // Update work orders filter with supplied ID
        this.$store.commit('updateInvoiceCustomerIdFilter', id);
        // Forward to work orders table
        this.$router.push('/invoices')
      },

    },

		created (){
			this.$store.dispatch('allCustomers')
				.then((response) => {
					// Toggle table loader
					this.loading = false;
				});
		}
	}
</script>
