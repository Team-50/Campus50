<template>
	<KeuanganLayout>
		<ModuleHeader>
			<template v-slot:icon>
				mdi-account-cash
			</template>
			<template v-slot:name>
				KONFIRMASI PEMBAYARAN
			</template>
			<template v-slot:subtitle>
				TAHUN AKADEMIK {{tahun_akademik}}
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
						Halaman ini berisi informasi konfirmasi pembayaran mahasiswa, silahkan melakukan filter tahun akademik.
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
						sort-by="no_transaksi"
						show-expand
						:expanded.sync="expanded"
						:single-expand="true"
						@click:row="dataTableRowClicked"
						class="elevation-1"
						:loading="datatableLoading"
						loading-text="Loading... Please wait">
						<template v-slot:top>
							<v-toolbar flat color="white">
								<v-toolbar-title>DAFTAR TRANSAKSI</v-toolbar-title>
								<v-divider
									class="mx-4"
									inset
									vertical
								></v-divider>
								<v-spacer></v-spacer>
								<v-dialog v-model="dialogfrm" max-width="750px" persistent v-if="dialogfrm">
									<v-card color="grey lighten-4">
										<v-toolbar elevation="2"> 
											<v-toolbar-title>KONFIRMASI !!!</v-toolbar-title>
											<v-divider
												class="mx-4"
												inset
												vertical
											></v-divider>
											<v-spacer></v-spacer>
											<v-icon @click.stop="closedialogfrm()">
												mdi-close-thick
											</v-icon>
										</v-toolbar>
										<v-card-text>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>ID :</v-card-title>
														<v-card-subtitle>
															{{data_transaksi.id}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>KODE BILLING :</v-card-title>
														<v-card-subtitle>
															{{data_transaksi.no_transaksi}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>NAMA MAHASISWA :</v-card-title>
														<v-card-subtitle>
															{{data_transaksi.nama_mhs}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>NOMOR FORMULIR :</v-card-title>
														<v-card-subtitle>
															{{data_transaksi.no_formulir}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>TANGGAL TRANSAKSI :</v-card-title>
														<v-card-subtitle>
															{{$date(data_transaksi.tanggal).format("DD/MM/YYYY")}} {{$date(data_transaksi.created_at).format("HH:mm:ss")}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>NIM :</v-card-title>
														<v-card-subtitle>
															{{data_transaksi.nim}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>TOTAL :</v-card-title>
														<v-card-subtitle>
															{{data_transaksi.total|formatUang}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>STATUS :</v-card-title>
														<v-card-subtitle>
															<v-chip :color="data_transaksi.style" dark>{{data_transaksi.nama_status}}</v-chip>
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col cols="12">
													<v-form ref="frmdata" v-model="form_valid" lazy-validation>
														<v-card>                                 
															<v-card-text>  
																<v-select
																	label="PEMBAYARAN MELALUI :"
																	v-model="formdata.id_channel"
																	:items="daftar_channel"
																	item-text="nama_channel"
																	item-value="id_channel"
																	:rules="rule_channel_pembayaran"
																	outlined
																/>  
																<v-currency-field 
																	label="SEBESAR :" 
																	:min="null"
																	:max="null"                                            
																	outlined                                                                    
																	v-model="formdata.total_bayar">             
																</v-currency-field>
																<v-text-field 
																	v-model="formdata.nomor_rekening_pengirim"
																	label="NOMOR REKENING PENGIRIM:" 
																	:rules="rule_nomor_rekening"
																	outlined />  
																<v-text-field 
																	v-model="formdata.nama_rekening_pengirim"
																	label="NAMA PENGIRIM:" 
																	:rules="rule_nama_pengirim"
																	outlined />  
																<v-text-field 
																	v-model="formdata.nama_bank_pengirim"
																	label="BANK PENGIRIM:" 
																	:rules="rule_nama_bank"
																	outlined /> 
																<v-menu
																	ref="menuTanggalBayar"
																	v-model="menuTanggalBayar"
																	:close-on-content-click="false"
																	:return-value.sync="formdata.tanggal_bayar"
																	transition="scale-transition"
																	offset-y
																	max-width="290px"
																	min-width="290px"
																>
																	<template v-slot:activator="{ on }">
																		<v-text-field
																			v-model="formdata.tanggal_bayar"
																			label="TANGGAL BAYAR/TRANSFER"                                            
																			readonly
																			outlined
																			v-on="on"
																			:rules="rule_tanggal_bayar"
																		></v-text-field>
																	</template>
																	<v-date-picker
																		v-model="formdata.tanggal_bayar"                                        
																		no-title                                
																		scrollable
																		>
																		<v-spacer></v-spacer>
																		<v-btn text color="primary" @click="menuTanggalBayar = false">Cancel</v-btn>
																		<v-btn text color="primary" @click="$refs.menuTanggalBayar.save(formdata.tanggal_bayar)">OK</v-btn>
																	</v-date-picker>
																</v-menu>
																<v-textarea 
																	v-model="formdata.desc"
																	label="CATATAN:"                                                                    
																	outlined />
																<v-file-input
																	accept="image/jpeg,image/png" 
																	label="BUKTI BAYAR (MAKS. 2MB)"
																	:rules="rule_bukti_bayar"
																	show-size
																	v-model="formdata.bukti_bayar"
																	@change="previewImage">
																</v-file-input> 
																<v-img class="white--text align-end" :src="buktiBayar"></v-img>                                                    
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
												</v-col>
											</v-row>
										</v-card-text>
									</v-card>
								</v-dialog>
								<v-dialog v-model="dialogdetailitem" max-width="750px" persistent v-if="dialogdetailitem">
									<v-card color="grey lighten-4">
										<v-toolbar elevation="2"> 
											<v-toolbar-title>DETAIL KONFIRMASI PEMBAYARAN !!!</v-toolbar-title>
											<v-divider
												class="mx-4"
												inset
												vertical
											></v-divider>
											<v-spacer></v-spacer>
											<v-icon                
												@click.stop="closedialogdetailitem()">
												mdi-close-thick
											</v-icon>
										</v-toolbar>
										<v-card-text>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>ID :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.transaksi_id}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>KODE BILLING :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.no_transaksi}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>CHANNEL PEMBAYARAN :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.nama_channel}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>TANGGAL KONFIRMASI :</v-card-title>
														<v-card-subtitle>
															{{$date(data_konfirmasi.tanggal_bayar).format("DD/MM/YYYY")}}
														</v-card-subtitle>
													</v-card>
												</v-col>                     
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>NOMOR REKENING PENGIRIM :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.nomor_rekening_pengirim}}
														</v-card-subtitle>
													</v-card>
												</v-col>                     
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>NAMA REKENING PENGIRIM :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.nama_rekening_pengirim}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>NAMA BANK PENGIRIM :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.nama_bank_pengirim}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>TOTAL BAYAR :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.total_bayar|formatUang}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-row>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>STATUS :</v-card-title>
														<v-card-subtitle>
															{{data_konfirmasi.nama_status}}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
												<v-col xs="12" sm="6" md="6">
													<v-card flat>
														<v-card-title>CREATED/UPDATED :</v-card-title>
														<v-card-subtitle>
															{{ $date(data_transaksi.created_at).format("DD/MM/YYYY HH:mm") }} - 
															{{ $date(data_transaksi.updated_at).format("DD/MM/YYYY HH:mm") }}
														</v-card-subtitle>
													</v-card>
												</v-col>
												<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
											</v-row>
											<v-img class="white--text align-end" :src="buktiBayar"></v-img>                                                    
										</v-card-text>
										<v-card-actions>
											<v-spacer></v-spacer>
											<v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">BATAL</v-btn>                 
										</v-card-actions>
									</v-card>
								</v-dialog>
								<v-dialog v-model="dialogcanceltransaksi" max-width="750px" persistent v-if="dialogcanceltransaksi">

								</v-dialog>
							</v-toolbar>
						</template>
						<template v-slot:item.tanggal="{ item }">    
							{{$date(item.tanggal).format("DD/MM/YYYY")}}
						</template>
						<template v-slot:item.idsmt="{ item }">    
							{{$store.getters["uiadmin/getNamaSemester"](item.idsmt)}}
						</template>
						<template v-slot:item.total="{ item }">    
							{{item.total|formatUang}}
						</template>
						<template v-slot:item.nama_status="{ item }">    
							<v-chip :color="item.style" dark>{{item.nama_status}}</v-chip>
						</template>
						<template v-slot:item.actions="{ item }">
							<v-icon
								title="Upload Bukti"
								small
								class="mr-2"
								@click.stop="addItem(item)"
								v-if="item.status_konfirmasi=='N.A'">
								mdi-send
							</v-icon>
							<v-icon
								title="Upload Bukti"
								small
								class="mr-2"
								:disabled="true"
								v-else>
								mdi-send
							</v-icon>   
							<v-icon
								title="Preview"
								small
								class="mr-2"
								@click.stop="viewItem(item)"
								v-if="item.status_konfirmasi=='VERIFIED' || item.status_konfirmasi=='UNVERIFIED'">
								mdi-eye
							</v-icon>                        
							<v-icon
								title="Preview"
								small
								class="mr-2"
								:disabled="true"                                
								v-else>
								mdi-eye
							</v-icon>                        
						</template>           
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="12">                          
									<strong>ID TRANSAKSI:</strong>{{ item.id }}          
									<strong>created_at:</strong>{{ item.created_at_konfirm=="N.A"?"N.A":$date(item.created_at_konfirm).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong>{{ item.updated_at_konfirm=="N.A"?"N.A":$date(item.updated_at_konfirm).format("DD/MM/YYYY HH:mm") }}
								</v-col>  
								<v-col cols="12" v-if="$store.getters['auth/can']('KEUANGAN-KONFIRMASI-PEMBAYARAN_UPDATE')&&(dashboard!='mahasiswabaru'&&dashboard!='mahasiswa')">                          
									<v-btn 
										text 
										small 
										color="primary" 
										@click.stop="verifikasi(item)" 
										class="mb-2" 
										:disabled="(item.status_konfirmasi=='UNVERIFIED'?false:true)||btnLoading"  
										:loading="btnLoading">
										VERIFIKASI
									</v-btn>
									<v-btn 
										text 
										small 
										color="primary" 
										@click.stop="cancel(item)" 
										class="mb-2" 
										:disabled="(item.nama_status=='PAID'?true:false)||btnLoading" 
										:loading="btnLoading">
										BATALKAN
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
			<Filter1 v-on:changeTahunAkademik="changeTahunAkademik" ref="filter1" />	
		</template>
	</KeuanganLayout>
</template>
<script>
	import KeuanganLayout from "@/views/layouts/KeuanganLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	import Filter1 from "@/components/sidebar/FilterMode1";
	export default {
		name: "KonfirmasiPembayaran",
		created() {
			this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled:false,
					href: "/dashboard/" + this.ACCESS_TOKEN,
				},
				{
					text: "KEUANGAN",
					disabled:false,
					href: "/keuangan"
				},
				{
					text: "KONFIRMASI PEMBAYARAN",
					disabled:true,
					href: "#"
				}
			];
			this.breadcrumbs[1].disabled=(this.dashboard=="mahasiswabaru"||this.dashboard=="mahasiswa");
			this.tahun_akademik=this.$store.getters["uiadmin/getTahunAkademik"];
			this.initialize()
		},
		data: () => ({
			firstloading:true,
			breadcrumbs: [],
			dashboard:null,
			btnLoading: false,
			tahun_akademik:null,

			//tables
			datatableLoading:false,
			datatable: [],
			headers: [                                                
				{ text: "KODE BILLING", value: "no_transaksi",width:100,sortable:true },
				{ text: "NO.REF", value: "no_faktur",width:100,sortable:true },
				{ text: "TANGGAL TRANSAKSI", value: "tanggal",width:100,sortable:true },
				{ text: "NIM", value: "nim",sortable:true,width:100 },
				{ text: "NAMA MAHASISWA", value: "nama_mhs",sortable:true,width:250 },
				{ text: "SMT", value: "idsmt",width:100,sortable:true },
				{ text: "TOTAL", value: "total",width:100,sortable:true },
				{ text: "STATUS TRANSAKSI", value: "nama_status",width:50,sortable:true }, 
				{ text: "KONFIRM.", value: "status_konfirmasi",width:50,sortable:true }, 
				{ text: "AKSI", value: "actions", sortable: false,width:82 },
			],
			expanded: [],
			search: "",

			//dialog        
			dialogfrm:false,
			dialogdetailitem:false,
			dialogcanceltransaksi:false,
			
			//form data   
			form_valid:true,
			menuTanggalBayar:false,
			image_prev:null,
			data_transaksi: {
				
			},
			data_konfirmasi: {},
			daftar_channel: [],
			formdata: {
				id_channel:1,
				total_bayar:0,
				nomor_rekening_pengirim: "",
				nama_rekening_pengirim: "",
				nama_bank_pengirim: "",
				desc: "",
				tanggal_bayar: "",
				bukti_bayar: [],
			},
			formdefault: {
				id_channel:1,
				total_bayar:0,
				nomor_rekening_pengirim: "",
				nama_rekening_pengirim: "",
				nama_bank_pengirim: "",
				desc: "",
				tanggal_bayar: "",
				bukti_bayar: [],
			},
			//form rules  
			rule_channel_pembayaran: [
				value => !!value || "Mohon dipilih Channel Pembayaran mohon untuk dipilih !!!"
			],
			rule_nama_pengirim: [
				value => !!value || "Mohon diisi nama pengirim !!!"
			],
			rule_nomor_rekening: [
				value => !!value || "Mohon diisi nomor rekening pengirim !!!",
				value => /^[0-9]+$/.test(value) || "Nomor Rekening hanya boleh angka",
			],
			rule_nama_bank: [
				value => !!value || "Mohon diisi nama bank !!!"
			],
			rule_tanggal_bayar: [
				value => !!value || "Tanggal Bayar mohon untuk diisi !!!"
			],
			rule_bukti_bayar: [
				value => !!value || "Mohon pilih foto !!!",
				value => !!value || value.size < 2000000 || "File Bukti Bayar harus kurang dari 2MB."
			],
		}),
		methods: {
			changeTahunAkademik (tahun)
			{
				this.tahun_akademik=tahun;
			},
			initialize:async function() 
			{
				this.datatableLoading = true;
				await this.$ajax.post("/keuangan/konfirmasipembayaran",
				{
					TA: this.tahun_akademik,
				},
				{
					headers: {
						Authorization: this.$store.getters["auth/Token"],
					}
				}).then(({ data }) => {
					this.datatable = data.transaksi;
					this.datatableLoading = false;
				});
				this.firstloading = false;
				this.$refs.filter1.setFirstTimeLoading(this.firstloading);
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
			async addItem(item)
			{
				await this.$ajax.get("/keuangan/channelpembayaran",
				{
					headers: {
						Authorization: this.$store.getters["auth/Token"],
					}
				})
				.then(({ data }) => {
					var channel = data.channel;
					var channel_pembayaran = [];
					channel.forEach(element => {
						if (element.id_channel != 4) {
							channel_pembayaran.push(element);
						}
					});
					this.daftar_channel = channel_pembayaran;
					this.data_transaksi = item;
					this.dialogfrm = true;
				});
				
			},
			async viewItem(item)
			{
				await this.$ajax.get("/keuangan/konfirmasipembayaran/" + item.id,
				{
					headers: {
						Authorization: this.$store.getters["auth/Token"],
					}
				}).then(({ data }) => {
					this.data_konfirmasi=data.konfirmasi;
					this.image_prev=this.$api.storageURL + "/" + data.konfirmasi.bukti_bayar;
					this.dialogdetailitem = true;
				});
				
			},
			previewImage(e) {
				if (typeof e === "undefined") {
					this.image_prev=null;
				} else {
					let reader = new FileReader();
					reader.readAsDataURL(e);
					reader.onload = img => {  
						this.image_prev=img.target.result;
					}    
				}          
			},
			save() {
				if (this.$refs.frmdata.validate())
				{
					this.btnLoading = true;
					var data = new FormData();
					data.append("transaksi_id", this.data_transaksi.id);
					data.append("id_channel", this.formdata.id_channel);
					data.append("total_bayar", this.formdata.total_bayar);
					data.append("nomor_rekening_pengirim", this.formdata.nomor_rekening_pengirim);
					data.append("nama_rekening_pengirim", this.formdata.nama_rekening_pengirim);
					data.append("nama_bank_pengirim", this.formdata.nama_bank_pengirim);
					data.append("desc", this.formdata.desc);
					data.append("tanggal_bayar", this.formdata.tanggal_bayar);
					data.append("bukti_bayar", this.formdata.bukti_bayar);

					this.$ajax.post("/keuangan/konfirmasipembayaran/store",data,
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
								"Content-Type": "multipart/form-data",
							},
						}
					).then(() => {
						this.btnLoading = false;
						this.closedialogfrm();
						this.initialize();
					}).catch(() => {
						this.btnLoading = false;
					});
				
				}
			},
			async verifikasi(item) {
				this.$root.$confirm.open("Konfirmasi Pembayaran", "Apakah sudah benar data bukti bayar kode billing " + item.no_transaksi + " ?", { color: "primary" }).then((confirm) => {
					if (confirm)
					{
						this.btnLoading = true;
						this.$ajax.post("/keuangan/transaksi/verifikasi/" + item.id,
							{
								_method: "put"            
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								}
							}
						).then(() => {
							this.initialize();
							this.btnLoading = false;
						}).catch(() => {
							this.btnLoading = false;
						});
					}    
				});
			},
			async cancel(item)
			{
				this.$root.$confirm.open("Batalkan Transaksi", "Apakah Anda ingin membatalkan transaksi dengan kode billing " + item.no_transaksi + " ?", { color: "red" }).then((confirm) => {
					if (confirm)
					{
						this.btnLoading = true;
						this.$ajax.post("/keuangan/transaksi/cancel",
							{
								transaksi_id: item.id,
							},
							{
								headers: {
									Authorization: this.$store.getters["auth/Token"],
								}
							}
						).then(() => {
							this.initialize();
							this.btnLoading = false;
						}).catch(() => {
							this.btnLoading = false;
						});
					}    
				});
			},
			closedialogfrm() {
				this.dialogfrm = false;
				setTimeout(() => {
					this.buktiBayar=null;
					this.formdata = Object.assign({}, this.formdefault);
					this.data_transaksi = Object.assign({}, {});
					this.data_konfirmasi = Object.assign({}, {});
					}, 300
				);
			},
			closedialogdetailitem() {
				this.dialogdetailitem = false;
				setTimeout(() => {
					this.formdata = Object.assign({}, this.formdefault);
					this.data_transaksi = Object.assign({}, {});
					this.data_konfirmasi = Object.assign({}, {});
				}, 300);
			},
		},
		computed: {
			TahunPendaftaran() {
				return this.$store.getters["uiadmin/getTahunPendaftaran"];
			},
			buktiBayar: {
				get() {
					if (this.image_prev == null) {
						return require("@/assets/no-image.png");
					} else {
						return this.image_prev;
					}
				},
				set(val) {
					this.image_prev = val;
				},
			},
		},
		watch: {
			tahun_akademik() {
				if (!this.firstloading) {
					this.initialize();
				}
			},
		},
		components: {
			KeuanganLayout,
			ModuleHeader,
			Filter1,
		},
	};
</script>
