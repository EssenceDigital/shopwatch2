<template>
	<layout>
		<div slot="title">Users</div>

		<div slot="tools">
			<v-tooltip top>
		    <v-btn color="info" left slot="activator" @click="addDialog = true">
		      <v-icon left>person_add</v-icon> User
		    </v-btn>				
	      <span>Add User</span>
	    </v-tooltip>
		</div>

		<div slot="content">
			<!-- Users table -->
			<users-table></users-table>

			<!-- Form dialog -->
			<v-dialog v-model="addDialog" persistent max-width="500px">
        <v-card>
				 	<v-system-bar window class="blue darken-4">
			      <v-spacer></v-spacer>
						<v-tooltip top>
				      <v-btn icon class="mr-0" slot="activator" @click="addDialog = false">
				      	<v-icon class="white--text mr-0">close</v-icon>
				      </v-btn>											
				      <span>Close dialog</span>
				    </v-tooltip>			      
			    </v-system-bar>
          <v-card-text>
          	<user-form action="createUser" @saved="addDialog = false"></user-form>
          </v-card-text>
        </v-card>
      </v-dialog>			

		</div><!--/ Content slot -->

	</layout>
</template>

<script>
	import Layout from './_Layout';
	import UsersTable from './../components/tables/Users-table';
	import UserForm from './../components/forms/User-form';

	export default{
		data () {
			return {
				addDialog: false
			}
		},

		components: {
			'layout': Layout,
			'users-table': UsersTable,
			'user-form': UserForm
		}	
	}
</script>