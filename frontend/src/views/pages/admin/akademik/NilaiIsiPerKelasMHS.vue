<template>
		<AkademikLayout>
				<ModuleHeader>
						<template v-slot:icon>
								mdi-monitor-dashboard
						</template>
						<template v-slot:name>
								ISI NILAI PER KELAS
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
										Halaman untuk melakukan pengisian nilai berdasarkan kelas mahasiswa yang telah dibentuk pada pembagian kelas.
								</v-alert>
						</template>
				</ModuleHeader>
				<template v-slot:filtersidebar>
						<Filter2 v-on:changeTahunAkademik="changeTahunAkademik" v-on:changeSemesterAkademik="changeSemesterAkademik" ref="filter2" />	
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
																<v-toolbar-title>DAFTAR KELAS</v-toolbar-title>
																<v-divider
																		class="mx-4"
																		inset
																		vertical
																></v-divider>
																<v-spacer></v-spacer>     
														</v-toolbar>
												</template>
												<template v-slot:item.nmatkul="{item}">
														{{item.nmatkul}} - {{$store.getters['uiadmin/getNamaKelas'](item.idkelas)}}
												</template>
												<template v-slot:item.jam_masuk="{item}">
														{{item.jam_masuk}}-{{item.jam_keluar}}
												</template>
												<template v-slot:item.actions="{ item }">
														<v-btn
																small
																icon
																@click.stop="$router.push('/akademik/nilai/matakuliah/isiperkelasmhs/'+item.id)"
																v-if="item.jumlah_mhs > 0">
																<v-icon>
																		mdi-video-input-svideo
																</v-icon>
														</v-btn>  
														<span v-else>
																N.A
														</span>
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
								</v-col>
						</v-row>            
				</v-container>
		</AkademikLayout>
</template>
<script>
import AkademikLayout from '@/views/layouts/AkademikLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter2 from '@/components/sidebar/FilterMode2';

import {mapGetters} from 'vuex';

export default {
		name: 'NilaiIsiPerKelasMHS',
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
								text:'ISI NILAI',
								disabled:false,
								href:'#'
						},
						{
								text:'PER KELAS MAHASISWA',
								disabled:true,
								href:'#'
						}
				];
				this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];    
				this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];    
				this.initialize()
		},
		data: () => ({ 
				firstloading:true,      
				daftar_ta: [],
				tahun_akademik:null,
				semester_akademik:null,

				btnLoadingTable:false,
				datatableLoading:false,
				expanded: [],
				datatable: [],    
				headers: [
						{ text: 'KODE', value: 'kmatkul', sortable:true,width:100  }, 
						{ text: 'NAMA MATAKULIAH/KELAS', value: 'nmatkul', sortable:true  }, 
						{ text: 'NAMA DOSEN', value: 'nama_dosen', sortable:true  },       
						{ text: 'HARI', value: 'nama_hari', sortable:true, width:100 },             
						{ text: 'JAM', value: 'jam_masuk',sortable:true, width:100 },       
						{ text: 'RUANG', value: 'namaruang',sortable:true, width:100},       
						{ text: 'JUMLAH PESERTA', value: 'jumlah_mhs',sortable:true, width:100},       
						{ text: 'AKSI', value: 'actions', sortable: false,width:120 },
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
				initialize:async function() 
				{
						this.datatableLoading=true;
						await this.$ajax.post('/akademik/perkuliahan/pembagiankelas',
						{
								ta: this.tahun_akademik,
								semester_akademik: this.semester_akademik,
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                               
								this.datatable = data.pembagiankelas;
								this.datatableLoading=false;
						}).catch(() => {
								this.datatableLoading=false;
						});  
						this.firstloading=false;
						this.$refs.filter2.setFirstTimeLoading(this.firstloading); 
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
		},
		computed: {
				...mapGetters('auth',{            
						CAN_ACCESS:'can', 
				}),
		},
		components: {
				AkademikLayout,
				ModuleHeader,  
				Filter2               
		},
}
</script>