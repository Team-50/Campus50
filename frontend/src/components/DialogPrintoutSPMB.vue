<template>
	<v-dialog v-model="dialog" max-width="500px" @keydown.esc="cancel">
		<v-card>
			<v-toolbar dark color="primary" dense flat>
				<v-toolbar-title class="white--text">
					Printout {{title}}        
				</v-toolbar-title>
				<v-spacer></v-spacer> 
				<v-icon>mdi-printer</v-icon> 
			</v-toolbar>
			<v-card-text class="mt-2">
				{{ message }}<br>				
				<v-btn
					color="green"
					text
					:href="$api.storageURL + '/' + file">
					Download
			</v-btn> 
			</v-card-text>
		</v-card>
	</v-dialog>
</template>
<script>
	export default {
		name: "DialogPrintoutSPMB",
		props: {
			pid: {
				type: String,
				required: true,
			},
			title: {
				type: String,
				default: "",
				required: true,
			},
		},
		data: () => ({
			dialog: false,
			message: "",
			file: "",
			nama_file: "",
		}),
		methods: {
			open(item) {
				this.message = item.message;
				this.file = item.file;
				this.nama_file = item.nama_file;
				this.dialog=true;
			},
			cancel () {
					this.resolve(false)
					this.dialog = false
			}
		},
	};
</script>
