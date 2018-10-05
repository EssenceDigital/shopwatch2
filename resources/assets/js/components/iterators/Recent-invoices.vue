<template>
  <v-container fluid grid-list-md>
    <v-layout row class="mb-4">
      <v-icon>payment</v-icon>
      <h2 class="font-weight-thin ml-2">Recent Invoices</h2>
    </v-layout>
    <v-data-iterator
      :items="invoices"
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
            <h4>INV-{{ props.item.id }} <v-chip small color="warning">Open</v-chip></h4>
            <v-spacer></v-spacer>
            <v-menu offset-y>
              <v-icon slot="activator">more_horiz</v-icon>
              <v-list>
                <v-list-tile @click="">
                  <v-list-tile-title>View</v-list-tile-title>
                </v-list-tile>
                <v-list-tile @click="">
                  <v-list-tile-title>Payment</v-list-tile-title>
                </v-list-tile>
              </v-list>
            </v-menu>
          </v-card-title>
          <v-divider></v-divider>
          <v-list dense>
            <v-list-tile>
              <v-list-tile-content>Customer:</v-list-tile-content>
              <v-list-tile-content class="align-end grey--text lighten-1 font-weight-black">{{ props.item.customer }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Date:</v-list-tile-content>
              <v-list-tile-content class="align-end grey--text lighten-1 font-weight-black">{{ props.item.date_created }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Vehicle:</v-list-tile-content>
              <v-list-tile-content class="align-end grey--text lighten-1 font-weight-black">{{ props.item.vehicle }}</v-list-tile-content>
            </v-list-tile>
            <v-list-tile>
              <v-list-tile-content>Total:</v-list-tile-content>
              <v-list-tile-content class="align-end grey--text lighten-1 font-weight-black">{{ props.item.grand_total }}</v-list-tile-content>
            </v-list-tile>
          </v-list>
        </v-card>
      </v-flex>
    </v-data-iterator>
  </v-container>
</template>

<script>
  export default {
    computed: {
      invoices () {
        return this.$store.getters.invoices;
      }
    },

    data: () => ({
      rowsPerPageItems: [4, 8, 12],
      pagination: {
        rowsPerPage: 4
      }
    })
  }
</script>
