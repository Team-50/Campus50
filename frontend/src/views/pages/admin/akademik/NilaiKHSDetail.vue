<template>
		<AkademikLayout :showrightsidebar="false">
				<ModuleHeader>
						<template v-slot:icon>
								mdi-format-columns
						</template>
						<template v-slot:name>
								KARTUS HASIL STUDI (KHS)
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
										Halaman untuk melihat daftar nilai matakuliah per tahun akademik, dan semester yang telah dilakukan.
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
																				<v-card-title>CREATED :</v-card-title>
																				<v-card-subtitle>
																						{{$date(datakrs.created_at).format('DD/MM/YYYY HH:mm')}}
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
																				<v-card-title>UPDATED :</v-card-title>
																				<v-card-subtitle>
																						{{$date(datakrs.updated_at).format('DD/MM/YYYY HH:mm')}}
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
														<v-btn 
																color="primary" 
																fab 
																small
																@click.stop="printpdf"
																:loading="btnLoading"
																:disabled="btnLoading || !datakrs.hasOwnProperty('id')">
																<v-icon>mdi-printer</v-icon>
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
																<template v-slot:body.append v-if="datatable.length > 0">
																		<tr class="grey lighten-4 font-weight-black">
																				<td class="text-right" colspan="2">JUMLAH</td>
																				<td>{{jumlah_sks}}</td>              
																				<td></td>              
																				<td>{{jumlah_am}}</td>
																				<td>{{jumlah_m}}</td>
																				<td></td>     
																		</tr>
																		<tr class="grey lighten-4 font-weight-black">
																				<td class="text-right" colspan="2">IPS</td>
																				<td colspan="5">{{ips}}</td>              
																		</tr>         
																		<tr class="grey lighten-4 font-weight-black">
																				<td class="text-right" colspan="2">IPK</td>
																				<td colspan="5">{{ipk}}</td>              
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
				<v-dialog v-model="dialogprintpdf" max-width="500px" persistent>                
						<v-card>
								<v-card-title>
										<span class="headline">Print to PDF</span>
								</v-card-title>
								<v-card-text>
										<v-btn
												color="green"
												text
												:href="$api.storageURL+'/'+file_pdf"> 
												Download
										</v-btn>
								</v-card-text>
								<v-card-actions>
										<v-spacer></v-spacer>
										<v-btn color="blue darken-1" text @click.stop="closedialogprintpdf">CLOSE</v-btn> 
								</v-card-actions>
						</v-card>            
				</v-dialog>
		</AkademikLayout>
</template>
<script>
import AkademikLayout from '@/views/layouts/AkademikLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
		name: 'NilaiKHSDetail',
		created() {
				this.krs_id=this.$route.params.krs_id;  
				console.log();      
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
								text:'NILAI',
								disabled:false,
								href:'#'
						},
						{
								text:'KHS',
								disabled:false,
								href:'/akademik/nilai/khs'
						},
						{
								text:'DETAIL',
								disabled:true,
								href:'#'
						},
				];
				this.fetchKHS();   
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
						{ text: 'HM', value: 'HM', sortable:false,width:50 },       
						{ text: 'AM', value: 'AM', sortable:false,width:50 },       
						{ text: 'M', value: 'M', sortable:false,width:50 },       
						{ text: 'NAMA DOSEN', value: 'nama_dosen', sortable:false,width:200 },                                            
				],

				jumlah_sks:0,
				jumlah_matkul:0,
				jumlah_m:0,
				jumlah_am:0,
				ips:0,
				ipk:0,

				dialogprintpdf:false,
				file_pdf:null
		}),
		methods: {          
				async fetchKHS()
				{
						await this.$ajax.get('/akademik/nilai/khs/'+this.krs_id,    
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                               
								this.datakrs=data.krs;    
								this.datatable=data.daftar_nilai;    
								if (Object.keys(this.datakrs).length)
								{
										let prodi_id=this.datakrs.kjur;        
										this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);    
										this.tahun_akademik=this.datakrs.tahun;                                          
										this.semester_akademik=this.datakrs.idsmt;
										
										this.jumlah_sks=data.jumlah_sks;
										this.jumlah_matkul=data.jumlah_matkul;
										this.jumlah_m=data.jumlah_m;
										this.jumlah_am=data.jumlah_am;
										this.ips=data.ips;
										this.ipk=data.ipk;
								}
						})  
				},    
				async printpdf()
				{
						this.btnLoading = true;
						await this.$ajax.get('/akademik/nilai/khs/printpdf/'+this.krs_id,              
								{
										headers: {
												Authorization: this.$store.getters['auth/Token']
										},
										
								}
						).then(({ data }) => {                              
								this.file_pdf=data.pdf_file;
								this.dialogprintpdf=true;
								this.btnLoading = false;
						}).catch(() => {
								this.btnLoading = false;
						});     
				},
				closedialogprintpdf() {                  
						setTimeout(() => {
								this.file_pdf=null;
								this.dialogprintpdf = false;      
								}, 300
						);
				},      
		},  
		components: {
				AkademikLayout,
				ModuleHeader,          
		},
}
</script>