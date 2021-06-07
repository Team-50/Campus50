<template>
	<AkademikLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-monitor-dashboard
			</template>
			<template v-slot:name>
				PENYELENGGARAAN PERKULIAHAN
			</template>
			<template v-slot:subtitle>
				TAHUN AKADEMIK {{tahun_akademik}} SEMESTER {{$store.getters['uiadmin/getNamaSemester'](semester_akademik)}} - {{nama_prodi}}
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
					Halaman untuk melakukan penyelenggaraan matakuliah per prodi, tahun akademik, dan semester.
				</v-alert>
			</template>
		</ModuleHeader>        
		<v-container fluid>                         
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">                    
					<v-form ref="frmdata" v-model="form_valid" lazy-validation>
						<v-card>
							<v-card-title>
								TAMBAH MATAKULIAH YANG AKAN DISELENGGARAKAN
							</v-card-title>
							<v-card-text>
								<v-alert type="info">
									Silahkan pilih matakuliah yang akan diselenggarakan pada T.A {{tahun_akademik}}{{semester_akademik}}
								</v-alert>
								<v-select
									v-model="ta_matkul"
									:items="daftar_ta"                                    
									label="TAHUN MATAKULIAH"
									:rules="rule_tamatkul"
									outlined/>   
								<v-text-field
									v-model="search"
									append-icon="mdi-database-search"
									label="Cari Matakuliah"
									single-line
									hide-details
								></v-text-field>
								<v-data-table
									v-model="daftar_matkul_selected"
									:headers="headers"
									:items="datatable"
									:search="search"
									item-key="id"                        
									show-expand
									:expanded.sync="expanded"
									:single-expand="true"
									show-select
									:disable-pagination="true"
									:hide-default-footer="true"
									@click:row="dataTableRowClicked"                                    
									:loading="datatableLoading"
									loading-text="Loading... Please wait">

									<template v-slot:top>
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
																<v-card-title>SKS :</v-card-title>
																<v-card-subtitle>
																	{{formdata.sks}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
													</v-row>                 
													<v-row no-gutters>
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>KODE MATAKULIAH :</v-card-title>
																<v-card-subtitle>
																	{{formdata.kmatkul}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                     
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>SKS TATAP MUKA :</v-card-title>
																<v-card-subtitle>
																	{{formdata.sks_tatap_muka}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
													</v-row>                 
													<v-row no-gutters>
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>NAMA MATAKULIAH :</v-card-title>
																<v-card-subtitle>
																	{{formdata.nmatkul}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                     
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>SKS PRAKTIKUM :</v-card-title>
																<v-card-subtitle>
																	{{formdata.sks_praktikum2}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
													</v-row>                 
													<v-row no-gutters>
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>SEMESTER :</v-card-title>
																<v-card-subtitle>
																	{{formdata.semester}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                     
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>SKS PRAKTIK LAPANGAN :</v-card-title>
																<v-card-subtitle>
																	{{formdata.sks_praktik_lapangan2}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
													</v-row>                 
													<v-row no-gutters>
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>MINIMAL NILAI :</v-card-title>
																<v-card-subtitle>
																	{{formdata.minimal_nilai}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                     
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>SYARAT SKRIPSI :</v-card-title>
																<v-card-subtitle>
																	{{formdata.syarat_skripsi == 1 ? 'YA' : 'TIDAK'}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
													</v-row>                 
													<v-row no-gutters>
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>PRODI / TA :</v-card-title>
																<v-card-subtitle>
																	{{formdata.nama_prodi}} - {{formdata.tahun_akademik}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                     
														<v-col xs="12" sm="6" md="6">
															<v-card flat>
																<v-card-title>STATUS :</v-card-title>
																<v-card-subtitle>
																	{{formdata.status == 1 ? 'AKTIF' : 'NON-AKTIF'}}
																</v-card-subtitle>
															</v-card>
														</v-col>
														<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
													</v-row>                 
												</v-card-text>
												<v-card-actions>
													<v-spacer></v-spacer>
													<v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">KELUAR</v-btn>
												</v-card-actions>
											</v-card>         
										</v-dialog>                 
									</template>
									<template v-slot:item.actions="{ item }">
										<v-icon
											small
											class="mr-2"
											@click.stop="viewItem(item)">
											mdi-eye
										</v-icon>         
									</template>
									<template v-slot:expanded-item="{ headers, item }">
										<td :colspan="headers.length" class="text-center">
											<v-col cols="12">                          
												<strong>ID:</strong>{{ item.id }}          
												<strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
												<strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
											</v-col>     
										</td>
									</template>         
									<template v-slot:no-data>
										Data matakuliah belum tersedia
									</template>
								</v-data-table>
							</v-card-text>
							<v-card-actions>
								<v-spacer></v-spacer>
								<v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
								<v-btn 
									color="blue darken-1" 
									text 
									@click.stop="save" 
									:loading="btnLoading"
									:disabled="!form_valid||btnLoading||!daftar_matkul_selected.length > 0">
										SIMPAN
								</v-btn>
							</v-card-actions>
						</v-card>
					</v-form>
				</v-col>
			</v-row>   
		</v-container>
	</AkademikLayout>
</template>
<script>
	import AkademikLayout from '@/views/layouts/AkademikLayout';
	import ModuleHeader from '@/components/ModuleHeader';
	export default {
		name: 'PerkuliahanPenyelenggaraanTambah',
		created() {
			this.breadcrumbs = [
				{
					text:'HOME',
					disabled:false,
					href:'/dashboard/'+this.$store.getters['auth/AccessToken']
				},
				{
					text:'AKADEMIK',
					disabled:false,
					href:'/akademik'
				},
				{
					text:'PERKULIAHAN',
					disabled:false,
					href:'#'
				},
				{
					text:'PENYELENGGARAAN MATAKULIAH',
					disabled:false,
					href:'/akademik/perkuliahan/penyelenggaraan/daftar'
				},
				{
					text:'TAMBAH',
					disabled:true,
					href:'#'
				},
			];
			let prodi_id=this.$store.getters['uiadmin/getProdiID'];
			this.prodi_id=prodi_id;
			this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);        
			this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];  
			this.daftar_ta=this.$store.getters['uiadmin/getDaftarTABefore'](this.tahun_akademik);                    
			this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];    
			
		},
		data: () => ({ 
			firstloading:true,
			prodi_id:null,
			nama_prodi:null,
			tahun_akademik:null,
			ta_matkul:null,
			semester_akademik:null,

			btnLoading: false,      

			//table
			dialogdetailitem:false,
			datatableLoading:false,
			expanded: [],
			datatable: [],    
			headers: [
				{ text: 'KODE', value: 'kmatkul', sortable:true,width:120  }, 
				{ text: 'NAMA MATAKULIAH', value: 'nmatkul',sortable:true },             
				{ text: 'KELOMPOK', value: 'group_alias', sortable:true,width:120 },             
				{ text: 'SKS', value: 'sks',sortable:true,width:80, align:'center' },             
				{ text: 'SMT', value: 'semester', sortable:true,width:80 },             
				{ text: 'AKSI', value: 'actions', sortable: false,width:100 },
			],
			search: "",  

			//formdata
			form_valid:true, 
			formdata: [],
			daftar_matkul_selected: [],
			rule_tamatkul: [
				value => !!value || "Mohon tahun matakuliah untuk dipilih !!!",            
			]        

		}),
		methods: {        
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
			fetchMatkul:async function() 
			{
				this.datatableLoading=true;
				await this.$ajax.post('/akademik/matakuliah/penyelenggaraan',
				{
					prodi_id: this.prodi_id,
					ta: this.ta_matkul,
					semester_akademik: this.semester_akademik,
				},
				{
					headers: {
						Authorization: this.$store.getters['auth/Token']
					}
				}).then(({ data }) => {               
					this.datatable = data.matakuliah;
					this.datatableLoading=false;
				}).catch(() => {
					this.datatableLoading=false;
				});  
			},
			async viewItem (item) {
				this.formdata=item;      
				await this.$ajax.get('/akademik/matakuliah/'+item.id,
				{
					headers: {
						Authorization: this.$store.getters['auth/Token']
					}
				}).then(({ data }) => {
					this.formdata=data.matakuliah;
				});
				this.dialogdetailitem=true;            
			},  
			save:async function() {
				if (this.$refs.frmdata.validate())
				{                
					this.btnLoading = true;
					await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/store',
						{
							prodi_id: this.prodi_id,
							ta: this.tahun_akademik,
							semester_akademik: this.semester_akademik,      
							matkul_selected:JSON.stringify(Object.assign({},this.daftar_matkul_selected)),                                                
						},
						{
							headers: {
								Authorization: this.$store.getters['auth/Token']
							}
						}
					).then(() => {                       
						this.btnLoading = false;
						this.closedialogfrm();
					}).catch(() => {
						this.btnLoading = false;
					});
				}
			},
			closedialogdetailitem() {
				this.dialogdetailitem = false;
				setTimeout(() => {
					this.formdata = Object.assign({}, this.formdefault)
					this.editedIndex = -1
					}, 300
				);
			},
			closedialogfrm() {                             
				setTimeout(() => {       
					this.formdata = Object.assign({}, this.formdefault);                    
					this.$router.push('/akademik/perkuliahan/penyelenggaraan/daftar');
					}, 300
				);
			},
		},
		watch: {
			ta_matkul(val)
			{
				this.fetchMatkul(val);            
			},      
		},
		components: {
			AkademikLayout,
			ModuleHeader,          
		},
	};
</script>
