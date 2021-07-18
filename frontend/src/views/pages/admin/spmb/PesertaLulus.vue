<template>
	<SPMBLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-contacts
			</template>
			<template v-slot:name>
				PESERTA YANG LULUS
			</template>
			<template v-slot:subtitle v-if="dashboard != 'mahasiswabaru'">
				TAHUN PENDAFTARAN {{ tahun_pendaftaran }} - {{ nama_prodi }}
			</template>
			<template v-slot:breadcrumbs>
				<v-breadcrumbs :items="breadcrumbs" class="pa-0">
					<template v-slot:divider>
						<v-icon>mdi-chevron-right</v-icon>
					</template>
				</v-breadcrumbs>
			</template>
			<template v-slot:desc>
				<v-alert color="orange" border="left" colored-border type="info">
					Berisi daftar mahasiswa baru yang lulus ujian PMB
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-text>
							<v-text-field
								v-model="search"
								append-icon="mdi-database-search"
								label="Search"
								single-line
								hide-details
							></v-text-field>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-data-table
						:headers="headers"
						:items="datatable"
						:search="search"
						item-key="id"
						sort-by="name"
						show-expand
						:expanded.sync="expanded"
						:single-expand="true"
						@click:row="dataTableRowClicked"
						class="elevation-1"
						:loading="datatableLoading"
						loading-text="Loading... Please wait"
					>
						<template v-slot:top>
							<v-toolbar flat color="white">
								<v-toolbar-title>DAFTAR PESERTA YANG LULUS</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
							</v-toolbar>
						</template>
						<template v-slot:item.foto="{ item }">
							<v-badge
								bordered
								:color="badgeColor(item)"
								:icon="badgeIcon(item)"
								overlap
							>
								<v-avatar size="30">
									<v-img :src="$api.storageURL + '/' + item.foto" />
								</v-avatar>
							</v-badge>
						</template>
						<template v-slot:item.actions="{ item }">
							<v-btn
								small
								icon
								:loading="btnLoading"
								:disabled="btnLoading"
								@click.stop="printpdf(item)"
								title="Print SK Kelulusan"
							>
								<v-icon>
									mdi-printer
								</v-icon>
							</v-btn>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="12">
									<strong>ID:</strong>{{ item.id }} <strong>created_at:</strong
									>{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong
									>{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
								</v-col>
							</td>
						</template>
						<template v-slot:no-data>
							Data belum tersedia
						</template>
					</v-data-table>
				</v-col>
			</v-row>
			<dialog-printout
				pid="sklulus"
				title="SK kelulusan"
				ref="dialogprint"
			></dialog-printout>
		</v-container>
		<template v-slot:filtersidebar v-if="dashboard != 'mahasiswabaru'">
			<Filter7
				v-on:changeTahunPendaftaran="changeTahunPendaftaran"
				v-on:changeProdi="changeProdi"
				ref="filter7"
			/>
		</template>
	</SPMBLayout>
</template>
<script>
	import SPMBLayout from "@/views/layouts/SPMBLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	import Filter7 from "@/components/sidebar/FilterMode7";
	import DialogPrintoutSPMB from "@/components/DialogPrintoutSPMB";
	export default {
		name: "NilaiUjian",
		created() {
			this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "SPMB",
					disabled: false,
					href: "/spmb",
				},
				{
					text: "PESERTA YANG UJIAN",
					disabled: true,
					href: "#",
				},
			];
			this.breadcrumbs[1].disabled =
				this.dashboard == "mahasiswabaru" || this.dashboard == "mahasiswa";
			let prodi_id = this.$store.getters["uiadmin/getProdiID"];
			this.prodi_id = prodi_id;
			this.nama_prodi = this.$store.getters["uiadmin/getProdiName"](prodi_id);
			this.tahun_pendaftaran = this.$store.getters[
				"uiadmin/getTahunPendaftaran"
			];
			this.initialize();
		},
		data: () => ({
			firstloading: true,
			prodi_id: null,
			tahun_pendaftaran: null,
			nama_prodi: null,
			breadcrumbs: [],
			dashboard: null,
			btnLoading: false,
			datatableLoading: false,
			expanded: [],
			datatable: [],
			headers: [
				{ text: "", value: "foto", width: 70 },
				{
					text: "NO.FORMULIR",
					value: "no_formulir",
					width: 150,
					sortable: true,
				},
				{ text: "NAMA MAHASISWA", value: "name", width: 350, sortable: true },
				{ text: "NOMOR HP", value: "nomor_hp", width: 100 },
				{ text: "KELAS", value: "nkelas", width: 100, sortable: true },
				{ text: "NILAI", value: "nilai", width: 100, sortable: true },
				{ text: "STATUS", value: "status", width: 100, sortable: true },
				{ text: "AKSI", value: "actions", sortable: false, width: 100 },
			],
			search: "",
		}),
		methods: {
			changeTahunPendaftaran(tahun) {
				this.tahun_pendaftaran = tahun;
			},
			changeProdi(id) {
				this.prodi_id = id;
			},
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/spmb/pesertalulus",
						{
							TA: this.tahun_pendaftaran,
							prodi_id: this.prodi_id,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.pmb;
						this.datatableLoading = false;
					});
				this.firstloading = false;
				this.$refs.filter7.setFirstTimeLoading(this.firstloading);
			},
			dataTableRowClicked(item) {
				if (item === this.expanded[0]) {
					this.expanded = [];
				} else {
					this.expanded = [item];
				}
			},
			badgeColor(item) {
				return item.active == 1 ? "success" : "error";
			},
			badgeIcon(item) {
				return item.active == 1 ? "mdi-check-bold" : "mdi-close-thick";
			},
			async printpdf(item) {
				this.btnLoading = true;
				await this.$ajax
					.post(
						"/spmb/skkelulusan/printtopdf1/" + item.id,
						{},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.$refs.dialogprint.open({
							message:
								"Silahkah download Formulir Pendaftaran dan SK Kelulusan",
							file: data.pdf_file,
							nama_file: "SK KELULUSAN",
						});
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
		},
		watch: {
			tahun_pendaftaran() {
				if (!this.firstloading) {
					this.initialize();
				}
			},
			prodi_id(val) {
				if (!this.firstloading) {
					this.nama_prodi = this.$store.getters["uiadmin/getProdiName"](val);
					this.initialize();
				}
			},
		},
		components: {
			SPMBLayout,
			ModuleHeader,
			Filter7,
			"dialog-printout": DialogPrintoutSPMB,
		},
	};
</script>
