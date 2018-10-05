<template>
  <v-app id="keep">
    <v-navigation-drawer
      v-model="drawer"
      fixed
      clipped
      class="grey lighten-4"
      app
    >
      <v-list
        dense
        class="grey lighten-4"
      >
        <template v-for="(item, i) in menuItems">
          <v-layout
            v-if="item.heading"
            :key="i"
            row
            align-center
          >
            <v-flex xs6>
              <v-subheader v-if="item.heading">
                {{ item.heading }}
              </v-subheader>
            </v-flex>
          </v-layout>
          <v-divider
            v-else-if="item.divider"
            :key="i"
            dark
            class="my-3"
          ></v-divider>
          <v-list-tile
            v-else
            :key="i"
            @click="$router.push(item.link)"
          >
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title class="grey--text">
                {{ item.text }}
              </v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </template>
      </v-list>
    </v-navigation-drawer>
    <v-toolbar color="red" app absolute clipped-left>
      <v-toolbar-side-icon @click.native="drawer = !drawer"></v-toolbar-side-icon>
      <span class="title ml-3 mr-5">Shop&nbsp;<span class="font-weight-light">Watch</span></span>
      <v-text-field
        solo-inverted
        flat
        hide-details
        label="Search"
        prepend-inner-icon="search"
      ></v-text-field>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-content>
      <!-- For showing user alerts and feedback -->
      <v-snackbar
        v-model="snackbar"
        :timeout="timeout"
        :absolute="true"
        :multi-line="true"
        class="absolute-center"
      >
        {{ snackBarText }}
        <v-btn flat class="pink--text" @click.native.stop="snackbar = false">Close</v-btn>
      </v-snackbar>

      <v-container fluid fill-height>

        <router-view></router-view>

      </v-container>
    </v-content>
    <v-footer color="red" class="white--text" app>
      <span class="ml-2">Shop&nbsp;</span><span class="font-weight-light">Watch</span></span>
      <v-spacer></v-spacer>
      <span class="mr-2">&copy; 2018</span>
    </v-footer>
  </v-app>
</template>

<script>
  export default {

    data: function(){
      return {
        snackbar: false,
        mode: '',
        timeout: 10000,
        snackBarText: '',
        drawer: null,
        menuItems: [
          { icon: 'dashboard', text: 'Dashboard', link: '/' },
          { divider: true },
          { heading: 'Tools' },
          { icon: 'group', text: 'Customers', link: '/customers' },
          { icon: 'assignment', text: 'Work Orders', link: '/work-orders' },
          { icon: 'payment', text: 'Invoices', link: '/invoices' },
          { icon: 'assessment', text: 'Expenses', link: '/expenses' },
          { divider: true },
          { icon: 'group', text: 'Users', link: '/users' },
          { icon: 'settings', text: 'Settings', link: '/settings' },
          { icon: 'help', text: 'Help', link: '/help' }
        ],
        token: window.Laravel.csrfToken,
        authEmail: AUTH_EMAIL
      }
    },

    props: {
      source: String
    },

    components: {

    },

    methods: {

    },

    created () {
      this.$router.app.$on('snackbar', (config) => {
        this.snackbar = true;
        this.snackBarText = config.text;
      });
    }
  }
</script>

<style scoped>
  .absolute-center{
    top: 40%;
  }
</style>
