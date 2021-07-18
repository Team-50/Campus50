<template>
		<AkademikLayout>
				<ModuleHeader>
						<template v-slot:icon>
								mdi-monitor-dashboard
						</template>
						<template v-slot:name>
								DAFTAR ULANG MAHASISWA LAMA 
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
										Halaman untuk melakukan daftar mahasiswa lama baik itu aktif, non-aktif, lulus, keluar, drop-out, dan status lainnya.
								</v-alert>
						</template>
				</ModuleHeader>
				<template v-slot:filtersidebar>
						<Filter6 v-on:changeTahunAkademik="changeTahunAkademik" v-on:changeSemesterAkademik="changeSemesterAkademik" v-on:changeProdi="changeProdi" ref="filter6" />	
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
												:disable-pagination="true"
												:hide-default-footer="true"
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
														</v-toolbar>
												</template>
												<template v-slot:item.idkelas="{item}">
														{{$store.getters['uiadmin/getNamaKelas'](item.idkelas)}}
												</template>
												<template v-slot:item.actions="{ item }">
													<v-icon
														small
														:loading="btnLoading"
														:disabled="btnLoading"
														@click.stop="deleteItem(item)">
														mdi-delete
													</v-icon>    
												</template>           
												<template v-slot:expanded-item="{ headers, item }">
														<td :colspan="headers.length" class="text-center">
																<v-col cols="12">                          
																		<strong>id:</strong>{{ item.id }}          
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
import Filter6 from '@/components/sidebar/FilterMode6';
export default {
		name: 'DulangMahasiswaBaru',
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
								text:'DAFTAR ULANG',
								disabled:false,
								href:'#'
						},
						{
								text:'DAFTAR ULANG MAHASISWA LAMA',
								disabled:true,
								href:'#'
						}
				];
				let prodi_id=this.$store.getters['uiadmin/getProdiID'];
				this.prodi_id=prodi_id;
				this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);
				this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];
				this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];   
				this.initialize()
		},
		data: () => ({ 
				firstloading:true,
				prodi_id:null,
				nama_prodi:null,
				tahun_akademik:null,
				semester_akademik:null,

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
						{ text: 'STATUS', value: 'n_status',sortable:true,width:120, },       
						{ text: 'AKSI', value: 'actions', sortable: false,width:100 },
				],
				search: "", 
		}),
		methods: {
				changeTahunAkademik (tahun)
				{
						this.tahun_akademik=tahun;
				},
				changeSemesterAkademik (semester)
				{
						this.semester_akademik=semester;
				},
				changeProdi (id)
				{
						this.prodi_id=id;
				},
				initialize:async function() 
				{
						this.datatableLoading=true;
						await this.$ajax.post('/akademik/dulang/mhslama',
						{
								prodi_id: this.prodi_id,
								ta: this.tahun_akademik,
								idsmt: this.semester_akademik,
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
						this.$refs.filter6.setFirstTimeLoading(this.firstloading); 
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
				deleteItem (item)
				{
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus daftar ulang '+item.nama_mhs+' ?', { color: 'red',width:600,'desc':'proses ini juga menghapus seluruh data akademik namun KEUANGAN TETAP ADA.' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoadingTable=true;
										this.$ajax.post('/akademik/dulang/mhslama'+item.id,
												{
														'_method':'DELETE',
												},
												{
														headers: {
																Authorization: this.$store.getters['auth/Token']
														}
												}
										).then(() => {   
												const index = this.datatable.indexOf(item);
												this.datatable.splice(index, 1);
												this.btnLoadingTable=false;
										}).catch(() => {
												this.btnLoadingTable=false;
										});
								}                
						});
				},
				closedialogfrm() {            
						this.dialogfrm = false;
						setTimeout(() => {       
								this.formdata = Object.assign({}, this.formdefault);                    
								this.data_mhs = Object.assign({}, {});   
								}, 300
						);
				},
		},
		watch: {
				tahun_akademik()
				{
						if (!this.firstloading)
						{
								this.initialize();
						}            
				},
				semester_akademik()
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
				Filter6               
		},
}
</script>