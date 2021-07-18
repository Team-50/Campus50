<template>
		<AkademikLayout :showrightsidebar="false">
				<ModuleHeader>
						<template v-slot:icon>
								mdi-format-columns
						</template>
						<template v-slot:name>
								KARTU RENCANA STUDI
						</template>
						<template v-slot:subtitle v-if="Object.keys(datakrs).length">
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
										Halaman untuk melihat detail krs mahasiswa 
								</v-alert>
						</template>
				</ModuleHeader>   
				<v-container fluid v-if="Object.keys(datakrs).length">   
						<v-row>   
								<v-col cols="12">
										<v-card>
												<v-card-title>
														<span class="headline">DATA KRS</span>
												</v-card-title>
												<v-card-text>
														<v-row no-gutters>
																<v-col xs="12" sm="6" md="6">
																		<v-card flat>
																				<v-card-title>ID KRS:</v-card-title>
																				<v-card-subtitle>
																						{{datakrs.id}}
																				</v-card-subtitle>
																		</v-card>
																</v-col>
																<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																<v-col xs="12" sm="6" md="6">
																		<v-card flat>
																				<v-card-title>SAH :</v-card-title>
																				<v-card-subtitle>
																						<v-chip label outlined color="info">{{datakrs.sah==1?'YA':'TIDAK'}}</v-chip>
																				</v-card-subtitle>
																		</v-card>
																</v-col>
																<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																<v-col xs="12" sm="6" md="6">
																		<v-card flat>
																				<v-card-title>NIM:</v-card-title>
																				<v-card-subtitle>
																						{{datakrs.nim}}
																				</v-card-subtitle>
																		</v-card>
																</v-col>
																<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																<v-col xs="12" sm="6" md="6">
																		<v-card flat>
																				<v-card-title>JUMLAH MATKUL / SKS :</v-card-title>
																				<v-card-subtitle>
																						{{totalMatkul}} / {{totalSKS}}
																				</v-card-subtitle>
																		</v-card>
																</v-col>
																<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                
																<v-col xs="12" sm="6" md="6">
																		<v-card flat>
																				<v-card-title>NAMA MAHASISWA:</v-card-title>
																				<v-card-subtitle>
																						{{datakrs.nama_mhs}}
																				</v-card-subtitle>
																		</v-card>
																</v-col>
																<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
																<v-col xs="12" sm="6" md="6">
																		<v-card flat>
																				<v-card-title>CREATED / UPDATED :</v-card-title>
																				<v-card-subtitle>
																						{{$date(datakrs.created_at).format('DD/MM/YYYY HH:mm')}} / {{$date(datakrs.updated_at).format('DD/MM/YYYY HH:mm')}}
																				</v-card-subtitle>
																		</v-card>
																</v-col>
																<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
														</v-row>            
												</v-card-text>
										</v-card>
								</v-col>                
						</v-row>
						<v-row>
								<v-col cols="12">           
										<v-card>
												<v-card-title>
														DAFTAR MATAKULIAH
														<v-spacer></v-spacer> 
														<v-btn color="primary" icon outlined small class="ma-2" :to="{path:'/akademik/perkuliahan/krs/'+this.krs_id+'/tambahmatkul'}">
																<v-icon>mdi-plus</v-icon>
														</v-btn>             
												</v-card-title>
												<v-card-text>
														<v-data-table        
																dense                        
																:headers="headers"
																:items="datatable"                                
																item-key="id"                                                        
																:disable-pagination="true"
																:hide-default-footer="true"                                                                
																:loading="datatableLoading"
																loading-text="Loading... Please wait">     
																<template v-slot:item.actions="{ item }">         
																		<v-btn
																				small
																				icon
																				:loading="btnLoading"
																				:disabled="btnLoading"
																				@click.stop="deleteItem(item)">
																				<v-icon>
																						mdi-delete
																				</v-icon>
																		</v-btn>   
																</template> 
																<template v-slot:body.append v-if="datatable.length > 0">
																		<tr class="grey lighten-4 font-weight-black">
																				<td class="text-right" colspan="2">TOTAL MATAKULIAH</td>
																				<td>{{totalMatkul}}</td> 
																				<td></td>
																				<td></td>
																				<td></td>
																				<td></td>
																		</tr>
																		<tr class="grey lighten-4 font-weight-black">
																				<td class="text-right" colspan="2">TOTAL SKS</td>
																				<td>{{totalSKS}}</td> 
																				<td></td>
																				<td></td>
																				<td></td>
																				<td></td>
																		</tr>
																</template>   
																<template v-slot:no-data>
																		Data matakuliah belum tersedia silahkan tambah
																</template>
														</v-data-table>
												</v-card-text>
										</v-card>
								</v-col>
						</v-row>
				</v-container>
		</AkademikLayout>
</template>
<script>
import AkademikLayout from '@/views/layouts/AkademikLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
		name: 'PerkuliahanKRSDetail',
		created() {
				this.krs_id=this.$route.params.krsid;        
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
								text:'KRS',
								disabled:false,
								href:'/akademik/perkuliahan/krs/daftar'
						},
						{
								text:'DETAIL',
								disabled:true,
								href:'#'
						},
				];
				this.fetchKRS();   
		},
		data: () => ({ 
				firstloading:true,      
				nama_prodi:null,
				tahun_akademik:null,      
				semester_akademik:null,
		
				btnLoading: false, 
				btnLoadingTable:false,

				//formdata
				krs_id:null,
				datakrs: {},
				
				//table        
				datatableLoading:false,
				expanded: [],
				datatable: [],    
				headers: [
						{ text: 'KODE', value: 'kmatkul', sortable:true,width:100  }, 
						{ text: 'NAMA MATAKULIAH', value: 'nmatkul',sortable:true },             
						{ text: 'SKS', value: 'sks', sortable:false,width:50 },       
						{ text: 'SMT', value: 'semester', sortable:false,width:50 },       
						{ text: 'KELAS', value: 'nama_kelas', sortable:false,width:200 },       
						{ text: 'NAMA DOSEN', value: 'nama_dosen', sortable:false,width:200 },       
						{ text: 'AKSI', value: 'actions', sortable:false,width:50 },       
				],
		}),
		methods: {          
				async fetchKRS()
				{
						await this.$ajax.get('/akademik/perkuliahan/krs/'+this.krs_id,    
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                               
								this.datakrs=data.krs;    
								this.datatable=data.krsmatkul;    
								if (Object.keys(this.datakrs).length)
								{
										let prodi_id=this.datakrs.kjur;        
										this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);    
										this.tahun_akademik=this.datakrs.tahun;                                          
										this.semester_akademik=this.datakrs.idsmt;            
								}
						})  
				},   
				deleteItem (item)
				{
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus matakuliah ('+item.nmatkul+') ?', { color: 'red',width:600,'desc':'proses ini juga menghapus seluruh data yang terkait dengan matkul ini.' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoadingTable=true;
										this.$ajax.post('/akademik/perkuliahan/krs/deletematkul/'+item.id,
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
		},
		computed: {
				totalMatkul()
				{
						return this.datatable.length;
				},
				totalSKS()
				{
						var total = 0;
						var index;
						for (index in this.datatable)
						{
								total = total + parseInt(this.datatable[index].sks);
						}            
						return total;
				}
		},
		components: {
				AkademikLayout,
				ModuleHeader,          
		},
}
</script>