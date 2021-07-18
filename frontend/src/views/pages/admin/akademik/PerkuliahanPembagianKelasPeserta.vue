<template>
		<AkademikLayout :showrightsidebar="false">
				<ModuleHeader>
						<template v-slot:icon>
								mdi-monitor-dashboard
						</template>
						<template v-slot:name>
								PEMBAGIAN KELAS
						</template>
						<template v-slot:subtitle>
								TAHUN AKADEMIK {{tahun_akademik}} SEMESTER {{$store.getters['uiadmin/getNamaSemester'](semester_akademik)}}
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
										Halaman untuk melakukan menambah peserta pada kelas terpilih.
								</v-alert>
						</template>
				</ModuleHeader>        
				<v-container fluid v-if="data_kelas_mhs">        
						<v-row>
								<v-col cols="12"> 
										<DataKelasMHS :datakelas="data_kelas_mhs" />
								</v-col>
						</v-row>
						<v-row>
								<v-col cols="12"> 
										<v-data-table
												:headers="headers"
												:items="datatable"                        
												item-key="id"                                                
												:disable-pagination="true"
												:hide-default-footer="true"                        
												class="elevation-1"
												:loading="datatableLoading"
												loading-text="Loading... Please wait">
												<template v-slot:top>
														<v-toolbar flat color="white">
																<v-toolbar-title>DAFTAR MATAKULIAH</v-toolbar-title>
																<v-divider
																		class="mx-4"
																		inset
																		vertical
																></v-divider>
																<v-spacer></v-spacer> 
																<v-tooltip bottom>             
																		<template v-slot:activator="{ on, attrs }">                  
																				<v-btn 
																						v-bind="attrs"
																						v-on="on"
																						color="primary" 
																						icon 
																						outlined 
																						small 
																						class="ma-2" 
																						@click.stop="tambahMatakuliah">
																						<v-icon>mdi-plus</v-icon>
																				</v-btn>     
																		</template>
																		<span>Tambah Matakuliah</span>        
																</v-tooltip>
														</v-toolbar>
												</template>
												<template v-slot:item.nmatkul="{item}">
														{{item.nmatkul}} - TA {{item.ta}}
												</template>
												<template v-slot:item.jam_masuk="{item}">
														{{item.jam_masuk}}-{{item.jam_keluar}}
												</template>
												<template v-slot:item.kjur="{item}">
														{{$store.getters['uiadmin/getProdiName'](item.kjur)}}
												</template>
												<template v-slot:item.actions="{ item }">   
														<v-btn
																small
																icon
																:loading="btnLoadingTable"
																:disabled="btnLoadingTable"
																@click.stop="deleteMatkul(item)">
																<v-icon>
																		mdi-delete
																</v-icon>
														</v-btn>   
												</template>                                
												<template v-slot:no-data>
														Data belum tersedia
												</template>   
										</v-data-table>
								</v-col>
						</v-row>
						<v-row>
								<v-col cols="12">          
										<v-dialog v-model="showdialogmatakuliah" max-width="800px" persistent>         
												<v-form ref="frmdatamatkul" v-model="form_valid" lazy-validation>
														<v-card>
																<v-card-title>
																		<span class="headline">TAMBAH MATAKULIAH DI KELAS INI</span>
																</v-card-title>
																<v-card-text>
																		<v-select
																				v-model="formdata.penyelenggaraan_dosen_id"
																				:items="daftar_matakuliah"
																				label="MATAKULIAH YANG DISELENGGARAKAN"
																				multiple
																				chips
																				hint="Pilihlah satu atau lebih matakuliah yang akan digabungkan dalam satu kelas"
																				persistent-hint                                        
																				outlined
																				item-text="nmatkul"
																				item-value="id">
																		</v-select>     
																</v-card-text>
																<v-card-actions>
																		<v-spacer></v-spacer>
																		<v-btn color="blue darken-1" text @click.stop="closedialogmatakuliah">BATAL</v-btn>
																		<v-btn 
																				color="blue darken-1" 
																				text 
																				@click.stop="savematakuliah" 
																				:loading="btnLoading"
																				:disabled="!form_valid||btnLoading">
																						SIMPAN
																		</v-btn>
																</v-card-actions>
														</v-card>
												</v-form>
										</v-dialog>                  
										<v-dialog v-model="showdialogpeserta" max-width="800px" persistent>         
												<v-form ref="frmdata" v-model="form_valid" lazy-validation>
														<v-card>
																<v-card-title>
																		<span class="headline">TAMBAH PESERTA</span>
																</v-card-title>
																<v-card-text>
																		<v-data-table
																				v-model="members_selected"
																				:headers="headers_members"
																				:items="datatable_members"
																				:search="search_members"
																				item-key="id"
																				sort-by="name"     
																				show-select                                                                           
																				:loading="datatableLoading"
																				loading-text="Loading... Please wait">

																				<template v-slot:item.id="{ item }">    
																						{{item.id}}
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
																						Data belum tersedia
																				</template>
																		</v-data-table>
																</v-card-text>
																<v-card-actions>
																		<v-spacer></v-spacer>
																		<v-btn color="blue darken-1" text @click.stop="closedialogpeserta">BATAL</v-btn>
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
										<v-data-table
												:headers="headers_peserta"
												:items="datatable_peserta"                        
												item-key="id"                                                
												:disable-pagination="true"
												:hide-default-footer="true"                        
												class="elevation-1"
												:loading="datatableLoading"
												loading-text="Loading... Please wait">
												<template v-slot:top>
														<v-toolbar flat color="white">
																<v-toolbar-title>DAFTAR PESERTA</v-toolbar-title>
																<v-divider
																		class="mx-4"
																		inset
																		vertical
																></v-divider>
																<v-spacer></v-spacer>     
																<v-tooltip bottom>             
																		<template v-slot:activator="{ on, attrs }">                  
																				<v-btn 
																						v-bind="attrs"
																						v-on="on"
																						color="primary" 
																						icon 
																						outlined 
																						small 
																						class="ma-2" 
																						@click.stop="tambahPeserta"
																						:disabled="!datatable>0">
																						<v-icon>mdi-plus</v-icon>
																				</v-btn>     
																		</template>
																		<span>Tambah Peserta Kelas</span>        
																</v-tooltip>
														</v-toolbar>
												</template>                        
												<template v-slot:item.idkelas="{item}">
														{{$store.getters['uiadmin/getNamaKelas'](item.idkelas)}}
												</template>
												<template v-slot:item.kjur="{item}">
														{{$store.getters['uiadmin/getProdiName'](item.kjur)}}
												</template>
												<template v-slot:item.actions="{ item }">   
														<v-btn
																small
																icon
																:loading="btnLoadingTable"
																:disabled="btnLoadingTable"
																@click.stop="deletePeserta(item)">
																<v-icon>
																		mdi-delete
																</v-icon>
														</v-btn>   
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
import AkademikLayout from '@/views/layouts/AkademikLayout';
import ModuleHeader from '@/components/ModuleHeader';
import DataKelasMHS from '@/components/DataKelasMHS';

export default {
		name: 'PerkuliahanPembagianKelasTambah',
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
								text:'PEMBAGIAN KELAS',
								disabled:false,
								href:'/akademik/perkuliahan/pembagiankelas/daftar'
						},
						{
								text:'PESERTA',
								disabled:true,
								href:'#'
						}
				];        
				this.kelas_mhs_id=this.$route.params.kelas_mhs_id;        
				this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];    
				this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];    
				this.initialize()
		},
		data: () => ({ 
				kelas_mhs_id:null,
				data_kelas_mhs:null,
				tahun_akademik:null,
				semester_akademik:null,

				btnLoadingTable:false,
				datatableLoading:false,
				btnLoading: false,
				
				datatable: [],  
				daftar_matakuliah: [],   
				datatable_peserta: [], 
				datatable_members: [],        
				headers: [
						{ text: 'KODE', value: 'kmatkul', sortable:false,width:100  }, 
						{ text: 'NAMA', value: 'nmatkul', sortable:false  }, 
						{ text: 'SKS', value: 'sks', sortable:false  },       
						{ text: 'PROGRAM STUDI', value: 'kjur', sortable:false, width:200 },       
						{ text: 'JUMLAH MHS DI KRS', value: 'jumlah_mhs', sortable:false, width:100 },       
						{ text: 'AKSI', value: 'actions', sortable: false,width:60 },
				],
				headers_peserta: [
						{ text: 'NIM', value: 'nim', sortable:false,width:100  }, 
						{ text: 'NAMA', value: 'nama_mhs', sortable:false  }, 
						{ text: 'PROGRAM STUDI', value: 'kjur', sortable:false  }, 
						{ text: 'KELAS', value: 'idkelas', sortable:false  },       
						{ text: 'TAHUN MASUK', value: 'tahun', sortable:false },                   
						{ text: 'AKSI', value: 'actions', sortable: false,width:60 },
				],
				headers_members: [
						{ text: 'NIM', value: 'nim', sortable:false,width:100  }, 
						{ text: 'NAMA', value: 'nama_mhs', sortable:false  }, 
						{ text: 'KELAS', value: 'idkelas', sortable:false  },       
						{ text: 'TAHUN MASUK', value: 'tahun', sortable:false },                               
				],
				search_members: "",  

				showdialogmatakuliah:false,    
				showdialogpeserta:false,    

				//formdata
				form_valid:true,
				members_selected: [],
				formdata: {                        
						penyelenggaraan_dosen_id: "",    
				},
				
		}),
		methods: {        
				initialize:async function() 
				{
						this.datatableLoading=true;
						await this.$ajax.get('/akademik/perkuliahan/pembagiankelas/'+this.kelas_mhs_id,          
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {           
								this.data_kelas_mhs=data.pembagiankelas;    
								this.datatable=data.penyelenggaraan;                    
								this.datatable_peserta=data.peserta;                    
								this.datatableLoading=false;
						})       
				},
				async fetchMatkul()
				{
						this.datatableLoading=true;
						await this.$ajax.get('/akademik/perkuliahan/pembagiankelas/matakuliah/'+this.kelas_mhs_id,          
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                                      
								this.datatable=data.penyelenggaraan;                    
								this.datatableLoading=false;
						})   
				},
				async fetchPeserta()
				{
						this.datatableLoading=true;
						await this.$ajax.get('/akademik/perkuliahan/pembagiankelas/peserta/'+this.kelas_mhs_id,          
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                                      
								this.datatable_peserta=data.peserta;                    
								this.datatableLoading=false;
						})   
				},
				async tambahMatakuliah()
				{
						await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/matakuliah',          
						{
								user_id: this.data_kelas_mhs.user_id,
								ta: this.data_kelas_mhs.tahun,              
								semester_akademik: this.data_kelas_mhs.idsmt,              
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                                               
								this.daftar_matakuliah = data.matakuliah; 
								this.showdialogmatakuliah=true;          
						})  
				},
				async tambahPeserta()
				{
						await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/members',          
						{
								pid:'belumterdaftar',
								kelas_mhs_id: this.kelas_mhs_id,
								penyelenggaraan:JSON.stringify(Object.assign({},this.datatable))
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {           
								this.datatable_members=data.members;    
								this.showdialogpeserta=true;
						})             
				},
				save:async function() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;
								await this.$ajax.post('/akademik/perkuliahan/pembagiankelas/storepeserta',
										{
												kelas_mhs_id: this.kelas_mhs_id,    
												members_selected:JSON.stringify(Object.assign({},this.members_selected)),                                                
										},
										{
												headers: {
														Authorization: this.$store.getters['auth/Token']
												}
										}
								).then(() => {                       
										this.btnLoading = false;
										this.closedialogpeserta();
								}).catch(() => {
										this.btnLoading = false;
								});
						}            
				},
				savematakuliah:async function() {
						if (this.$refs.frmdatamatkul.validate())
						{
								this.btnLoading = true;
								await this.$ajax.post('/akademik/perkuliahan/pembagiankelas/storematakuliah',
										{
												kelas_mhs_id: this.kelas_mhs_id,    
												penyelenggaraan_dosen_id:JSON.stringify(Object.assign({},this.formdata.penyelenggaraan_dosen_id)),
										},
										{
												headers: {
														Authorization: this.$store.getters['auth/Token']
												}
										}
								).then(() => {                       
										this.btnLoading = false;
										this.closedialogmatakuliah();
								}).catch(() => {
										this.btnLoading = false;
								});
						}            
				},
				deleteMatkul(item)
				{
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus data matakuliah di kelas ini dengan ID '+item.id+' ?', { color: 'red', width:600,'desc':'proses ini juga menghapus seluruh mahasiswa yang mengontrak matakuliah di kelas ini.' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoading = true;
										this.$ajax.post('/akademik/perkuliahan/pembagiankelas/deletematkul/'+item.id,
												{
														'_method':'DELETE',
												},
												{
														headers: {
																Authorization: this.$store.getters['auth/Token']
														}
												}
										).then(() => {                           
												this.btnLoading = false;
												this.$router.go();
										}).catch(() => {
												this.btnLoading = false;
										});
								}                
						});
				},     
				deletePeserta(item)
				{
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus data mahasiswa di kelas ini dengan ID '+item.id+' ?', { color: 'red' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoading = true;
										this.$ajax.post('/akademik/perkuliahan/pembagiankelas/deletepeserta/'+item.id,
												{
														'_method':'DELETE',
												},
												{
														headers: {
																Authorization: this.$store.getters['auth/Token']
														}
												}
										).then(() => {                           
												this.btnLoading = false;
												this.$router.go();
										}).catch(() => {
												this.btnLoading = false;
										});
								}                
						});
				},     
				closedialogpeserta() {
						this.showdialogpeserta = false;
						setTimeout(() => {                
								this.members_selected=[];
								this.fetchPeserta();
								this.$refs.frmdata.reset(); 
								}, 300
						);
				},
				closedialogmatakuliah() {
						this.showdialogmatakuliah = false;
						setTimeout(() => {                                
								this.fetchMatkul();
								this.$refs.frmdatamatkul.reset(); 
								}, 300
						);
				},
		},
		watch: {
				
		},  
		components: {
				AkademikLayout,
				ModuleHeader,   
				DataKelasMHS       
		},
}
</script>