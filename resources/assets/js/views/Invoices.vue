<template>
	<layout>
		<div slot="title">Invoices</div>

		<div slot="content">
			<invoices-table></invoices-table>
		</div><!--/ Content slot -->
	</layout>
</template>

<script>
	import Layout from './_Layout';
	import InvoicesTable from './../components/tables/Invoices-table';

	export default{
		data () {
			return {
				addDialog: false
			}
		},

		components: {
			'layout': Layout,
			'invoices-table': InvoicesTable
		}	
	}
</script>