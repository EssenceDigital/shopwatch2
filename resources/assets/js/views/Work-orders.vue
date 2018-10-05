<template>
	<layout>
		<div slot="title">Work Orders</div>

		<div slot="content">
			<work-orders-table></work-orders-table>
		</div><!--/ Content slot -->
	</layout>
</template>

<script>
	import Layout from './_Layout';
	import WorkOrdersTable from './../components/tables/Work-orders-table';

	export default{
		data () {
			return {
				addDialog: false
			}
		},

		components: {
			'layout': Layout,
			'work-orders-table': WorkOrdersTable
		}	
	}
</script>