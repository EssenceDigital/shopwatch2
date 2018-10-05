<template v-if="vehicles">
 	<v-card class="elevation-0">
    <!-- Title -->
    <v-card-title>
      Vehicles
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
          <td class="text-xs-left">{{ props.item.plate_number }}</td>
          <td class="text-xs-right">
            <v-menu offset-y >
              <v-btn slot="activator" icon>
                <v-icon>settings</v-icon>
              </v-btn>              
              <v-list>
                <v-list-tile @click="openDialog(props.item, 'editDialog')">
                  <v-list-tile-title>Edit</v-list-tile-title>
                </v-list-tile>  
                <v-list-tile @click="openDialog(props.item, 'addVehicleDialog')">
                  <v-list-tile-title>View WOs</v-list-tile-title>
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
      <v-card>
        <v-system-bar window class="blue darken-4">
          <v-spacer></v-spacer>
          <v-tooltip top>
            <v-btn icon class="mr-0" slot="activator" @click="closeDialog('editDialog')">
              <v-icon class="white--text mr-0">close</v-icon>
            </v-btn>                      
            <span>Close dialog</span>
          </v-tooltip>            
        </v-system-bar>
        <v-card-text>
          <vehicle-form action="updateVehicle" :vehicle="selectedVehicle" edit-state="true" @saved="closeDialog('editDialog')"></vehicle-form>
        </v-card-text>
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
        selectedVehicle: ''
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
      }
      
    }		
	}
</script>