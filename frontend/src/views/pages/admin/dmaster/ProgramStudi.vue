<template>
	<DataMasterLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-file-tree
			</template>
			<template v-slot:name>
				PROGRAM STUDI
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
					Halaman untuk mengelola nama-nama program studi pada perguruan tinggi.
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
							>
							</v-text-field>
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
						sort-by="nama_prodi"
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
								<v-toolbar-title>DAFTAR PROGRAM STUDI</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
								<v-btn
									color="indigo darken-3"
									small
									elevation="0"
									class="primary"
									@click.stop="tambahItem"
								>
									<v-icon size="21px">mdi-plus-circle</v-icon>
								</v-btn>
								<v-dialog v-model="dialogfrm" max-width="500px" persistent>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card>
											<v-card-title>
												<span class="headline">{{ formTitle }}</span>
											</v-card-title>
											<v-card-text>
												<v-select
													v-model="formdata.kode_fakultas"
													label="FAKULTAS"
													:items="daftar_fakultas"
													item-text="nama_fakultas"
													item-value="kode_fakultas"
													outlined
													:rules="rule_kode_fakultas"
													v-if="
														$store.getters['uifront/getBentukPT'] ==
															'universitas'
													"
												>
												</v-select>
												<v-text-field
													v-model="formdata.id"
													label="KODE PRODI"
													outlined
													:rules="rule_kode_prodi"
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.kode_forlap"
													label="KODE FORLAP"
													outlined
													:rules="rule_kode_forlap"
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.nama_prodi"
													label="NAMA PROGRAM STUDI"
													outlined
													:rules="rule_nama_prodi"
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.konsentrasi"
													label="KONSENTRASI"
													outlined
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.nama_prodi_alias"
													label="NAMA SINGKAT PROGRAM STUDI"
													outlined
													:rules="rule_nama_prodi_alias"
												>
												</v-text-field>
												<v-select
													v-model="jenjang_studi"
													label="JENJANG"
													:items="daftar_jenjang"
													item-text="nama_jenjang"
													item-value="kode_jenjang"
													return-object
													outlined
													:rules="rule_kode_jenjang"
												>
												</v-select>
											</v-card-text>
											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="closedialogfrm"
												>
													BATAL
												</v-btn>
												<v-btn
													color="blue darken-1"
													text
													@click.stop="save"
													:disabled="!form_valid || btnLoading"
												>
													SIMPAN
												</v-btn>
											</v-card-actions>
										</v-card>
									</v-form>
								</v-dialog>
								<v-dialog
									v-if="dialogdetailitem"
									v-model="dialogdetailitem"
									max-width="800px"
									persistent
								>
									<v-card>
										<v-card-title>
											<span class="headline">DETAIL DATA</span>
										</v-card-title>
										<v-card-text>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>KODE PRODI / FORLAP :</v-card-title>
														<v-card-subtitle>
															{{ formdata.id }} /
															{{ formdata.kode_forlap }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>JENJANG :</v-card-title>
														<v-card-subtitle>
															{{ formdata.kode_jenjang }} /
															{{ formdata.nama_jenjang }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
											</v-row>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>NAMA PRODI :</v-card-title>
														<v-card-subtitle>
															{{ formdata.nama_prodi }}&nbsp; ({{
																formdata.nama_prodi_alias
															}})
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>KETUA PRODI :</v-card-title>
														<v-card-subtitle>
															{{ kaprodi(formdata) }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
											</v-row>
											<v-row no-gutters>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>KONSENTRASI :</v-card-title>
														<v-card-subtitle>
															{{ formdata.konsentrasi }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>CREATED/UPDATED :</v-card-title>
														<v-card-subtitle>
															{{
																$date(formdata.created_at).format(
																	"DD/MM/YYYY HH:mm"
																)
															}}
															/
															{{
																$date(formdata.updated_at).format(
																	"DD/MM/YYYY HH:mm"
																)
															}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
											</v-row>
											<v-row>
												<v-col cols="12">
													<v-card flat>
														<v-card-text>
															<v-autocomplete
																label="KETUA PROGRAM STUDI"
																v-model="dosen_id"
																:items="daftar_dosen"
																item-text="nama_dosen"
																item-value="id"
																return-object
																:disabled="btnLoading"
																outlined
															/>
														</v-card-text>
													</v-card>
												</v-col>
											</v-row>
										</v-card-text>
										<v-card-actions>
											<v-spacer></v-spacer>
											<v-btn
												color="blue darken-1"
												text
												@click.stop="closedialogdetailitem"
											>
												KELUAR
											</v-btn>
										</v-card-actions>
									</v-card>
								</v-dialog>
							</v-toolbar>
						</template>
						<template v-slot:item.nama_prodi="{ item }">
							{{ item.nama_prodi }} ({{ item.nama_prodi_alias }})
						</template>
						<template v-slot:item.config="{ item }">
							{{ kaprodi(item) }}
						</template>
						<template v-slot:item.actions="{ item }">
							<v-icon small class="mr-2" @click.stop="viewItem(item)">
								mdi-eye
							</v-icon>
							<v-icon small class="mr-2" @click.stop="editItem(item)">
								mdi-pencil
							</v-icon>
							<v-icon
								small
								
								:disabled="btnLoading"
								@click.stop="deleteItem(item)"
							>
								mdi-delete
							</v-icon>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="12">
									<strong>ID: </strong> {{ item.id }}
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
	</DataMasterLayout>
</template>
<script>
	import { mapGetters } from "vuex";
	import DataMasterLayout from "@/views/layouts/DataMasterLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "ProgramStudi",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.ACCESS_TOKEN,
				},
				{
					text: "DATA MASTER",
					disabled: false,
					href: "#",
				},
				{
					text: "PROGRAM STUDI",
					disabled: true,
					href: "#",
				},
			];
			this.initialize();
		},
		data: () => ({
			btnLoading: false,
			datatableLoading: false,
			expanded: [],
			datatable: [],
			search: "",
			firstloading: true,

			//dialog
			dialogfrm: false,
			dialogdetailitem: false,

			//form data
			form_valid: true,
			daftar_fakultas: [],

			daftar_jenjang: [],
			jenjang_studi: null,
			kode_forlap: "",
			old_kode_prodi: 0,
			formdata: {
				id: "",
				kode_fakultas: "",
				kode_forlap: "",
				nama_prodi: "",
				nama_prodi_alias: "",
				konsentrasi: "",
				kode_jenjang: "",
				nama_jenjang: "",
				config: {},
			},
			formdefault: {
				id: "",
				kode_fakultas: "",
				kode_forlap: "",
				nama_prodi: "",
				nama_prodi_alias: "",
				konsentrasi: "",
				kode_jenjang: "",
				nama_jenjang: "",
				config: {},
			},
			dosen_id: null,
			daftar_dosen: [],
			editedIndex: -1,

			//form rules
			rule_kode_fakultas: [
				value => !!value || "Mohon fakultas untuk dipilih !!!",
			],
			rule_kode_prodi: [
				value => !!value || "Kode Program Studi mohon untuk diisi !!!",
				value =>
					/^[1-9]{1}[0-9]{1,14}$/.test(value) ||
					"Kode Program Studi hanya boleh angka",
			],
			rule_kode_forlap: [
				value => !!value || "Kode FORLAP mohon untuk diisi !!!",
				value =>
					/^[1-9]{1}[0-9]{1,14}$/.test(value) ||
					"Kode FORLAP hanya boleh angka",
			],
			rule_nama_prodi: [
				value => !!value || "Mohon Nama Program Studi untuk diisi !!!",
				value =>
					/^[A-Za-z\s]*$/.test(value) ||
					"Nama Program Studi hanya boleh string dan spasi",
			],
			rule_nama_prodi_alias: [
				value => !!value || "Mohon Nama Singkat Program Studi untuk diisi !!!",
				value =>
					/^[A-Za-z\s]*$/.test(value) ||
					"Nama Singkat Program Studi hanya boleh string dan spasi",
			],
			rule_kode_jenjang: [
				value => !!value || "Mohon Jenjang Studi untuk dipilih !!!",
			],
		}),
		methods: {
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.get("/datamaster/programstudi", {
						headers: {
							Authorization: this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.datatable = data.prodi;
						this.datatableLoading = false;
					})
					.catch(() => {
						this.datatableLoading = false;
					});
			},
			kaprodi(item) {
				var message = "N.A";
				if (item.config) {
					var config = JSON.parse(item.config);
					if (config.kaprodi) {
						message = config.kaprodi.name;
					}
				}
				return message;
			},
			dataTableRowClicked(item) {
				if (item === this.expanded[0]) {
					this.expanded = [];
				} else {
					this.expanded = [item];
				}
			},
			tambahItem: async function() {
				if (this.$store.getters["uifront/getBentukPT"] == "universitas") {
					await this.$ajax.get("/datamaster/fakultas").then(({ data }) => {
						this.daftar_fakultas = data.fakultas;
					});
				}
				await this.$ajax
					.get("/datamaster/programstudi/jenjangstudi")
					.then(({ data }) => {
						this.daftar_jenjang = data.jenjangstudi;
					});
				this.dialogfrm = true;
			},
			async viewItem(item) {
				this.datatableLoading = true;
				await this.$ajax
					.get("/system/usersdosen", {
						headers: {
							Authorization: this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.daftar_dosen = data.users;
						this.dosen_id = item.config;
						if (item.config) {
							var config = JSON.parse(item.config);
							this.dosen_id = config.kaprodi;
						}
						this.datatableLoading = false;
						this.formdata = item;
						this.dialogdetailitem = true;
					});
				this.firstloading = false;
			},
			editItem: async function(item) {
				this.editedIndex = this.datatable.indexOf(item);
				this.old_kode_prodi = item.id;
				this.formdata = Object.assign({}, item);

				if (this.$store.getters["uifront/getBentukPT"] == "universitas") {
					await this.$ajax.get("/datamaster/fakultas").then(({ data }) => {
						this.daftar_fakultas = data.fakultas;
						this.formdata.kode_fakultas = item.kode_fakultas;
					});
				}
				await this.$ajax
					.get("/datamaster/programstudi/jenjangstudi")
					.then(({ data }) => {
						this.daftar_jenjang = data.jenjangstudi;
					});
				this.jenjang_studi = {
					kode_jenjang: item.kode_jenjang,
					nama_jenjang: item.nama_jenjang,
				};
				this.dialogfrm = true;
			},
			save: async function() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					if (this.editedIndex > -1) {
						await this.$ajax
							.post(
								"/datamaster/programstudi/" + this.old_kode_prodi,
								{
									_method: "PUT",
									kode_fakultas: this.formdata.kode_fakultas,
									id: this.formdata.id,
									kode_forlap: this.formdata.kode_forlap,
									nama_prodi: this.formdata.nama_prodi,
									nama_prodi_alias: this.formdata.nama_prodi_alias,
									konsentrasi: this.formdata.konsentrasi,
									kode_jenjang: this.jenjang_studi.kode_jenjang,
									nama_jenjang: this.jenjang_studi.nama_jenjang,
								},
								{
									headers: {
										Authorization: this.TOKEN,
									},
								}
							)
							.then(() => {
								this.initialize();
								this.btnLoading = false;
								this.closedialogfrm();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					} else {
						await this.$ajax
							.post(
								"/datamaster/programstudi/store",
								{
									kode_fakultas: this.formdata.kode_fakultas,
									id: this.formdata.id,
									kode_forlap: this.formdata.kode_forlap,
									nama_prodi: this.formdata.nama_prodi,
									nama_prodi_alias: this.formdata.nama_prodi_alias,
									konsentrasi: this.formdata.konsentrasi,
									kode_jenjang: this.jenjang_studi.kode_jenjang,
									nama_jenjang: this.jenjang_studi.nama_jenjang,
								},
								{
									headers: {
										Authorization: this.TOKEN,
									},
								}
							)
							.then(() => {
								this.initialize();
								this.btnLoading = false;
								this.closedialogfrm();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					}
				}
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus data program studi dengan kode " +
							item.kode_forlap +
							" ?",
						{ color: "red" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/datamaster/programstudi/" + item.id,
									{
										_method: "DELETE",
									},
									{
										headers: {
											Authorization: this.TOKEN,
										},
									}
								)
								.then(() => {
									const index = this.datatable.indexOf(item);
									this.datatable.splice(index, 1);
									this.btnLoading = false;
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
			closedialogdetailitem() {
				this.dialogdetailitem = false;
				setTimeout(() => {
					this.old_kode_prodi = 0;
					this.formdata = Object.assign({}, this.formdefault);
					this.editedIndex = -1;
					this.firstloading = true;
				}, 300);
			},
			closedialogfrm() {
				this.dialogfrm = false;
				setTimeout(() => {
					this.old_kode_prodi = 0;
					this.formdata = Object.assign({}, this.formdefault);
					this.$refs.frmdata.reset();
					this.editedIndex = -1;
				}, 300);
			},
		},
		computed: {
			...mapGetters("auth", {
				ACCESS_TOKEN: "AccessToken",
				TOKEN: "Token",
			}),
			formTitle() {
				return this.editedIndex === -1 ? "TAMBAH DATA" : "UBAH DATA";
			},
			headers() {
				if (this.$store.getters["uifront/getBentukPT"] == "universitas") {
					return [
						{ text: "KODE FORLAP", value: "kode_forlap", width: 150 },
						{ text: "NAMA PRODI", value: "nama_prodi", width: 280 },
						{ text: "KONSENTRASI", value: "konsentrasi", width: 280 },
						{ text: "FAKULTAS", value: "nama_fakultas", width: 200 },
						{ text: "JENJANG", value: "nama_jenjang", width: 120 },
						{ text: "KETUA PRODI", value: "config", width: 200 },
						{ text: "AKSI", value: "actions", sortable: false, width: 100 },
					];
				} else {
					return [
						{ text: "KODE FORLAP", value: "kode_forlap", width: 150 },
						{ text: "NAMA PRODI", value: "nama_prodi", width: 280 },
						{ text: "KOSENTRASI", value: "konsentrasi", width: 280 },
						{ text: "JENJANG", value: "nama_jenjang", width: 120 },
						{ text: "KETUA PRODI", value: "config", width: 200 },
						{ text: "AKSI", value: "actions", sortable: false, width: 100 },
					];
				}
			},
		},
		watch: {
			async dosen_id(val) {
				if (!this.firstloading) {
					this.btnLoading = true;
					await this.$ajax
						.post(
							"/datamaster/programstudi/updateconfig/" + this.formdata.id,
							{
								_method: "PUT",
								config: JSON.stringify({
									kaprodi: val,
								}),
							},
							{
								headers: {
									Authorization: this.TOKEN,
								},
							}
						)
						.then(() => {
							this.initialize();
							this.btnLoading = false;
						})
						.catch(() => {
							this.btnLoading = false;
						});
				}
			},
		},
		components: {
			DataMasterLayout,
			ModuleHeader,
		},
	};
</script>
