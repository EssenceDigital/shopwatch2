<template>
 	<v-card class="elevation-0">
    <v-card-title>
      Users
      <v-spacer></v-spacer>
      <v-text-field
        append-icon="search"
        label="Search"
        single-line
        hide-details
        v-model="search"
      ></v-text-field>
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="users"
      :search="search"
    >
      <template slot="items" slot-scope="props">
        <tr>
          <td class="text-xs-left">{{ props.item.name }}</td>
          <td class="text-xs-left">{{ props.item.email }}</td>
          <td class="text-xs-left">{{ props.item.role }}</td>
          <td class="text-xs-left">{{ props.item.hourly_wage | money }}</td>
          <td class="text-xs-right">
            <v-menu offset-y >
              <v-btn slot="activator" icon>
                <v-icon>settings</v-icon>
              </v-btn>              
              <v-list>
                <v-list-tile @click="openDialog(props.item, 'editDialog')">
                  <v-list-tile-title>Edit</v-list-tile-title>
                </v-list-tile>
                <v-list-tile @click="openDialog(props.item, 'passwordDialog')">
                  <v-list-tile-title>Password</v-list-tile-title>
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

    <!-- Edit user dialog -->
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
          <user-form action="updateUser" :user="selectedUser" edit-state="true" @saved="closeDialog('editDialog')"></user-form>
        </v-card-text>
      </v-card>
    </v-dialog> 

    <!-- Edit password dialog -->
    <v-dialog v-model="passwordDialog" persistent max-width="500px">
      <v-card>
        <v-system-bar window class="blue darken-4">
          <v-spacer></v-spacer>
          <v-tooltip top>
            <v-btn icon class="mr-0" slot="activator" @click="closeDialog('passwordDialog')">
              <v-icon class="white--text mr-0">close</v-icon>
            </v-btn>                      
            <span>Close dialog</span>
          </v-tooltip>            
        </v-system-bar>
        <v-card-text>
          <user-password-form 
            action="changeUserPassword"
            editState="true"
            :user="selectedUser" 
            @saved="closeDialog('passwordDialog')"
          ></user-password-form>
        </v-card-text>
      </v-card>
    </v-dialog>     

  </v-card>		
</template>

<script>
  import UserForm from './../forms/User-form';
  import PasswordForm from './../forms/User-password-form';

	export default{
		data (){
			return {
				search: '',
				loading: true,
 				headers: [
          {
            text: 'Name',
            align: 'left',
            sortable: true,
            value: 'name'
          },
          {
            text: 'Email',
            align: 'left',
            sortable: true,
            value: 'email'
          },
          {
            text: 'Role',
            align: 'left',
            sortable: true,
            value: 'role'
          },
          {
            text: 'Hourly',
            align: 'left',
            sortable: true,
            value: 'hourly_wage'
          },
          {
            text: '',
            align: 'right',
            sortable: false,
          }                                    
        ],
        editDialog: false,
        selectedUser: '',
        passwordDialog: false,
			}
		},

		computed: {
			users (){
				return this.$store.getters.users;
			}
		},

    components: {
      'user-form': UserForm,
      'user-password-form': PasswordForm
    },

    methods: {
      openDialog (user, dialog){
        // The the user for editing
        this.selectedUser = user;
        // Toggle Dialog
        this[dialog] = true;
      },

      closeDialog (dialog){
        // Toggle Dialog
        this[dialog] = false;        
      },
      
    },

		created (){
			this.$store.dispatch('getUsers')
				.then((response) => {
					// Toggle table loader
					this.loading = false;
				});
		}		
	}
</script>