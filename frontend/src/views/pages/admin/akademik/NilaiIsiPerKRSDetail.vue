<template>
		<AkademikLayout :showrightsidebar="false">
				<ModuleHeader>
						<template v-slot:icon>
								mdi-format-columns
						</template>
						<template v-slot:name>
								ISI NILAI PER KRS
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
										Halaman untuk melakukan pengisian nilai berdasarkan krs mahasiswa per tahun akademik, dan semester yang telah dilakukan.
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
										<v-form ref="frmdata" v-model="form_valid" lazy-validation>      
												<v-card>
														<v-card-title>
																DAFTAR MATAKULIAH
																<v-spacer></v-spacer> 
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
																		<template v-slot:item.n_kuan="props">         
																				<v-numeric                
																						v-model="props.item.n_kuan"
																						text
																						:min="0"
																						:max="100"
																						locale="en-US"
																						:useGrouping="false"
																						precision="2"
																						dense
																						style="width:65px"
																						:calcIcon="null"
																						:useCalculator="false">
																				</v-numeric> 
																				<v-chip color="primary" class="ma-2" outlined label v-if="props.item.n_kuan != null">{{props.item.n_kuan}}</v-chip>
																		</template> 
																		<template v-slot:item.n_kual="props">         
																				<v-select 
																						:items="$store.getters['uiadmin/getSkalaNilai']" 
																						v-model="props.item.n_kual"
																						style="width:65px"
																						dense>
																		</v-select>
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
														<v-card-actions>
																<v-spacer></v-spacer>
																<v-btn color="blue darken-1" text href="/akademik/nilai/matakuliah/isiperkrs">KELUAR</v-btn>
																<v-btn 
																		color="blue darken-1" 
																		text 
																		@click.stop="save" 
																		:loading="btnLoading"
																		:disabled="btnLoading">
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
		name: 'NilaiIsiPerKRSDetail',
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
								text:'ISI NILAI',
								disabled:false,
								href:'#'
						},
						{
								text:'PER KRS',
								disabled:true,
								href:'#'
						}
				];  
				this.fetchKRS();   
		},
		data: () => ({ 
				test:100,
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
						{ text: 'KODE', value: 'kmatkul', sortable:true,width:120  }, 
						{ text: 'NAMA MATAKULIAH', value: 'nmatkul',sortable:true },             
						{ text: 'SKS', value: 'sks', sortable:false,width:120 },       
						{ text: 'SMT', value: 'semester', sortable:false,width:120 },       
						{ text: 'KELAS', value: 'nama_kelas', sortable:false,width:120 },       
						{ text: 'NILAI ANGKA (0 s.d 100)', value: 'n_kuan', sortable:false,width:100 },       
						{ text: 'NILAI HURUF', value: 'n_kual', sortable:false,width:100 },											 
				],
				//formdata
				form_valid:true,               
		}),
		methods: {          
				async fetchKRS()
				{
						await this.$ajax.get('/akademik/nilai/matakuliah/perkrs/'+this.krs_id,    
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
				async save ()
				{           
						this.btnLoadingTable=true;
						var daftar_nilai=[];

						this.datatable.forEach(item => {
								daftar_nilai.push({
										krsmatkul_id:item.id,
										n_kuan:item.n_kuan,
										n_kual:item.n_kual
								});
						});
						await this.$ajax.post('/akademik/nilai/matakuliah/perkrs/storeperkrs',
								{
										krs_id: this.krs_id,
										daftar_nilai:JSON.stringify(Object.assign({},daftar_nilai)),
								},
								{
										headers: {
												Authorization: this.$store.getters['auth/Token']
										}
								}
						).then(() => {                   
								this.$router.go();
						}).catch(() => {
								this.btnLoadingTable=false;
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