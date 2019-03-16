<template>
  <v-card class="elevation-2 grey lighten-4">
    <v-card-title>
      <h3 class="font-weight-thin ml-2">
        <v-icon>shopping_basket</v-icon>
        Select a job
      </h3>
    </v-card-title>

    <v-card-text>

      <v-radio-group v-model="selectedJob" :mandatory="false">
        <v-radio label="Oil Change" value="LOF"></v-radio>
        <v-radio label="Detail" value="DTL"></v-radio>
        <v-radio label="Mount and Balance Tires" value="MTB"></v-radio>
      </v-radio-group>

      <!-- Oil change options dialog -->
      <v-dialog v-model="lofDialog" persistent max-width="500px">
        <v-card>
          <v-card-text>
            <v-layout row>
              <v-flex xs3>
                <v-text-field
                  v-model="lofOptions.litres"
                  type="number"
                  min="3"
                  step="0.1"
                  label="Litres"
                ></v-text-field>
              </v-flex>
              <v-spacer></v-spacer>
              <v-flex xs4>
                <v-text-field
                  v-model="lofOptions.weight"
                  label="Oil Weight"
                ></v-text-field>
              </v-flex>
              <v-spacer></v-spacer>
              <v-flex xs4>
                <v-text-field
                  v-model="lofOptions.filter"
                  label="Oil Filter"
                ></v-text-field>
              </v-flex>
            </v-layout>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="closeDialog('lofDialog')" flat>Cancel</v-btn>
            <v-btn @click="addRegLof" color="teal" flat>Confirm</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-card-text>

    <v-divider></v-divider>

    <v-card-actions>

      <v-spacer></v-spacer>
      <v-btn
        @click="$emit('close')"
        color="danger"
        flat
      >
        Cancel
      </v-btn>
      <v-btn
        flat
        color="teal"
      >
        Select
      </v-btn>
    </v-card-actions>
  </v-card>

</template>

<script>
  export default{
    props: ['workOrder'],

    data (){
      return {

        lof: {
          work_order_id: '',
          is_premade: true,
          title: '',
          description: 'Lube, oil, filter - ',
          hours: 0.5,
          shop_rate: 50,
          // Flat rate fields here so job form works later on
          is_flat_rate: false,
					flat_rate: 0,
					flat_rate_cost: 0,
          parts: {
            filter: {
              id: "filter",
              title: 'Filter',
    					part_number: '',
    					supplier: 'C.E.P.',
    					quantity: 1,
    					total_cost: 3.99,
    					billing_price: 7.99,
            },
            oil: {
              id: "oil",
              title: 'Oil',
              part_number: '',
              supplier: 'Auto Value',
              quantity: 0,
              total_cost: 3.35,
              billing_price: 6.35,
            }
          }
        },


        selectedJob: '',
        lofDialog: false,
        lofOptions: {
          litres: '',
          weight: '',
          filter: ''
        }
      }
    },

    watch: {
      selectedJob (job){
        if(job == 'LOF'){
          this.lofDialog = true;
        }
      }
    },

    methods: {
      closeDialog (dialog){
        // Reset fields
        this[dialog] = false;
        this.selectedJob = '';
        this.lofOptions.litres = '';
        this.lofOptions.weight = '';
        this.lofOptions.filter = '';
      },

      addRegLof (){
        // Set lof options for car/small SUV
        if(this.lofOptions.litres <= 5){
          this.lof.title = 'Oil Change - Car/Minivan/Small SUV';
          this.lof.parts.filter.part_number = this.lofOptions.filter;
          this.lof.parts.oil.part_number = this.lofOptions.weight;
          this.lof.parts.oil.quantity = 5;
        } else {
          this.lof.title = 'Oil Change - Truck/Van/Large SUV';
          this.lof.parts.filter.part_number = this.lofOptions.filter;
          this.lof.parts.oil.part_number = this.lofOptions.weight;
          this.lof.parts.oil.quantity = 8;
        }



        // Add oil litres and weight to description
        this.lof.description += this.lofOptions.litres + 'L/'
                              + this.lofOptions.weight + '/'
                              + this.lofOptions.filter;
        // Add end of description
        this.lof.description += ' - Tire pressures ok, fluids ok, coolant -35c';
        // Emit event and send premade job to parent
        this.$emit('added', this.lof);
        // Close premade job dialog
        this.closeDialog('lofDialog');
      }
    },

    created (){
      if(this.workOrder){
        this.lof.work_order_id = this.workOrder;
      }
    }
  }
</script>
