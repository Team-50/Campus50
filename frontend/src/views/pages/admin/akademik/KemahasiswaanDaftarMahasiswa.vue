<template>
		<AkademikLayout>
				<ModuleHeader>
						<template v-slot:icon>
								mdi-playlist-check
						</template>
						<template v-slot:name>
								DAFTAR MAHASISWA 
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
										Halaman untuk daftar mahasiswa yang sudah per program studi dan tahun pendaftaran.
								</v-alert>
						</template>
				</ModuleHeader>
				<template v-slot:filtersidebar>
						<Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeProdi="changeProdi" ref="filter7" />	
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
												item-key="user_id"                           
												show-expand
												:expanded.sync="expanded"
												:single-expand="true"                        
												@click:row="dataTableRowClicked"
												class="elevation-1"
												:loading="datatableLoading"
												loading-text="Loading... Please wait">
												<template v-slot:top>
														<v-toolbar flat color="white">
																<v-toolbar-title>DAFTAR MAHASISWA</v-toolbar-title>
																<v-divider
																		class="mx-4"
																		inset
																		vertical
																></v-divider>
																<v-spacer></v-spacer>
																<v-btn 
																		:loading="btnLoading"
																		:disabled="btnLoading"
																		color="warning" 
																		class="mb-2 mr-2" 
																		@click.stop="syncPermission" 
																		v-if="$store.getters['auth/can']('USER_STOREPERMISSIONS')">
																		SYNC PERMISSION
																</v-btn>
																<v-btn
																		color="primary"
																		:loading="btnLoading"
																		:disabled="btnLoading"
																		@click.stop="printtoexcel"
																		v-if="$store.getters['auth/can']('AKADEMIK-KEMAHASISWAAN-DAFTAR-MAHASISWA_BROWSE')">
																		Cetak
																		<v-icon right>mdi-printer</v-icon>
																</v-btn>
														</v-toolbar>
												</template>
												<template v-slot:item.idkelas="{item}">
														{{$store.getters['uiadmin/getNamaKelas'](item.idkelas)}}
												</template>                        
												<template v-slot:item.k_status="{item}">
														{{$store.getters['uiadmin/getStatusMahasiswa'](item.k_status)}}
												</template>                        
												<template v-slot:expanded-item="{ headers, item }">
														<td :colspan="headers.length" class="text-center">
																<v-col cols="12">                          
																		<strong>id:</strong>{{ item.user_id }}          
																		<strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
																		<strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
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
import AkademikLayout from '@/views/layouts/AkademikLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter7 from '@/components/sidebar/FilterMode7';

export default {
		name: 'KemahasiswaanDaftarMahasiswa',
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
								text:'KEMAHASISWAAN',
								disabled:false,
								href:'#'
						},
						{
								text:'DAFTAR MAHASISWA',
								disabled:true,
								href:'#'
						}
				];
				let prodi_id=this.$store.getters['uiadmin/getProdiID'];
				this.prodi_id=prodi_id;
				this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);
				this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];    
				this.initialize()
		},
		data: () => ({ 
				firstloading:true,
				prodi_id:null,
				nama_prodi:null,
				tahun_pendaftaran:null,

				btnLoading: false,
				btnLoadingTable:false,
				datatableLoading:false,
				expanded: [],
				datatable: [],    
				headers: [
						{ text: 'NO. FORMULIR', value: 'no_formulir', sortable:true,width:150  }, 
						{ text: 'NIM', value: 'nim', sortable:true,width:150  }, 
						{ text: 'NIRM', value: 'nirm', sortable:true,width:150  }, 
						{ text: 'NAMA MAHASISWA', value: 'nama_mhs',sortable:true },       
						{ text: 'KELAS', value: 'idkelas',sortable:true,width:120, },                   
						{ text: 'STATUS', value: 'k_status',sortable:true,width:120, },                   
				],
				search: "", 
		}),
		methods: {
				changeTahunPendaftaran (tahun)
				{
						this.tahun_pendaftaran=tahun;
				},
				changeProdi (id)
				{
						this.prodi_id=id;
				},
				initialize:async function() 
				{
						this.datatableLoading=true;
						await this.$ajax.post('/akademik/kemahasiswaan/daftarmhs',
						{
								prodi_id: this.prodi_id,
								ta: this.tahun_pendaftaran
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {               
								this.datatable = data.mahasiswa;
								this.datatableLoading=false;
						}).catch(() => {
								this.datatableLoading=false;
						});  
						this.firstloading=false;
						this.$refs.filter7.setFirstTimeLoading(this.firstloading); 
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
				printtoexcel:async function()
				{
						this.btnLoading = true;
						await this.$ajax.post('/akademik/kemahasiswaan/daftarmhs/printtoexcel',
								{
										TA: this.tahun_pendaftaran,                                            
										prodi_id: this.prodi_id,  
										nama_prodi: this.nama_prodi,               
								},
								{
										headers: {
												Authorization: this.$store.getters['auth/Token']
										},
										responseType:'arraybuffer'
								}
						).then(({ data }) => {              
								const url = window.URL.createObjectURL(new Blob([data]));
								const link = document.createElement('a');
								link.href = url;
								link.setAttribute('download', 'daftar_mahasiswa_'+Date.now()+'.xlsx');    
								link.setAttribute('id', 'download_laporan');    
								document.body.appendChild(link);
								link.click();         
								document.body.removeChild(link);
								this.btnLoading = false;
						}).catch(() => {
								this.btnLoading = false;
						});     
				},  
				syncPermission:async function()
				{
						this.btnLoading = true;
						await this.$ajax.post('/system/users/syncallpermissions',
								{
										role_name:'mahasiswa',
										TA: this.tahun_pendaftaran,
										prodi_id: this.prodi_id                     
								},
								{
										headers: {
												Authorization: this.$store.getters['auth/Token']
										}
								}
						).then(() => {                   
								this.btnLoading = false;
						}).catch(() => {
								this.btnLoading = false;
						});     
				},
		},
		watch: {
				tahun_pendaftaran()
				{
						if (!this.firstloading)
						{
								this.initialize();
						}            
				},
				prodi_id(val)
				{
						if (!this.firstloading)
						{
								this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](val);
								this.initialize();
						}            
				}
		},
		components: {
				AkademikLayout,
				ModuleHeader,  
				Filter7               
		},
}
</script>