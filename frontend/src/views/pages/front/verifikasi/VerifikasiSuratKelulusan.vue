<template>
	<v-app>
		<v-main>
			<v-container v-if="sk_id">
				<v-row align="center" justify="center">
					<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
					<v-col xs="12" sm="6" md="4">
						<v-img
							class="mt-3 mb-3"
							max-width="400"
							max-height="auto"
							:src="$api.storageURL+'/storage/images/applogosuratkelulusan.png'"
							>
						</v-img>
						<v-alert dense text color="primary" class="text-center">
							Menyatakan bahwa :
							<br>
							<h3>
							{{data_surat.kepada}}
							</h3>
						</v-alert>
						<p class="text-center">
							Telah mengikuti tes masuk sebagai calon mahasiswa
							Sekolah Tinggi Teknologi Indonesia Tanjungpinang
							dan dinyatakan :
						</p>
						<v-alert dense text color="success" class="text-center">
							<h3><strong>LULUS</strong></h3>
						</v-alert>
						<p class="text-center">
							Dengan nomor surat : {{data_surat.nomor_surat}}
							Tanggal : {{data_surat.tanggal_surat}}
						</p>
						<v-alert dense text color="error" class="text-center">
							Tertanda :
							<br>
							<h3>
							{{data_surat.nama_user_ttd}}
							</h3>
						</v-alert>
					</v-col>
				</v-row>
			</v-container>

			<v-container class="fill-height" v-else>
				<v-alert dense text color="error" class="text-center">
					<h3>
						Maaf, resources ini tidak ada.
					</h3>
				</v-alert>
			</v-container>
		</v-main>
	</v-app>
</template>
<script>
	export default {
		name: "VerifikasiSuratKelulusan",
		created() {
			this.sk_id=this.$route.params.sk_id;
			this.initialize();
		},
		data: () => ({
			sk_id: null,
			data_surat: {
				id: "",   
				nama_user_ttd:"",
				nomor_surat:"",
				tanggal_surat: "",    
				perihal:"",
				kepada:"", 
			},
		}),
		methods: {
			async initialize() {
				await this.$ajax
					.get(
						"/verifikasi/" + this.sk_id + "/suratkelulusan",
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						})
						.then(({ data }) => {
							this.data_surat = data.surat_keluar;
						});
			},
		},
	};
</script>
