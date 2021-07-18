<template>
		<AkademikLayout>
				<ModuleHeader>
						<template v-slot:icon>
								mdi-google-classroom
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
										Halaman untuk melakukan pembentukan kelas yang akan diselenggarakan dan juga dikaitkan dengan penyelenggaraan matakuliah.
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
																<v-toolbar-title>DAFTAR PEMBAGIAN KELAS</v-toolbar-title>
																<v-divider
																		class="mx-4"
																		inset
																		vertical
																></v-divider>
																<v-spacer></v-spacer>     
																<v-btn color="warning" elevation="0" small class="mr-2 primary" to="/akademik/perkuliahan/pembagiankelas/tambah" v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PEMGBAGIAN-KELAS_STORE')">
																		<v-icon size="21px">mdi-plus-circle</v-icon>
																</v-btn>
																<v-btn color="indigo darken-3" elevation="0" small class="primary">
																		<v-icon>mdi-printer</v-icon>
																</v-btn>
																<v-dialog v-model="dialogfrm" max-width="750px" persistent>         
																		<v-form ref="frmdata" v-model="form_valid" lazy-validation>
																				<v-card>
																						<v-card-title>
																								<span class="headline">UBAH DATA KELAS</span>
																						</v-card-title>
																						<v-card-text>
																								<v-row>
																										<v-col cols="4">
																												<v-select
																														v-model="formdata.hari"
																														:items="daftar_hari"                                                    
																														label="HARI"
																														:rules="rule_hari"        
																														outlined/> 
																										</v-col>
																										<v-col cols="4">
																												<v-text-field 
																														v-model="formdata.jam_masuk" 
																														label="JAM MASUK (contoh: 04:00)"
																														outlined
																														:rules="rule_jam_masuk">
																												</v-text-field>
																										</v-col>
																										<v-col cols="4">
																												<v-text-field 
																														v-model="formdata.jam_keluar" 
																														label="JAM KELUAR (contoh: 06:00)"
																														outlined
																														:rules="rule_jam_keluar">
																												</v-text-field>
																										</v-col>
																								</v-row>
																								<v-select
																										v-model="formdata.ruang_kelas_id"
																										:items="daftar_ruang_kelas"                                                    
																										label="RUANG KELAS"
																										:rules="rule_ruang_kelas"
																										item-text="namaruang"
																										item-value="id"
																										outlined/> 
																								<v-select
																										v-model="formdata.zoom_id"
																										:items="daftar_zoom"                                                   
																										label="AKUN ZOOM"                                    
																										item-text="email"
																										item-value="id"
																										outlined/>
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
														</v-toolbar>
												</template>
												<template v-slot:item.nmatkul="{item}">
														{{item.nmatkul}} - {{$store.getters['uiadmin/getNamaKelas'](item.idkelas)}}
												</template>
												<template v-slot:item.jam_masuk="{item}">
														{{item.jam_masuk}}-{{item.jam_keluar}}
												</template>
												<template v-slot:item.actions="{ item }" v-if="CAN_ACCESS('AKADEMIK-PERKULIAHAN-PENYELENGGARAAN_STORE')">
														<v-btn
																small
																icon
																@click.stop="$router.push('/akademik/perkuliahan/pembagiankelas/'+item.id+'/peserta')">
																<v-icon>
																		mdi-account-child-outline
																</v-icon>
														</v-btn>   
														<v-btn
																small
																icon
																:loading="btnLoadingTable"
																:disabled="btnLoadingTable"
																@click.stop="editItem(item)">
																<v-icon>
																		mdi-pencil
																</v-icon>
														</v-btn>   
														<v-btn
																small
																icon
																:loading="btnLoadingTable"
																:disabled="btnLoadingTable"
																@click.stop="deleteItem(item)">
																<v-icon>
																		mdi-delete
																</v-icon>
														</v-btn>   
												</template>           
												<template v-slot:item.actions v-else>
														N.A
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
		name: 'PerkuliahanPembagianKelas',
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
				tahun_akademik:null,
				semester_akademik:null,

				btnLoading: false,
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
				
				//dialog
				dialogfrm:false,

				//formdata
				form_valid:true, 
				daftar_ruang_kelas: [],
				daftar_hari: [
						{
								text:'SENIN',
								value:1,
						},
						{
								text:'SELASA',
								value:2,
						},
						{
								text:'RABU',
								value:3,
						},
						{
								text:'KAMIS',
								value:4,
						},
						{
								text:'JUMAT',
								value:5,
						},
						{
								text:'SABTU',
								value:6,
						},
				],
				daftar_zoom: [],
				formdata: {            
						id: "",
						zoom_id: "",
						idkelas: "",          
						hari: "",          
						jam_masuk: "",
						jam_keluar: "",
						penyelenggaraan_dosen_id: "",
						ruang_kelas_id: "",          
				}, 
				formdefault: {            
						id: "",
						zoom_id: "",
						idkelas: "",          
						hari: "",          
						jam_masuk: "",
						jam_keluar: "",
						penyelenggaraan_dosen_id: "",
						ruang_kelas_id: "",          
				},       

				rule_hari: [
						value => !!value || "Mohon dipilih hari mengajar!!!"
				],
				rule_jam_masuk: [
						value => !!value || "Mohon diisi jam masuk mengajar!!!",
						value => /^([0-9]|0[0-9]|1[0-9]|2[0-3]): [0-5][0-9]$/.test(value) || 'Format jam masuk mengajar hh:mm, misalnya 15:30'
				],
				rule_jam_keluar: [
						value => !!value || "Mohon diisi jam keluar mengajar!!!",
						value => /^([0-9]|0[0-9]|1[0-9]|2[0-3]): [0-5][0-9]$/.test(value) || 'Format jam keluar mengajar hh:mm, misalnya 15:00'
				],
				rule_ruang_kelas: [
						value => !!value || "Mohon dipilih ruang kelas mengajar!!!"
				],

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
				async editItem (item) {
						await this.$ajax.get('/datamaster/ruangankelas',{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {
								this.daftar_ruang_kelas = data.ruangan;     
								this.formdata = Object.assign({}, item);
								this.dialogfrm = true;   
						});
						await this.$ajax.get(process.env.VUE_APP_API_HOST+'/h2h/zoom',     
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                               
								this.daftar_zoom = data.zoom;    
						});
				},  
				save:async function() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;
								
								await this.$ajax.post('/akademik/perkuliahan/pembagiankelas/'+this.formdata.id,
										{
												'_method':'PUT',    
												hari: this.formdata.hari,        
												jam_masuk: this.formdata.jam_masuk,
												jam_keluar: this.formdata.jam_keluar,    
												ruang_kelas_id: this.formdata.ruang_kelas_id,        
										},
										{
												headers: {
														Authorization: this.$store.getters['auth/Token']
												}
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
				deleteItem (item)
				{
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus pembagian kelas matakuliah ('+item.nmatkul+') ?', { color: 'red',width:600,'desc':'proses ini membuat mahasiswa tidak memiliki kelas.' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoadingTable=true;
										this.$ajax.post('/akademik/perkuliahan/pembagiankelas/'+item.id,
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
								this.$refs.frmdata.reset(); 
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