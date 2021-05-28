<template>
	<AkademikLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-book
			</template>
			<template v-slot:name>
				MATAKULIAH
			</template>
			<template v-slot:subtitle>PROGRAM STUDI {{ nama_prodi }}</template>
			<template v-slot:breadcrumbs>
				<v-breadcrumbs :items="breadcrumbs" class="pa-0">
					<template v-slot:divider>
						<v-icon>mdi-chevron-right</v-icon>
					</template>
				</v-breadcrumbs>
			</template>
			<template v-slot:desc>
				<v-alert color="cyan" border="left" colored-border type="info">
					Halaman untuk mengelola nama-nama matakuliah program studi setiap
					angkatannya.
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid>
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-title>
							FILTER
						</v-card-title>
						<v-card-text>
							<v-select
								v-model="filter_semester"
								label="SEMESTER"
								:items="daftar_semester"
								outlined
							>
							</v-select>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
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
						:disable-pagination="true"
						:hide-default-footer="true"
						@click:row="dataTableRowClicked"
						class="elevation-1"
						:loading="datatableLoading"
						loading-text="Loading... Please wait"
					>
						<template v-slot:top>
							<v-toolbar flat color="white">
								<v-toolbar-title>DAFTAR MATAKULIAH</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
								<v-btn
									color="indigo darken-3"
									small
									elevation="0"
									class="primary"
									@click.stop="tambahItem"
									v-if="$store.getters['auth/can']('AKADEMIK-MATAKULIAH_STORE')"
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
													v-model="formdata.id_group"
													label="KELOMPOK MATAKULIAH"
													:items="group_matakuliah"
													item-text="group_alias"
													item-value="id_group"
													outlined
													:rules="rule_group_matakuliah"
													v-if="group_matakuliah && group_matakuliah.length > 0"
												>
												</v-select>
												<v-text-field
													v-model="formdata.kmatkul"
													label="KODE MATAKULIAH"
													outlined
													:rules="rule_kode_matkul"
												>
												</v-text-field>
												<v-text-field
													v-model="formdata.nmatkul"
													label="NAMA MATAKULIAH"
													outlined
													:rules="rule_nama_matakuliah"
												>
												</v-text-field>
												<v-select
													v-model="formdata.sks"
													label="SKS"
													:items="daftar_sks"
													outlined
													:rules="rule_sks"
												>
												</v-select>
												<v-select
													v-model="formdata.sks_tatap_muka"
													label="SKS TATAP MUKA"
													:items="daftar_sks"
													outlined
													:rules="rule_sks_tatap_muka"
												>
												</v-select>
												<v-select
													v-model="formdata.sks_praktikum"
													label="SKS PRAKTIKUM"
													:clearable="true"
													:items="daftar_sks"
													outlined
												>
												</v-select>
												<v-select
													v-model="formdata.sks_praktik_lapangan"
													label="SKS PRAKTIK LAPANGAN"
													:items="daftar_sks"
													:clearable="true"
													outlined
												>
												</v-select>
												<v-select
													v-model="formdata.semester"
													label="SEMESTER"
													:items="daftar_semester"
													outlined
													:rules="rule_semester"
												>
												</v-select>
												<v-select
													v-model="formdata.minimal_nilai"
													label="MIMIMAL NILAI"
													:items="daftar_nilai"
													outlined
													:rules="rule_minimal_nilai"
												>
												</v-select>
												<v-select
													v-model="formdata.idkonsentrasi"
													label="KONSENTRASI"
													:items="group_matakuliah"
													item-text="nama_konsentrasi"
													item-value="id_konsentrasi"
													outlined
													v-if="
														daftar_konsentrasi && daftar_konsentrasi.length > 0
													"
												>
												</v-select>
												<v-switch
													v-model="formdata.ispilihan"
													label="MATAKULIAH PILIHAN"
												>
												</v-switch>
												<v-switch
													v-model="formdata.islintas_prodi"
													label="MATAKULIAH LINTAS PROGRAM STUDI"
												>
												</v-switch>
												<v-switch
													v-model="formdata.syarat_skripsi"
													label="SYARAT SKRIPSI"
												>
												</v-switch>
												<v-switch v-model="formdata.status" label="STATUS">
												</v-switch>
												<v-switch
													v-model="formdata.update_penyelenggaraan"
													label="UPDATE PENYELENGGARAAN"
													v-if="editedIndex > -1"
												>
												</v-switch>
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
									v-model="dialogdetailitem"
									max-width="750px"
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
														<v-card-title>ID :</v-card-title>
														<v-card-subtitle>
															{{ formdata.id }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>SKS :</v-card-title>
														<v-card-subtitle>
															{{ formdata.sks }}
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
														<v-card-title>KODE MATAKULIAH :</v-card-title>
														<v-card-subtitle>
															{{ formdata.kmatkul }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>SKS TATAP MUKA :</v-card-title>
														<v-card-subtitle>
															{{ formdata.sks_tatap_muka }}
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
														<v-card-title>NAMA MATAKULIAH :</v-card-title>
														<v-card-subtitle>
															{{ formdata.nmatkul }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>SKS PRAKTIKUM :</v-card-title>
														<v-card-subtitle>
															{{ formdata.sks_praktikum2 }}
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
														<v-card-title>SEMESTER :</v-card-title>
														<v-card-subtitle>
															{{ formdata.semester }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>SKS PRAKTIK LAPANGAN :</v-card-title>
														<v-card-subtitle>
															{{ formdata.sks_praktik_lapangan2 }}
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
														<v-card-title>MINIMAL NILAI :</v-card-title>
														<v-card-subtitle>
															{{ formdata.minimal_nilai }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>SYARAT SKRIPSI :</v-card-title>
														<v-card-subtitle>
															{{
																formdata.syarat_skripsi == 1 ? "YA" : "TIDAK"
															}}
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
														<v-card-title>PRODI :</v-card-title>
														<v-card-subtitle>
															{{ formdata.nama_prodi }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>STATUS :</v-card-title>
														<v-card-subtitle>
															{{ formdata.status == 1 ? "AKTIF" : "NON-AKTIF" }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive
													width="100%"
													v-if="$vuetify.breakpoint.xsOnly"
												/>
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
									<strong>ID:</strong>{{ item.id }}
									<strong>created_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong>
									{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
								</v-col>
							</td>
						</template>
						<template v-slot:body.append v-if="datatable.length > 0">
							<tr class="grey lighten-4 font-weight-black">
								<td class="text-right" colspan="4">TOTAL</td>
								<td class="text-center">{{ totalSKS }}</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</template>
						<template v-slot:no-data>
							Data belum tersedia
						</template>
					</v-data-table>
				</v-col>
			</v-row>
			<v-row>
				<v-col cols="12">
					<v-alert>
						<ul>
							<li v-for="item in group_matakuliah" v-bind:key="item.id_group">
								{{ item.group_alias }} - {{ item.nama_group }}
							</li>
						</ul>
					</v-alert>
				</v-col>
			</v-row>
		</v-container>
		<template v-slot:filtersidebar>
			<Filter4 v-on:changeProdi="changeProdi" ref="Filter4" />
		</template>
	</AkademikLayout>
</template>
<script>
	import { mapGetters } from "vuex";
	import AkademikLayout from "@/views/layouts/AkademikLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	import Filter4 from "@/components/sidebar/FilterMode4";
	export default {
		name: "Matakuliah",
		created() {
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.ACCESS_TOKEN,
				},
				{
					text: "AKADEMIK",
					disabled: false,
					href: "/akademik",
				},
				{
					text: "PERKULIAHAN",
					disabled: false,
					href: "#",
				},
				{
					text: "MATAKULIAH",
					disabled: true,
					href: "#",
				},
			];
			let prodi_id = this.$store.getters["uiadmin/getProdiID"];
			this.prodi_id = prodi_id;
			this.nama_prodi = this.$store.getters["uiadmin/getProdiName"](prodi_id);
			this.initialize();
		},
		data: () => ({
			firstloading: true,
			prodi_id: null,
			nama_prodi: null,

			btnLoading: false,
			datatableLoading: false,
			expanded: [],
			datatable: [],
			headers: [
				{ text: "KODE", value: "kmatkul", sortable: true, width: 120 },
				{ text: "NAMA MATAKULIAH", value: "nmatkul", sortable: true },
				{ text: "KELOMPOK", value: "group_alias", sortable: true, width: 120 },
				{
					text: "SKS",
					value: "sks",
					sortable: true,
					width: 80,
					align: "center",
				},
				{ text: "SMT", value: "semester", sortable: true, width: 80 },
				{
					text: "JUMLAH PENYELENGGARAAN",
					value: "jummlah_penyelenggaraan",
					sortable: true,
					width: 100,
				},
				{ text: "AKSI", value: "actions", sortable: false, width: 100 },
			],
			search: "",
			filter_semester: null,

			//dialog
			dialogfrm: false,
			dialogdetailitem: false,

			//form data
			form_valid: true,
			daftar_ta: [],
			group_matakuliah: [],
			daftar_konsentrasi: [],
			daftar_semester: [1, 2, 3, 4, 5, 6, 7, 8],
			daftar_sks: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
			daftar_nilai: [
				"A",
				"A-",
				"A/B",
				"(B+)",
				"B",
				"B-",
				"B/C",
				"(C+)",
				"C",
				"C-",
				"C/D",
				"(D+)",
				"D",
				"E",
			],
			formdata: {
				id: "",
				id_group: null,
				nama_group: null,
				group_alias: null,
				kmatkul: "",
				nmatkul: "",
				sks: "",
				idkonsentrasi: null,
				ispilihan: false,
				islintas_prodi: false,
				semester: "",
				sks_tatap_muka: "",
				sks_praktikum: null,
				sks_praktik_lapangan: null,
				minimal_nilai: "C",
				syarat_skripsi: true,
				status: true,
				kjur: "",
				update_penyelenggaraan: false,
			},
			formdefault: {
				id: "",
				id_group: null,
				nama_group: null,
				group_alias: null,
				kmatkul: "",
				nmatkul: "",
				sks: "",
				idkonsentrasi: null,
				ispilihan: false,
				islintas_prodi: false,
				semester: "",
				sks_tatap_muka: "",
				sks_praktikum: null,
				sks_praktik_lapangan: null,
				minimal_nilai: "C",
				syarat_skripsi: true,
				status: true,
				kjur: "",
				update_penyelenggaraan: false,
			},
			editedIndex: -1,

			//form rules
			rule_group_matakuliah: [
				value => !!value || "Mohon Group Matakuliah untuk dipilih !!!",
			],
			rule_kode_matkul: [
				value => !!value || "Kode Program Studi mohon untuk diisi !!!",
			],
			rule_nama_matakuliah: [
				value => !!value || "Mohon Nama Program Studi untuk diisi !!!",
			],
			rule_sks: [value => !!value || "Mohon SKS Matakuliah untuk dipilih !!!"],
			rule_sks_tatap_muka: [
				value => !!value || "Mohon SKS Matakuliah Tatap Muka untuk dipilih !!!",
			],
			rule_semester: [
				value =>
					!!value ||
					"Mohon Semester Matakuliah ini diselenggarakan untuk dipilih !!!",
			],
			rule_minimal_nilai: [
				value =>
					!!value ||
					"Mohon Minimal nilai kelulusan matakuliah untuk dipilih !!!",
			],
		}),
		methods: {
			changeProdi(id) {
				this.prodi_id = id;
			},
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/akademik/matakuliah",
						{
							prodi_id: this.prodi_id,
						},
						{
							headers: {
								Authorization: this.TOKEN,
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.matakuliah;
						this.datatableLoading = false;
					})
					.catch(() => {
						this.datatableLoading = false;
					});
				await this.$ajax
					.get("/akademik/groupmatakuliah", {
						headers: {
							Authorization: this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.group_matakuliah = data.group_matakuliah;
					});
				this.firstloading = false;
				this.$refs.Filter4.setFirstTimeLoading(this.firstloading);
			},
			fetchDataBySemester(val) {
				this.datatableLoading = true;
				this.$ajax
					.post(
						"/akademik/matakuliah",
						{
							prodi_id: this.prodi_id,
							filter_semester: val,
						},
						{
							headers: {
								Authorization: this.TOKEN,
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.matakuliah;
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
			tambahItem: async function() {
				this.dialogfrm = true;
			},
			async viewItem(item) {
				this.formdata = item;
				await this.$ajax
					.get("/akademik/matakuliah/" + item.id, {
						headers: {
							Authorization: this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.formdata = data.matakuliah;
					});
				this.dialogdetailitem = true;
			},
			editItem: async function(item) {
				this.editedIndex = this.datatable.indexOf(item);
				await this.$ajax
					.get("/akademik/groupmatakuliah", {
						headers: {
							Authorization: this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.group_matakuliah = data.group_matakuliah;
					});

				await this.$ajax
					.get("/akademik/matakuliah/" + item.id, {
						headers: {
							Authorization: this.TOKEN,
						},
					})
					.then(({ data }) => {
						this.formdata = data.matakuliah;
					});
				this.dialogfrm = true;
			},
			save: async function() {
				if (this.$refs.frmdata.validate()) {
					this.btnLoading = true;
					if (this.editedIndex > -1) {
						await this.$ajax
							.post(
								"/akademik/matakuliah/" + this.formdata.id,
								{
									_method: "PUT",
									id_group: this.formdata.id_group,
									nama_group: this.formdata.nama_group,
									group_alias: this.formdata.group_alias,
									kmatkul: this.formdata.kmatkul,
									nmatkul: this.formdata.nmatkul,
									sks: this.formdata.sks,
									idkonsentrasi: this.formdata.idkonsentrasi,
									ispilihan: this.formdata.ispilihan,
									islintas_prodi: this.formdata.islintas_prodi,
									semester: this.formdata.semester,
									sks_tatap_muka: this.formdata.sks_tatap_muka,
									sks_praktikum: this.formdata.sks_praktikum,
									sks_praktik_lapangan: this.formdata.sks_praktik_lapangan,
									minimal_nilai: this.formdata.minimal_nilai,
									syarat_skripsi: this.formdata.syarat_skripsi,
									status: this.formdata.status,
									kjur: this.formdata.kjur,
									update_penyelenggaraan: this.formdata.update_penyelenggaraan,
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
								"/akademik/matakuliah/store",
								{
									id_group: this.formdata.id_group,
									nama_group: this.formdata.nama_group,
									group_alias: this.formdata.group_alias,
									kmatkul: this.formdata.kmatkul,
									nmatkul: this.formdata.nmatkul,
									sks: this.formdata.sks,
									idkonsentrasi: this.formdata.idkonsentrasi,
									ispilihan: this.formdata.ispilihan,
									islintas_prodi: this.formdata.islintas_prodi,
									semester: this.formdata.semester,
									sks_tatap_muka: this.formdata.sks_tatap_muka,
									sks_praktikum: this.formdata.sks_praktikum,
									sks_praktik_lapangan: this.formdata.sks_praktik_lapangan,
									minimal_nilai: this.formdata.minimal_nilai,
									syarat_skripsi: this.formdata.syarat_skripsi,
									status: this.formdata.status,
									kjur: this.prodi_id,
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
						"Apakah Anda ingin menghapus matakuliah " + item.nmatkul + " ?",
						{ color: "red" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/akademik/matakuliah/" + item.id,
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
					this.formdata = Object.assign({}, this.formdefault);
					this.editedIndex = -1;
				}, 300);
			},
			closedialogfrm() {
				this.dialogfrm = false;
				setTimeout(() => {
					this.$refs.frmdata.resetValidation();
					this.formdata = Object.assign({}, this.formdefault);
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
			totalSKS() {
				var total = 0;
				for (var i = 0; i < this.datatable.length; i++) {
					var item = this.datatable[i];
					total = total + parseInt(item.sks);
				}
				return total;
			},
		},
		watch: {
			prodi_id(val) {
				if (!this.firstloading) {
					this.nama_prodi = this.$store.getters["uiadmin/getProdiName"](val);
					this.initialize();
				}
			},
			filter_semester(val) {
				if (!this.firstloading) {
					if (val > 0) {
						this.fetchDataBySemester(val);
					} else {
						this.initialize();
					}
				}
			},
		},
		components: {
			AkademikLayout,
			ModuleHeader,
			Filter4,
		},
	};
</script>
