<template v-if="vehicles">
 	<v-card class="elevation-2 grey lighten-4">
    <!-- Title -->
    <v-card-title>
      <h3 class="font-weight-thin ml-2">
        <v-icon>drive_eta</v-icon>
        Vehicles
      </h3>
      <v-spacer></v-spacer>
    </v-card-title>

    <!-- Table -->
    <v-data-table
      :headers="headers"
      :items="vehicles"
    >
      <template slot="items" slot-scope="props">
        <tr>
          <td class="text-xs-left">{{ props.item.year }}</td>
          <td class="text-xs-left">{{ props.item.make }}</td>
          <td class="text-xs-left">{{ props.item.model }}</td>
          <td class="text-xs-left">{{ props.item.vin }}</td>
          <td class="text-xs-left">
            <span v-if="props.item.plate_number">
              {{ props.item.plate_number }}
            </span>
            <span v-else>
              <v-chip>N/A</v-chip>
            </span>
          </td>
          <td class="text-xs-right">
            <v-menu offset-y left>
              <v-btn slot="activator" icon>
                <v-icon dark color="teal">settings</v-icon>
              </v-btn>
              <v-list>
                <v-list-tile @click="openDialog(props.item, 'confirmWorkOrderDialog')">
                  <v-list-tile-title>Work Order</v-list-tile-title>
                </v-list-tile>
                <v-list-tile @click="openDialog(props.item, 'editDialog')">
                  <v-list-tile-title>Edit</v-list-tile-title>
                </v-list-tile>
              </v-list>
            </v-menu>

          </td>
        </tr>
      </template>
      <template slot="pageText" slot-scope="{ pageStart, pageStop }">
        From {{ pageStart }} to {{ pageStop }}
      </template>
    </v-data-table>

    <!-- Edit vehicle dialog -->
    <v-dialog v-model="editDialog" persistent max-width="500px">
      <vehicle-form
        action="updateVehicle"
        :vehicle="selectedVehicle"
        :edit-state="editState"
        @saved="closeDialog('editDialog')"
        @close="closeDialog('editDialog')"
      ></vehicle-form>
    </v-dialog>

    <!-- Create work order dialog -->
    <v-dialog v-model="confirmWorkOrderDialog" persistent max-width="300px">
      <v-card>
        <v-card-text>
          Create work order?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn flat @click.native="confirmWorkOrderDialog = false">Cancel</v-btn>
          <v-btn color="teal" flat :loading="workOrderCreating" @click.native="newWorkOrder">Yes</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-card>
</template>

<script>
  import VehicleForm from './../forms/Vehicle-form';

	export default{
    props: ['vehicles'],

		data (){
			return {
				search: '',
				loading: false,
        editState: true,
 				headers: [
          {
            text: 'Year',
            align: 'left',
            sortable: true,
            value: 'year'
          },
          {
            text: 'Make',
            align: 'left',
            sortable: true,
            value: 'make'
          },
          {
            text: 'Model',
            align: 'left',
            sortable: true,
            value: 'model'
          },
          {
            text: 'VIN',
            align: 'left',
            sortable: true,
            value: 'vin'
          },
          {
            text: 'Plate Number',
            align: 'left',
            sortable: true,
            value: 'plate_number'
          },
          {
            text: '',
            align: 'right',
            sortable: false,
          }
        ],
        editDialog: false,
        selectedVehicle: '',
        confirmWorkOrderDialog: false,
        workOrderCreating: false
			}
		},

    components: {
      'vehicle-form': VehicleForm
    },

    methods: {
      openDialog (vehicle, dialog){
        // Set current vehicle
        this.selectedVehicle = vehicle;
        // Toggle Dialog
        this[dialog] = true;
      },

      closeDialog (dialog){
        // Toggle Dialog
        this[dialog] = false;
      },

      newWorkOrder (){

        // Toggle loader
        this.workOrderCreating = true;
        // For post
        var data = {
					vehicle_id: this.selectedVehicle.id
				};
        console.log(data);
        // Dispatch event
        this.$store.dispatch('createWorkOrder', data)
          .then((response) => {
            // Toggle loader
            this.workOrderCreating = false;
            // Redirect view
            this.$router.push('/work-orders/'+response.id);
          })
          .catch((response) => {
            // Toggle loader
            this.workOrderCreating = false;
            console.log(response);
          });
      }

    }
	}
</script>
