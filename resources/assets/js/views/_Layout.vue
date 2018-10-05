<template>
	<v-layout row wrap>
    <v-flex xs12 class="my-3">
      <v-card color="grey lighten-3" flat>
        <v-toolbar class="elevation-0">
 					<!--<v-btn icon class="hidden-xs-only" @click="$router.go(-1)">
            <v-icon>arrow_back</v-icon>
          </v-btn>-->
          <v-toolbar-title>
          	<slot name="title"></slot>
          </v-toolbar-title>
          <v-spacer></v-spacer>

          <slot name="tools"></slot>

        </v-toolbar>
        <v-card-text>

        	<slot name="content"></slot>

        </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script>
	export default{

	}
</script>
