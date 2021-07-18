<template>
	<AkademikLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-account-box-multiple
			</template>
			<template v-slot:name>
				DAFTAR ULANG MAHASISWA LULUS
			</template>
			<template v-slot:subtitle>
				TAHUN AKADEMIK {{ tahun_akademik }} SEMESTER
				{{ $store.getters["uiadmin/getNamaSemester"](semester_akademik) }} -
				{{ nama_prodi }}
			</template>
			<template v-slot:breadcrumbs>
				<v-breadcrumbs :items="breadcrumbs" class="pa-0">
					<template v-slot:divider>
						<v-icon>mdi-chevron-right</v-icon>
					</template>
				</v-breadcrumbs>
			</template>
			<template v-slot:desc>
				<v-alert color="cyan" border="left" colored-border type="info">
					Halaman untuk melihat daftar mahasiswa yang lulus.
				</v-alert>
			</template>
		</ModuleHeader>
		<template v-slot:filtersidebar>
			<Filter6
				v-on:changeTahunAkademik="changeTahunAkademik"
				v-on:changeSemesterAkademik="changeSemesterAkademik"
				v-on:changeProdi="changeProdi"
				ref="filter6"
			/>
		</template>
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
								<v-toolbar-title>DAFTAR MAHASISWA</v-toolbar-title>
								<v-divider class="mx-4" inset vertical />
								<v-spacer></v-spacer>
								<v-tooltip bottom>
									<template v-slot:activator="{ on, attrs }">
										<v-btn
											color="primary"
											v-bind="attrs"
											v-on="on"
											small
											icon
											outlined
											class="ma-2"
											@click.stop="addItem"
										>
											<v-icon>mdi-plus</v-icon>
										</v-btn>
									</template>
									<span>Tambah Mahasiswa Lulus</span>
								</v-tooltip>
								<v-dialog v-model="dialogfrm" max-width="800px" persistent>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">DAFTAR ULANG MAHASISWA DENGAN STATUS LULUS</span>
											</v-card-title>
											<v-card-subtitle>
												Mahasiswa Program Studi {{ nama_prodi }} di T.A {{ tahun_akademik }} SEMESTER
												{{ $store.getters["uiadmin/getNamaSemester"](semester_akademik) }}
											</v-card-subtitle>
											<v-card-text>
												<v-text-field
													v-model="formdata.nim"
													label="NOMOR INDUK MAHASISWA (NIM)"
													outlined
													append-outer-icon="mdi-send"
													@click:append-outer="cekNIM"
													:rules="rule_nim"
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.nama_mhs"
													label="NAMA MAHASISWA"
													outlined
													:disabled="true"
												>
												</v-text-field>
												<v-textarea
													v-model="formdata.descr"
													label="KETERANGAN"
													outlined
													:disabled="!is_mhs_checked"
												>
												</v-textarea>
											</v-card-text>
											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="closedialogfrm"
												>
													TUTUP
												</v-btn>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="save"
													:disabled="!form_valid || btnLoading || !is_mhs_checked"
												>
													LULUS
												</v-btn>
											</v-card-actions>
										</v-card>
									</v-form>
								</v-dialog>
							</v-toolbar>
						</template>
						<template v-slot:item.idkelas="{ item }">
							{{ $store.getters["uiadmin/getNamaKelas"](item.idkelas) }}
						</template>
						<template v-slot:item.actions v-if="dashboard == 'mahasiswa'">
							N.A
						</template>
						<template v-slot:item.actions="{ item }" v-else>
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<v-icon
										v-bind="attrs"
										v-on="on"
										small
										color="red darken-1"
										:disabled="btnLoading"
										@click.stop="deleteItem(item)"
									>
										mdi-delete
									</v-icon>
								</template>
								<span>Hapus Daftar Ulang Aktif</span>
							</v-tooltip>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="12">
									<strong>id:</strong>
									{{ item.id }}
									<strong>created_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong>
									{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
								</v-col>
							</td>
						</template>
						<template v-slot:no-data>
							Data belum tersedia
						</template>
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
	</AkademikLayout>
</template>
<script>
	import AkademikLayout from "@/views/layouts/AkademikLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	import Filter6 from "@/components/sidebar/FilterMode6";
	export default {
		name: "DulangMahasiswaLulus",
		created() {
			this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "AKADEMIK",
					disabled: false,
					href: "/akademik",
				},
				{
					text: "DAFTAR ULANG",
					disabled: false,
					href: "#",
				},
				{
					text: "MAHASISWA LULUS",
					disabled: true,
					href: "#",
				},
			];
			let prodi_id = this.$store.getters["uiadmin/getProdiID"];
			this.prodi_id = prodi_id;
			this.nama_prodi = this.$store.getters["uiadmin/getProdiName"](prodi_id);
			this.tahun_akademik = this.$store.getters["uiadmin/getTahunAkademik"];
			this.semester_akademik = this.$store.getters[
				"uiadmin/getSemesterAkademik"
			];
			this.initialize();
		},
		mounted() {
			this.firstloading = false;
			this.$refs.filter6.setFirstTimeLoading(this.firstloading);
		},
		data: () => ({
			dashboard: null,
			firstloading: true,
			prodi_id: null,
			nama_prodi: null,
			tahun_akademik: null,
			semester_akademik: null,

			btnLoading: false,
			btnLoadingTable: false,
			datatableLoading: false,
			expanded: [],
			datatable: [],
			headers: [
				{
					text: "NO. FORMULIR",
					value: "no_formulir",
					sortable: true,
					width: 100,
				},
				{ text: "NIM", value: "nim", sortable: true, width: 100 },
				{ text: "NIRM", value: "nirm", sortable: true, width: 100 },
				{
					text: "NAMA MAHASISWA",
					value: "nama_mhs",
					sortable: true,
					width: 250,
				},
				{ text: "KELAS", value: "idkelas", sortable: true, width: 120 },
				{ text: "DOSEN WALI", value: "dosen_wali", sortable: true, width: 200 },
				{ text: "AKSI", value: "actions", sortable: false, width: 70 },
			],
			search: "",

			//dialog
			dialogfrm: false,
			dialogdetailitem: false,

			//form data
			form_valid: true,
			is_mhs_checked: false,
			formdata: {
				id: "",
				user_id: "",
				nim: "",
				nama_mhs: "",
			},
			formdefault: {
				id: "",
				user_id: "",
				nim: "",
				nama_mhs: "",
			},

			//form rules
			rule_nim: [
				value => !!value || "Nomor Induk Mahasiswa (NIM) mohon untuk diisi !!!",
				value => /^[0-9]+$/.test(value) || "Nomor Induk Mahasiswa (NIM) hanya boleh angka",
			],
		}),
		methods: {
			changeTahunAkademik(tahun) {
				this.tahun_akademik = tahun;
			},
			changeSemesterAkademik(semester) {
				this.semester_akademik = semester;
			},
			changeProdi(id) {
				this.prodi_id = id;
			},
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/akademik/dulang/mhslulus",
						{
							prodi_id: this.prodi_id,
							ta: this.tahun_akademik,
							idsmt: this.semester_akademik,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.mahasiswa;
						this.datatableLoading = false;
					})
					.catch(() => {
						this.datatableLoading = false;
					});
			},
			dataTableRowClicked(item) {
				if (item === this.expanded[0]) {
					this.expanded = [];
				} else {
					this.expanded = [item];
				}
			},
			addItem() {
				this.dialogfrm = true;
			},
			async cekNIM() {
				if (this.formdata.nim.length > 0) {
					let nim = this.formdata.nim;
					await this.$ajax
						.post(
							"/akademik/dulang/mhslulus/cek",
							{
								nim: nim,
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								},
							}
						)
						.then(({ data }) => {
							this.is_mhs_checked = true;
							this.formdata.user_id = data.profil.user_id;
							this.formdata.nama_mhs = data.profil.nama_mhs;
						})
						.catch(() => {
							this.btnLoading = false;
							this.formdata = Object.assign({}, this.formdefault);
							this.formdata.nim = nim;
							this.is_mhs_checked = false;
						});
				}
			},
			save: async function() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					await this.$ajax
						.post(
							"/akademik/dulang/mhslulus/store",
							{
								user_id: this.formdata.user_id,
								nim: this.formdata.nim,
								idsmt: this.semester_akademik,
								tahun: this.tahun_akademik,
								descr: this.formdata.descr,
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								},
							}
						)
						.then(({ data }) => {
							this.datatable.push(data.mahasiswa);
							this.closedialogfrm();
							this.btnLoading = false;
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus daftar ulang " + item.nama_mhs + " ?",
						{
							color: "red",
							width: 600,
							desc:
								"proses ini akan mengembalikan mahasiswa ke status sebelumnya dan tidak menghapus data KEUANGAN.",
						}
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoadingTable = true;
							this.$ajax
								.post(
									"/akademik/dulang/mhslulus/" + item.id,
									{
										_method: "DELETE",
									},
									{
										headers: {
											Authorization: this.$store.getters["auth/Token"],
										},
									}
								)
								.then(() => {
									const index = this.datatable.indexOf(item);
									this.datatable.splice(index, 1);
									this.btnLoadingTable = false;
								})
								.catch(() => {
									this.btnLoadingTable = false;
								});
						}
					});
			},
			closedialogfrm() {
				this.dialogfrm = false;
				setTimeout(() => {
					this.formdata = Object.assign({}, this.formdefault);
					this.$refs.frmdata.reset();
				}, 300);
			},
		},
		watch: {
			tahun_akademik() {
				if (!this.firstloading) {
					this.initialize();
				}
			},
			semester_akademik() {
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
			AkademikLayout,
			ModuleHeader,
			Filter6,
		},
	};
</script>
