<template>
	<SystemConfigLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-variable
			</template>
			<template v-slot:name>
				VARIABLES
			</template>
			<template v-slot:breadcrumbs>
				<v-breadcrumbs :items="breadcrumbs" class="pa-0">
					<template v-slot:divider>
						<v-icon>mdi-chevron-right</v-icon>
					</template>
				</v-breadcrumbs>
			</template>
			<template v-slot:desc>
				<v-alert
					color="orange"
					border="left"
					colored-border
					type="info"
					>
					Mengatur berbagai macam variable default sistem. Perubahan berlaku pada Login selanjutnya.
					</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>  
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-form ref="frmdata_pmb" v-model="form_valid" lazy-validation>
						<v-card class="mb-4" color="grey lighten-4" flat>
							<v-card-title>
								PENERIMAAN MAHASISWA BARU
							</v-card-title>
							<v-card-text>
								<v-row>									
									<v-col xs="12" sm="12" md="4">
										<v-select
											v-model="formdata_pmb.tahun_pendaftaran" 
											:items="daftar_ta"
											label="TAHUN PENDAFTARAN"
											outlined
											:rules="rule_tahun_pendaftaran"/>
									</v-col>
									<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
								</v-row>
							</v-card-text>
						</v-card>
						<v-card class="mb-4" color="grey lighten-4" flat>
							<v-card-title>
									PEJABAT PENANDATANGAN SK KELULUSAN
							</v-card-title>
							<v-card-text>
								<v-row>
									<v-col xs="12" sm="12" md="12">
										<v-text-field
											v-model="formdata_pmb.nama_sk_kelulusan"
											label="NAMA PEJABAT"
											outlined
											:rules="rule_nama_sk_kelulusan"
										>
										</v-text-field>
										<v-text-field
											v-model="formdata_pmb.nidn_sk_kelulusan"
											label="NIDN"
											outlined
											:rules="rule_nidn_sk_kelulusan"
										>
										</v-text-field>
										<v-text-field
											v-model="formdata_pmb.nik_sk_kelulusan"
											label="NOMOR INDUK KEPEGAWAIAN"
											outlined
											:rules="rule_nik_sk_kelulusan"
										>
										</v-text-field>
									</v-col>
									<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
								</v-row>
							</v-card-text>
							<v-card-actions>
								<v-spacer></v-spacer>
								<v-btn
									color="blue darken-1"
									text
									@click.stop="save_pmb"									
									:disabled="!form_valid||btnLoading">SIMPAN</v-btn>
							</v-card-actions>
						</v-card>
					</v-form>
				</v-col>
				<v-col cols="12">
					<v-form ref="frmdata_perkuliahan" v-model="form_valid" lazy-validation>
							<v-card class="mb-4" color="grey lighten-4" flat>
							<v-card-title>
								PERKULIAHAN
							</v-card-title>
							<v-card-text>
								<v-row>
									<v-col xs="12" sm="12" md="4">
										<v-select
											v-model="formdata_perkuliahan.default_ta" 
											:items="daftar_ta"
											label="TAHUN AKADEMIK"
											outlined
											:rules="rule_default_ta"/>
									</v-col>
									<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
									<v-col xs="12" sm="12" md="4">
										<v-select
											v-model="formdata_perkuliahan.default_semester" 
											:items="daftar_semester"
											item-text="text"
											item-value="id"
											label="SEMESTER AKADEMIK"
											outlined
											:rules="rule_default_semester"/>
									</v-col>
									<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>									
								</v-row>
							</v-card-text>
							<v-card-actions>
								<v-spacer></v-spacer>
								<v-btn
									color="blue darken-1"
									text
									@click.stop="save_perkuliahan"									
									:disabled="!form_valid||btnLoading">SIMPAN</v-btn>
							</v-card-actions>
							</v-card>
					</v-form>
				</v-col>
			</v-row>
		</v-container>
	</SystemConfigLayout>
</template>
<script>
	import { mapGetters } from "vuex";
	import SystemConfigLayout from "@/views/layouts/SystemConfigLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "Variables",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.ACCESS_TOKEN,
				},
				{
					text: "KONFIGURASI SISTEM",
					disabled: false,
					href: "/system-setting",
				},
				{
					text: "PERGURUAN TINGGI",
					disabled: false,
					href: "#",
				},
				{
					text: "VARIABLES",
					disabled: true,
					href: "#",
				},
			];
			this.daftar_ta = this.$store.getters['uiadmin/getDaftarTA'];
			this.daftar_semester = this.$store.getters['uiadmin/getDaftarSemester'];
			this.initialize();
		},
		data: () => ({
			breadcrumbs: [],
			btnLoading: false, 			

			//form
			form_valid: true,
			
			//form pmb
			daftar_user_ttd_sk_kelulusan: [],
			formdata_pmb: {
				tahun_pendaftaran: 0,
				nama_sk_kelulusan: "",
				nidn_sk_kelulusan: "",
				nik_sk_kelulusan: "",
			},

			//form perkuliahan
			daftar_ta: [],
			daftar_semester: [],
			formdata_perkuliahan: {
				default_ta: "",
				default_semester: "",
				tahun_pendaftaran: 0,
			},

			//form rules  pmb
			rule_tahun_pendaftaran: [
				value => !!value || "Mohon untuk dipilih Tahun Pendaftaran !!!",
			],
			rule_nama_sk_kelulusan: [
				value => !!value || "Mohon untuk diisi nama penjabat yang akan ttd sk kelulusan !!!",
			],
			rule_nidn_sk_kelulusan: [
				value => !!value || "Mohon untuk diisi nidn penjabat yang akan ttd sk kelulusan !!!",
				value => /^[0-9]+$/.test(value) || "NIDN hanya boleh angka",
			],
			rule_nik_sk_kelulusan: [
				value => !!value || "Mohon untuk diisi nik penjabat yang akan ttd sk kelulusan !!!",
				value => /^[0-9]+$/.test(value) || "NIK hanya boleh angka",
			],

			//form rules  perkuliahan
			rule_default_ta: [
				value => !!value || "Mohon untuk dipilih Tahun Akademik !!!",
			],
			rule_default_semester: [
				value => !!value || "Mohon untuk diisi Semester !!!",
			],
		}),
		methods: {
			initialize: async function() {
				await this.$ajax
					.get("/system/setting/variables",
						{
						headers: {
							Authorization: this.TOKEN,
						},
					})
					.then(({ data }) => {
						let setting = data.setting;
						
						this.formdata_pmb.tahun_pendaftaran = setting.DEFAULT_TAHUN_PENDAFTARAN;
						var ttd_sk_kelulusan = JSON.parse(setting.DEFAULT_TTD_SK_KELULUSAN);
						this.formdata_pmb.nama_sk_kelulusan = ttd_sk_kelulusan.nama;
						this.formdata_pmb.nidn_sk_kelulusan = ttd_sk_kelulusan.nidn;
						this.formdata_pmb.nik_sk_kelulusan = ttd_sk_kelulusan.nik;

						this.formdata_perkuliahan.default_ta = setting.DEFAULT_TA;
						this.formdata_perkuliahan.default_semester = setting.DEFAULT_SEMESTER;
					});
			},
			save_pmb() {
				if (this.$refs.frmdata_pmb.validate()) {
					this.btnLoading = true;
					var default_ttd_sk_kelulusan = {
						nama: this.formdata_pmb.nama_sk_kelulusan,
						nidn: this.formdata_pmb.nidn_sk_kelulusan,
						nik: this.formdata_pmb.nik_sk_kelulusan,
					};
					this.$ajax
						.post(
							"/system/setting/variables",
							{
								_method: "PUT",
								pid: "Variable default sistem",
								setting: JSON.stringify({
									203: this.formdata_pmb.tahun_pendaftaran,
									207: JSON.stringify(default_ttd_sk_kelulusan),
								}),
							},
							{
								headers: {
									Authorization: this.TOKEN,
								},
							}
						)
						.then(() => {
							this.btnLoading = false;
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
			save_perkuliahan() {
				if (this.$refs.frmdata_perkuliahan.validate()) {
					this.btnLoading = true;
					this.$ajax
						.post(
							"/system/setting/variables",
							{
								_method: "PUT",
								pid: "Variable default sistem",
								setting: JSON.stringify({
									201: this.formdata_perkuliahan.default_ta,
									202: this.formdata_perkuliahan.default_semester,
								}),
							},
							{
								headers: {
									Authorization: this.TOKEN,
								},
							}
						)
						.then(() => {
							this.btnLoading = false;
							this.$router.go();
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
		},
		computed: {
			...mapGetters("auth", {
				ACCESS_TOKEN: "AccessToken",
				TOKEN: "Token",
			}),
		},
		components: {
			SystemConfigLayout,
			ModuleHeader,
		},
	};
</script>
