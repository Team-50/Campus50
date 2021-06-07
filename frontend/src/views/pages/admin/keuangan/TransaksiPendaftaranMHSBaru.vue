<template>
	<KeuanganLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-video-input-component
			</template>
			<template v-slot:name>
				TRANSAKSI PENDAFTARAN MAHASISWA BARU
			</template>
			<template v-slot:subtitle>
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
				<v-alert color="cyan" border="left" colored-border type="info">
					Halaman ini digunakan untuk mengelola transaksi pendaftaran mahasiswa
					baru.
				</v-alert>
			</template>
		</ModuleHeader>
		<template v-slot:filtersidebar>
			<Filter7
				v-on:changeTahunPendaftaran="changeTahunPendaftaran"
				v-on:changeProdi="changeProdi"
				ref="filter7"
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
							>
							</v-text-field>
							<v-switch
								v-model="filter_ignore"
								label="ABAIKAN FILTER"
								class="font-weight-bold"
							>
							</v-switch>
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
						sort-by="nama_mhs"
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
								<v-toolbar-title>DAFTAR TRANSAKSI</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
								<v-btn
									color="primary"
									icon
									outlined
									small
									class="ma-2"
									@click.stop="addItem"
								>
									<v-icon>mdi-plus</v-icon>
								</v-btn>
								<v-btn
									color="primary"
									icon
									outlined
									small
									class="ma-2"
									@click.stop="showDialogPrintout"
								>
									<v-icon>mdi-printer</v-icon>
								</v-btn>
								<v-dialog v-model="dialogfrm" max-width="500px" persistent>
									<v-form ref="frmdata" v-model="form_valid" lazy-validation>
										<v-card outlined>
											<v-list-item three-line>
												<v-list-item-content>
													<div class="overline mb-1">
														TAMBAH TRANSAKSI
													</div>
													<v-list-item-subtitle>
														Masukan calon mahasiswa baru Prodi
														{{ nama_prodi }} T.A {{ tahun_pendaftaran }}
														<v-autocomplete
															v-model="data_mhs"
															:items="entries"
															:loading="isLoading"
															:search-input.sync="search_data_mhs"
															cache-items
															dense
															item-text="name"
															item-value="id"
															hide-no-data
															hide-details
															prepend-icon="mdi-database-search"
															return-object
															ref="ref_data_mhs"
														></v-autocomplete>
													</v-list-item-subtitle>
												</v-list-item-content>
											</v-list-item>
											<v-divider></v-divider>
											<v-expand-transition>
												<v-list v-if="data_mhs">
													<template v-for="(field, i) in fields">
														<v-list-item :key="i" v-if="field.key != 'name'">
															<v-list-item-content>
																<v-list-item-title>
																	{{ field.value }}
																</v-list-item-title>
																<v-list-item-subtitle>
																	<strong>{{ field_alias(field.key) }}</strong>
																</v-list-item-subtitle>
															</v-list-item-content>
														</v-list-item>
													</template>
												</v-list>
											</v-expand-transition>
											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn :disabled="!data_mhs" @click="save">
													Buat Transaksi
													<v-icon right>
														mdi-forward
													</v-icon>
												</v-btn>
												<v-btn @click="closedialogfrm">
													Close
													<v-icon right>
														mdi-close-circle
													</v-icon>
												</v-btn>
											</v-card-actions>
										</v-card>
									</v-form>
								</v-dialog>
								<v-dialog v-model="dialogeditfrm" max-width="750px" persistent>
									<v-form
										ref="frmeditdata"
										v-model="form_valid"
										lazy-validation
									>
										<v-card outlined>
											<v-card-title>
												UBAH TRANSAKSI
											</v-card-title>
											<v-card-text>
												<v-row no-gutters>
													<v-col xs="12" sm="6" md="6">
														<v-card flat>
															<v-card-title>ID :</v-card-title>
															<v-card-subtitle>
																{{ data_transaksi.id }}
															</v-card-subtitle>
														</v-card>
													</v-col>
													<v-responsive
														width="100%"
														v-if="$vuetify.breakpoint.xsOnly"
													/>
													<v-col xs="12" sm="6" md="6">
														<v-card flat>
															<v-card-title>KODE BILLING :</v-card-title>
															<v-card-subtitle>
																{{ data_transaksi.no_transaksi }}
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
															<v-card-title>NAMA MAHASISWA :</v-card-title>
															<v-card-subtitle>
																{{ data_transaksi.nama_mhs }}
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
																<v-chip :color="data_transaksi.style" dark>
																	{{ data_transaksi.nama_status }}
																</v-chip>
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
															<v-card-title>BIAYA PENDAFTARAN :</v-card-title>
															<v-card-subtitle>
																{{ data_transaksi.biaya | formatUang }}
															</v-card-subtitle>
														</v-card>
													</v-col>
													<v-responsive
														width="100%"
														v-if="$vuetify.breakpoint.xsOnly"
													/>
													<v-col xs="12" sm="6" md="6">
														<v-card flat>
															<v-card-title>TOTAL :</v-card-title>
															<v-card-subtitle>
																{{ data_transaksi.total | formatUang }}
															</v-card-subtitle>
														</v-card>
													</v-col>
													<v-responsive
														width="100%"
														v-if="$vuetify.breakpoint.xsOnly"
													/>
												</v-row>
												<v-row no-gutters>
													<v-col cols="12">
														<v-card>
															<v-card-text>
																<v-currency-field
																	label="POTONGAN :"
																	:min="null"
																	:max="null"
																	outlined
																	v-model="formdata.promovalue"
																>
																</v-currency-field>
																<v-textarea
																	v-model="formdata.desc"
																	label="CATATAN:"
																	outlined
																/>
															</v-card-text>
														</v-card>
													</v-col>
												</v-row>
											</v-card-text>
											<v-card-actions>
												<v-spacer></v-spacer>
												<v-btn @click.stop="save">
													SIMPAN
													<v-icon right>
														mdi-content-save
													</v-icon>
												</v-btn>
												<v-btn @click="closedialogeditfrm">
													Close
													<v-icon right>
														mdi-close-circle
													</v-icon>
												</v-btn>
											</v-card-actions>
										</v-card>
									</v-form>
								</v-dialog>
							</v-toolbar>
						</template>
						<template v-slot:item.idsmt="{ item }">
							{{ item.ta }}
							{{ $store.getters["uiadmin/getNamaSemester"](item.idsmt) }}
						</template>
						<template v-slot:item.tanggal="{ item }">
							{{ $date(item.tanggal).format("DD/MM/YYYY") }}
						</template>
						<template v-slot:item.promovalue="{ item }">
							{{ item.promovalue | formatUang }}
						</template>
						<template v-slot:item.sub_total="{ item }">
							{{ item.sub_total | formatUang }}
						</template>
						<template v-slot:item.nama_status="{ item }">
							<v-chip :color="item.style" dark>
								{{ item.nama_status }}
							</v-chip>
						</template>
						<template v-slot:body.append v-if="datatable.length > 0">
							<tr class="grey lighten-4 font-weight-black">
								<td class="text-right" colspan="7">TOTAL TRANSAKSI PAID</td>
								<td class="text-right">
									{{ totaltransaksi_paid | formatUang }}
								</td>
								<td></td>
								<td></td>
							</tr>
							<tr class="grey lighten-4 font-weight-black">
								<td class="text-right" colspan="7">TOTAL TRANSAKSI UNPAID</td>
								<td class="text-right">
									{{ totaltransaksi_unpaid | formatUang }}
								</td>
								<td></td>
								<td></td>
							</tr>
							<tr class="grey lighten-4 font-weight-black">
								<td class="text-right" colspan="7">TOTAL TRANSAKSI CANCELED</td>
								<td class="text-right">
									{{ totaltransaksi_canceled | formatUang }}
								</td>
								<td></td>
								<td></td>
							</tr>
							<tr class="grey lighten-4 font-weight-black">
								<td class="text-right" colspan="7">TOTAL TRANSAKSI</td>
								<td class="text-right">
									{{
										(totaltransaksi_canceled +
											totaltransaksi_paid +
											totaltransaksi_unpaid)
											| formatUang
									}}
								</td>
								<td></td>
								<td></td>
							</tr>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="13">
									<strong>TRANS.DETAIL ID:</strong>{{ item.id }}
									<strong>created_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong>
									{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
								</v-col>
							</td>
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
								v-if="item.status == 0"
							>
								mdi-delete
							</v-icon>
						</template>
						<template v-slot:no-data>
							Data transaksi daftar ulang mahasiswa baru belum tersedia
						</template>
					</v-data-table>
				</v-col>
			</v-row>
		</v-container>
		<dialog-printout
			pid="dulangmhsbaru"
			title="Daftar Ulang Mahasiwa Baru"
			ref="dialogprint"
		>
		</dialog-printout>
	</KeuanganLayout>
</template>
<script>
	import KeuanganLayout from "@/views/layouts/KeuanganLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	import Filter7 from "@/components/sidebar/FilterMode7";
	import DialogPrintoutKeuangan from "@/components/DialogPrintoutKeuangan";
	export default {
		name: "TransaksiPendaftaranMHSBaru",
		created() {
			this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "KEUANGAN",
					disabled: false,
					href: "/keuangan",
				},
				{
					text: "TRANSAKSI PENDAFTARAN MAHASISWA BARU",
					disabled: true,
					href: "#",
				},
			];
			let prodi_id = this.$store.getters["uiadmin/getProdiID"];
			this.prodi_id = prodi_id;
			this.nama_prodi = this.$store.getters["uiadmin/getProdiName"](prodi_id);
			this.tahun_pendaftaran = this.$store.getters[
				"uiadmin/getTahunPendaftaran"
			];
		},
		mounted() {
			this.initialize();
		},
		data: () => ({
			dashboard: null,
			firstloading: true,
			breadcrumbs: [],
			prodi_id: null,
			nama_prodi: null,
			tahun_pendaftaran: 0,
			filter_ignore: false,
			awaiting_search: false,

			btnLoading: false,

			//tables
			datatableLoading: false,
			datatable: [],
			headers: [
				{
					text: "KODE BILLING",
					value: "no_transaksi",
					width: 100,
					sortable: true,
				},
				{
					text: "TANGGAL",
					value: "tanggal",
					width: 90,
					sortable: true,
				},
				{
					text: "NO. FORMULIR",
					value: "no_formulir",
					sortable: true,
					width: 100,
				},
				{
					text: "NAMA MAHASISWA",
					value: "nama_mhs",
					sortable: true,
					width: 250,
				},
				{
					text: "SMT",
					value: "idsmt",
					width: 100,
					sortable: false,
				},
				{
					text: "POTONGAN",
					value: "promovalue",
					width: 100,
					sortable: false,
					align: "right",
				},
				{
					text: "JUMLAH",
					value: "sub_total",
					width: 100,
					sortable: false,
					align: "right",
				},
				{
					text: "STATUS",
					value: "nama_status",
					width: 100,
					sortable: false,
				},
				{
					text: "AKSI",
					value: "actions",
					sortable: false,
					width: 100,
				},
			],
			expanded: [],
			search: "",

			//dialog
			dialogfrm: false,
			dialogeditfrm: false,

			//form data
			entries: [],
			isLoading: false,
			data_mhs: null,
			search_data_mhs: null,

			form_valid: true,
			data_transaksi: {},
			formdata: {
				id: null,
				promovalue: 0,
				desc: "",
			},
			formdefault: {
				id: null,
				promovalue: 0,
				desc: "",
			},
			editedIndex: -1,
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
						"/keuangan/transaksi-pendaftaranmhsbaru",
						{
							TA: this.tahun_pendaftaran,
							PRODI_ID: this.prodi_id,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.transaksi;
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
			async addItem() {
				this.dialogfrm = true;
			},
			async editItem(item) {
				this.editedIndex = this.datatable.indexOf(item);
				this.formdata = Object.assign({}, item);
				this.dialogeditfrm = true;
				this.data_transaksi = item;
			},
			field_alias(atr) {
				var alias;
				switch (atr) {
					case "id":
						alias = "USER ID";
						break;
					case "name":
						alias = "NAMA MAHASIWA";
						break;
					case "username":
						alias = "USERNAME";
						break;
					case "email":
						alias = "EMAIL";
						break;
					case "nomor_hp":
						alias = "NOMOR HP";
						break;
				}
				return alias;
			},
			viewItem(item) {
				this.$router.push(
					"/keuangan/transaksi-pendaftaranmhsbaru/" + item.transaksi_id
				);
			},
			save: async function() {
				this.btnLoading = true;
				if (this.editedIndex > -1) {
					if (this.$refs.frmeditdata.validate()) {
						await this.$ajax
							.post(
								"/keuangan/transaksi-pendaftaranmhsbaru/" + this.formdata.id,
								{
									_method: "put",
									promovalue: this.formdata.promovalue,
									desc: this.formdata.desc,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(() => {
								this.closedialogeditfrm();
								this.btnLoading = false;
								this.initialize();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					}
				} else {
					if (this.$refs.frmdata.validate()) {
						await this.$ajax
							.post(
								"/keuangan/transaksi-pendaftaranmhsbaru/store",
								{
									user_id: this.data_mhs.id,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(() => {
								this.closedialogfrm();
								this.btnLoading = false;
								this.initialize();
							})
							.catch(() => {
								this.btnLoading = false;
							});
					}
				}
			},
			showDialogPrintout() {
				this.$refs.dialogprint.open();
			},
			closedialogfrm() {
				this.dialogfrm = false;
				setTimeout(() => {
					this.data_mhs = null;
					this.$refs.frmdata.reset();
				}, 300);
			},
			closedialogeditfrm() {
				this.dialogeditfrm = false;
				setTimeout(() => {
					this.data_transaksi = {};
					this.$refs.frmeditdata.reset();
				}, 300);
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus data transaksi daftar ulang mahasiswa baru dengan ID " +
							item.id +
							" ?",
						{ color: "red", width: "500px" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/keuangan/transaksi-pendaftaranmhsbaru/" + item.transaksi_id,
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
									this.btnLoading = false;
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
		},
		computed: {
			totaltransaksi_paid() {
				var total = 0;
				this.datatable.forEach(item => {
					if (item.status == 1) {
						total += item.total;
					}
				});
				return total;
			},
			totaltransaksi_unpaid() {
				var total = 0;
				this.datatable.forEach(item => {
					if (item.status == 0) {
						total += item.total;
					}
				});
				return total;
			},
			totaltransaksi_canceled() {
				var total = 0;
				this.datatable.forEach(item => {
					if (item.status == 2) {
						total += item.total;
					}
				});
				return total;
			},
			fields() {
				if (!this.data_mhs) return [];
				return Object.keys(this.data_mhs).map(key => {
					return {
						key,
						value: this.data_mhs[key] || "n/a",
					};
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
			search() {
				if (!this.awaiting_search) {
					setTimeout(async () => {
						if (this.search.length > 0 && this.filter_ignore) {
							this.datatableLoading = true;
							await this.$ajax
								.post(
									"/keuangan/transaksi-dulangmhsbaru",
									{
										PRODI_ID: this.prodi_id,
										TA: this.tahun_pendaftaran,
										search: this.search,
									},
									{
										headers: {
											Authorization: this.$store.getters["auth/Token"],
										},
									}
								)
								.then(({ data }) => {
									this.datatable = data.transaksi;
									this.datatableLoading = false;
								});
						}
						this.awaiting_search = false;
					}, 1000); // 1 sec delay
				}
				this.awaiting_search = true;
			},
			search_data_mhs(val) {
				if (this.isLoading) return;
				if (val && val !== this.data_mhs && val.length > 1) {
					setTimeout(async () => {
						this.isLoading = true;
						await this.$ajax
							.post(
								"/spmb/pmb/search",
								{
									search: val,
									prodi_id: this.prodi_id,
									ta: this.tahun_pendaftaran,
								},
								{
									headers: {
										Authorization: this.$store.getters["auth/Token"],
									},
								}
							)
							.then(({ data }) => {
								const { jumlah, daftar_mhs } = data;
								this.count = jumlah;
								this.entries = daftar_mhs;
								this.isLoading = false;
							})
							.catch(() => {
								this.isLoading = false;
							});
					}, 1000);
				}
			},
		},
		components: {
			KeuanganLayout,
			ModuleHeader,
			Filter7,
			"dialog-printout": DialogPrintoutKeuangan,
		},
	};
</script>
