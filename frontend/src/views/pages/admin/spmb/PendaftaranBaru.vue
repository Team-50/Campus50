<template>
		<SPMBLayout>
				<ModuleHeader>
						<template v-slot:icon>
								mdi-account-plus
						</template>
						<template v-slot:name>
								PENDAFTAR 
						</template>
						<template v-slot:subtitle>
								TAHUN PENDAFTARAN {{tahun_pendaftaran}} - {{nama_prodi}}
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
												Berisi pendaftar baru, silahkan melakukan filter tahun akademik dan program studi. CATATAN: Melakukan perubahan terhadap Prodi, Kelas, dan Tahun Pendaftaran Mahasiswa Baru tidak berpengaruh terhadap Transaksi keuangan yang telah dilakukannya.
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
												loading-text="Loading... Please wait">
												<template v-slot:top>
														<v-toolbar flat color="white">     
																<v-spacer></v-spacer>
																<v-btn 
																		:loading="btnLoading"
																		:disabled="btnLoading"
																		color="warning"
																		small
																		elevation="0"
																		class="mr-2"
																		@click.stop="syncPermission" 
																		v-if="$store.getters['auth/can']('USER_STOREPERMISSIONS')">
																		SYNC
																</v-btn>
																<v-btn
																		color="indigo darken-3"
																		class="primary"
																		small
																		elevation="0"
																		@click.stop="addItem">
																		<v-icon size="21px">mdi-plus-circle</v-icon>
																</v-btn>
																<v-dialog v-model="dialogfrm" max-width="500px" persistent>         
																		<v-form ref="frmdata" v-model="form_valid" lazy-validation>
																				<v-card>
																						<v-card-title>
																								<span class="headline">{{ formTitle }}</span>
																						</v-card-title>
																						<v-card-subtitle>
																								<span class="info--text">
																										Secara default akan tersimpan di prodi <strong>{{nama_prodi}} - {{tahun_pendaftaran}}.</strong>
																										Anda bisa merubahnya dengan memilih PRODI atau Tahun Akademik dibawah ini. Namun bila sudah memiliki NIM, T.A dan PRODI tidak bisa diubah.
																								</span>
																						</v-card-subtitle>
																						<v-card-text>
																								<v-text-field 
																										v-model="formdata.name"
																										label="NAMA LENGKAP" 
																										:rules="rule_name"
																										outlined/>    
																								<v-text-field 
																										v-model="formdata.nomor_hp"
																										label="NOMOR HP (ex: +628123456789)" 
																										:rules="rule_nomorhp"
																										outlined/>    
																								<v-text-field 
																										v-model="formdata.email"
																										label="EMAIL" 
																										:rules="rule_email"
																										outlined/>       
																								<v-select
																										v-model="kode_fakultas"
																										label="FAKULTAS"
																										outlined
																										:rules="rule_fakultas"
																										:items="daftar_fakultas"
																										item-text="nama_fakultas"
																										item-value="kode_fakultas"
																										:disabled="registered"
																										:loading="btnLoadingFakultas"
																										v-if="$store.getters['uifront/getBentukPT']=='universitas'"
																								/>
																								<v-select
																										label="PROGRAM STUDI"
																										v-model="formdata.prodi_id"
																										:items="daftar_prodi"
																										item-text="nama_prodi2"
																										item-value="id"
																										:rules="rule_prodi"
																										:disabled="registered"
																										outlined />
																								<v-select
																										v-model="formdata.ta"
																										:items="daftar_ta"                                                    
																										label="TAHUN PENDAFTARAN"
																										:disabled="registered"
																										outlined/>   
																								<v-text-field 
																										v-model="formdata.username"
																										label="USERNAME" 
																										:rules="rule_username"
																										outlined />   
																								<v-text-field 
																										v-model="formdata.password"
																										label="PASSWORD" 
																										type="password"                                                                             
																										:disabled="registered"                                                                       
																										outlined 
																										v-if="editedIndex>-1" /> 
																								<v-text-field 
																										v-model="formdata.password"
																										label="PASSWORD" 
																										type="password"         
																										:disabled="registered"       
																										:rules="rule_password"                
																										outlined 
																										v-else /> 
																						</v-card-text>
																						<v-card-actions>
																								<v-spacer></v-spacer>
																								<v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
																								<v-btn 
																										color="blue darken-1" 
																										text 
																										@click.stop="save" 
																										:loading="btnLoading"
																										:disabled="!form_valid||btnLoading">
																												SIMPAN
																								</v-btn>
																						</v-card-actions>
																				</v-card>
																		</v-form>
																</v-dialog>
																<v-dialog v-model="dialogdetailitem" max-width="750px" persistent>
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
																														{{formdata.id}}
																												</v-card-subtitle>
																										</v-card>
																								</v-col>
																								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																								<v-col xs="12" sm="6" md="6">
																										<v-card flat>
																												<v-card-title>USERNAME :</v-card-title>
																												<v-card-subtitle>
																														{{formdata.username}}
																												</v-card-subtitle>
																										</v-card>
																								</v-col>
																								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																						</v-row>
																						<v-row no-gutters>
																								<v-col xs="12" sm="6" md="6">
																										<v-card flat>
																												<v-card-title>NAMA MAHASISWA :</v-card-title>
																												<v-card-subtitle>
																														{{formdata.name}}
																												</v-card-subtitle>
																										</v-card>
																								</v-col>
																								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																								<v-col xs="12" sm="6" md="6">
																										<v-card flat>
																												<v-card-title>CREATED/UPDATED :</v-card-title>
																												<v-card-subtitle>
																														{{$date(formdata.created_at).format("DD/MM/YYYY HH:mm")}} /  
																														{{$date(formdata.updated_at).format("DD/MM/YYYY HH:mm")}}
																												</v-card-subtitle>
																										</v-card>
																								</v-col>
																								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																						</v-row>
																						<v-row no-gutters>
																								<v-col cols="12">
																										<v-card>
																												<v-card-title>KIRIM ULANG EMAIL</v-card-title>
																												<v-card-subtitle>
																														Klik tombol berikut ini untuk mengirim ulang email konfirmasi pendaftaran
																												</v-card-subtitle>
																												<v-card-text>
																														<v-btn small color="primary" @click.stop="resend(formdata.id)" class="mb-2" :loading="btnLoading">KIRIM ULANG</v-btn>
																												</v-card-text>
																										</v-card>
																								</v-col>                     
																						</v-row>
																				</v-card-text>
																				<v-card-actions>
																						<v-spacer></v-spacer>
																						<v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">KELUAR</v-btn>
																				</v-card-actions>
																		</v-card>         
																</v-dialog>
														</v-toolbar>
												</template>
												<template v-slot:item.nomor_hp="{ item }">    
														{{ item.nomor_hp == null || item.nomor_hp == ""? "N.A" : "+" + item.nomor_hp}}
												</template>
												<template v-slot:item.actions="{ item }">
														<v-icon
																small
																class="mr-2"
																:loading="btnLoading"
																:disabled="btnLoading"
																@click.stop="viewItem(item)"
														>
																mdi-eye
														</v-icon>
														<v-icon
																small
																class="mr-2"
																:loading="btnLoading"
																:disabled="btnLoading"
																@click.stop="editItem(item)"
														>
																mdi-pencil
														</v-icon>
														<v-icon
																small
																:loading="btnLoading"
																:disabled="btnLoading"
																@click.stop="deleteItem(item)"
														>
																mdi-delete
														</v-icon>
												</template>
												<template v-slot:item.foto="{ item }">    
														<v-badge
																bordered
																:color="badgeColor(item)"
																:icon="badgeIcon(item)"
																overlap>                
																<v-avatar size="30">             
																		<v-img :src="$api.storageURL+'/'+item.foto" />                                          
																</v-avatar>                                                                       
														</v-badge>
												</template>
												<template v-slot:item.created_at="{ item }"> 
														{{$date(item.created_at).format("DD/MM/YYYY HH:mm")}}
												</template>
												<template v-slot:expanded-item="{ headers, item }">
														<td :colspan="headers.length" class="text-center">
																<v-col cols="12">
																		<strong>ID:</strong>{{ item.id }}
																		<strong>created_at:</strong>{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
																		<strong>updated_at:</strong>{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
																</v-col>
																<v-col cols="12" v-if="item.active==0">
																		<v-btn 
																				small 
																				class="primary ma-2" 
																				@click.stop="aktifkan(item.id)"                                         
																				:disabled="btnLoading"
																				:loading="btnLoading">
																						<v-icon>mdi-email-check</v-icon>
																						VERIFIFIKASI EMAIL
																		</v-btn>
																		<v-btn 
																				small 
																				class="primary ma-2" 
																				@click.stop="resend(item.id)"                                         
																				:disabled="btnLoading"
																				:loading="btnLoading">
																						<v-icon>mdi-email-sync</v-icon>
																						KIRIM ULANG KODE AKTIVASI 
																		</v-btn>
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
				<template v-slot:filtersidebar>
						<Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeProdi="changeProdi" ref="filter7" />	
				</template>
		</SPMBLayout>
</template>
<script>
import SPMBLayout from "@/views/layouts/SPMBLayout";
import ModuleHeader from "@/components/ModuleHeader";
import Filter7 from "@/components/sidebar/FilterMode7";
export default {
		name: "PendaftaranBaru",
		created()
		{
				this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
				this.breadcrumbs = [
						{
								text: "HOME",
								disabled:false,
								href: "/dashboard/" + this.$store.getters["auth/AccessToken"]
						},
						{
								text: "SPMB",
								disabled:false,
								href: "/spmb"
						},
						{
								text: "PENDAFTAR BARU",
								disabled:true,
								href: "#"
						}
				];
				this.breadcrumbs[1].disabled=(this.dashboard=="mahasiswabaru"||this.dashboard=="mahasiswa");

				let prodi_id=this.$store.getters["uiadmin/getProdiID"];
				this.prodi_id=prodi_id;
				this.nama_prodi=this.$store.getters["uiadmin/getProdiName"](prodi_id);
				this.tahun_pendaftaran=this.$store.getters["uiadmin/getTahunPendaftaran"];
				this.initialize();
		},
		data: () => ({ 
				firstloading:true,
				prodi_id:null,
				tahun_pendaftaran:null,
				nama_prodi:null,
				
				breadcrumbs: [],
				dashboard:null,
				datatableLoading:false,
				btnLoading: false,
				btnLoadingFakultas:false,
									
				//tables
				headers: [                        
						{ text: "", value: "foto", width:70 },
						{ text: "NAMA MAHASISWA", value: "name",width:350,sortable:true },
						{ text: "USERNAME", value: "username",sortable:true },
						{ text: "EMAIL", value: "email",sortable:true }, 
						{ text: "NOMOR HP", value: "nomor_hp",sortable:false,width:130 },
						{ text: "TGL.DAFTAR", value: "created_at",sortable:true,width:100 }, 
						{ text: "AKSI", value: "actions", sortable: false,width:100 },
				],
				expanded: [],
				search: "",
				datatable: [],

				//dialog
				dialogfrm:false,
				dialogdetailitem:false,
				
				//form data   
				form_valid:true,
				registered:false, 
				daftar_fakultas: [],
				kode_fakultas: "",
				daftar_prodi: [], 
				daftar_ta: [], 
				formdata: {
						name: "",
						email: "",
						nomor_hp: "",
						username: "",
						password: "", 
						prodi_id: "", 
						ta: "", 
						created_at: "", 
						updated_at: "", 
				}, 
				formdefault: {
						name: "",
						email: "",
						nomor_hp: "",
						username: "",
						password: "",
						prodi_id: "",
						ta: "",
						created_at: "", 
						updated_at: "",
				},
				editedIndex: -1,

				rule_name: [
						value => !!value || "Nama Mahasiswa mohon untuk diisi !!!",
						value => /^[A-Za-z\s\\,\\.]*$/.test(value) || "Nama Mahasiswa hanya boleh string dan spasi",
				], 
				rule_nomorhp: [
						value => !!value || "Nomor HP mohon untuk diisi !!!",
						value => /^\+[1-9]{1}[0-9]{1,14}$/.test(value) || "Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388",
				], 
				rule_email: [
						value => !!value || "Email mohon untuk diisi !!!",
						v => /.+@.+\..+/.test(v) || "Format E-mail mohon di isi dengan benar",
				], 
				rule_fakultas: [
						value => !!value || "Fakultas mohon untuk dipilih !!!"
				], 
				rule_prodi: [
						value => !!value || "Program studi mohon untuk dipilih !!!"
				],
				rule_username: [
						value => !!value || "Username mohon untuk diisi !!!"
				], 
				rule_password: [
						value => !!value || "Password mohon untuk diisi !!!"
				], 
		}),
		methods: {
				changeTahunPendaftaran(tahun) {
					this.tahun_pendaftaran = tahun;
				},
				changeProdi(id) {
					this.prodi_id = id;
				},
				initialize: async function() {
					this.datatableLoading=true;
					await this.$ajax.post("/spmb/pmb",
					{
							TA: this.tahun_pendaftaran,
							prodi_id: this.prodi_id,
					},
					{
							headers: {
									Authorization: this.$store.getters["auth/Token"]
							}
					}).then(({ data }) => { 
							this.datatable = data.pmb;
							this.datatableLoading=false;
					});
					this.firstloading=false;
					this.$refs.filter7.setFirstTimeLoading(this.firstloading);
				},
				badgeColor(item)
				{
						return item.active == 1 ? "success": "error"
				},
				badgeIcon(item)
				{
						return item.active == 1 ? "mdi-check-bold": "mdi-close-thick"
				},
				dataTableRowClicked(item)
				{
						if ( item === this.expanded[0])
						{
								this.expanded=[];
						}
						else
						{
								this.expanded=[item];
						}
				},
				aktifkan(id)
				{
						this.btnLoading = true;
						this.$ajax.post("/akademik/kemahasiswaan/updatestatus/"+id,
								{
										"active":1
								},
								{
										headers: {
												Authorization: this.$store.getters["auth/Token"]
										}
								}
						).then(() => {
								this.initialize();
								this.btnLoading = false;
						}).catch(() => {
								this.btnLoading = false;
						});
				},
				syncPermission: async function()
				{
						this.btnLoading = true;
						await this.$ajax.post("/system/users/syncallpermissions",
								{
										role_name: "mahasiswabaru",
										TA: this.tahun_pendaftaran,
										prodi_id: this.prodi_id                     
								},
								{
										headers: {
												Authorization: this.$store.getters["auth/Token"]
										}
								}
						).then(() => {
								this.btnLoading = false;
						}).catch(() => {
								this.btnLoading = false;
						});
				},
				async addItem ()
				{
						this.daftar_ta=this.$store.getters["uiadmin/getDaftarTA"];
						this.formdata.ta=this.tahun_pendaftaran;
						this.formdata.prodi_id=this.prodi_id;

						if (this.$store.getters["uifront/getBentukPT"]=="universitas")
						{
								await this.$ajax.get("/datamaster/fakultas").then(({ data }) => { 
										this.daftar_fakultas=data.fakultas;
								});
						}
						else
						{
								await this.$ajax.get("/datamaster/programstudi").then(({ data }) => {
										this.daftar_prodi=data.prodi;
								});
						}
						this.dialogfrm = true;
				},
					save: async function() {
						if (this.$refs.frmdata.validate()) {
							this.btnLoading = true;
							if (this.editedIndex > -1) {
								await this.$ajax
									.post(
										"/spmb/pmb/updatependaftar/" + this.formdata.id,
										{
											"_method": "PUT",
											name: this.formdata.name,
											email: this.formdata.email,
											nomor_hp: this.formdata.nomor_hp,
											prodi_id: this.formdata.prodi_id,
											tahun_pendaftaran: this.formdata.ta,
											username: this.formdata.username,
											password: this.formdata.password, 
										},
										{
											headers: {
												Authorization: this.$store.getters["auth/Token"],
											},
										}
									)
									.then(() => {
										this.initialize();
										this.closedialogfrm();
										this.btnLoading = false;
									})
									.catch(() => {
										this.btnLoading = false;
									});
									
							} else {
								await this.$ajax.post("/spmb/pmb/storependaftar",
										{
											name: this.formdata.name,
											email: this.formdata.email,
											nomor_hp: this.formdata.nomor_hp,
											username: this.formdata.username,
											prodi_id: this.formdata.prodi_id,
											tahun_pendaftaran: this.formdata.ta,
											password: this.formdata.password,
										},
										{
											headers: {
												Authorization: this.$store.getters["auth/Token"],
											},
										}
									)
									.then(({ data }) => {
										this.datatable.push(data.pendaftar);
										this.closedialogfrm();
										this.btnLoading = false;
									})
									.catch(() => {
										this.btnLoading = false;
									});
							}
						}
					},
			async resend(id) {
				this.btnLoading = true;
				await this.$ajax
					.post(
						"/spmb/pmb/resend",
						{
							id: id,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(() => {
						this.closedialogdetailitem();
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			viewItem(item) {
				this.formdata = item;
				this.dialogdetailitem = true;
			},
			async editItem(item) {
				this.editedIndex = this.datatable.indexOf(item);
				this.formdata = Object.assign({}, item);
				this.formdata.nomor_hp = "+" + this.formdata.nomor_hp;
				this.daftar_ta = this.$store.getters["uiadmin/getDaftarTA"];
				if (this.$store.getters["uifront/getBentukPT"] == "universitas") {
					await this.$ajax.get("/datamaster/fakultas").then(({ data }) => {
						this.daftar_fakultas = data.fakultas;
					});
					await this.$ajax.get("/datamaster/programstudi").then(({ data }) => {
						this.daftar_prodi = data.prodi;
					});
				} else {
					await this.$ajax.get("/datamaster/programstudi").then(({ data }) => {
						this.daftar_prodi = data.prodi;
					});
				}
				await this.$ajax
					.get("/akademik/kemahasiswaan/biodatamhs2/" + item.id, {
						headers: {
							Authorization: this.$store.getters["auth/Token"],
						},
					})
					.then(({ data }) => {
						this.registered = data.status == 1;
						this.dialogfrm = true;
					});
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus MAHASISWA BARU " + item.name + " ?",
						{ color: "red" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/spmb/pmb/" + item.id,
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
					this.formdata = Object.assign({}, this.formdefault);
					this.editedIndex = -1;
					this.$refs.frmdata.reset();
				}, 300);
			},
		},
		watch: {
			tahun_pendaftaran() {
				if (!this.firstloading) {
					this.initialize();
				}
			},
			kode_fakultas(val) {
				if (val != null && val != "") {
					this.btnLoadingFakultas = true;
					this.$ajax
						.get("/datamaster/fakultas/" + val + "/programstudi")
						.then(({ data }) => {
							this.daftar_prodi = data.programstudi;
							this.btnLoadingFakultas = false;
						});
				}
			},
			prodi_id(val) {
				if (!this.firstloading) {
					this.nama_prodi = this.$store.getters["uiadmin/getProdiName"](val);
					this.initialize();
				}
			},
		},
		computed: {
			formTitle() {
				return this.editedIndex === -1 ? "TAMBAH DATA" : "UBAH DATA";
			},
		},
		components: {
			SPMBLayout,
			ModuleHeader,
			Filter7,
		},
	};
</script>
