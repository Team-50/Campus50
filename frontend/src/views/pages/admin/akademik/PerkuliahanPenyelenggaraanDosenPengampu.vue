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
						<v-row class="mb-4">   
								<v-col cols="12">
										<DataMatakuliahPenyelenggaraan :datamatkul="data_matkul"></DataMatakuliahPenyelenggaraan>
								</v-col>
								<v-col cols="12">                    
										<v-form ref="frmdata" v-model="form_valid" lazy-validation>
												<v-card>
														<v-card-title>
																TAMBAH DOSEN PENGAMPU
														</v-card-title>
														<v-card-text>
																<v-alert type="info">
																		Silahkan pilih dosen untuk mengampu matakuliah ini 
																</v-alert>     
																<v-autocomplete
																		label="DOSEN"
																		v-model="formdata.dosen_id"
																		:items="daftar_dosen"
																		item-text="nama_dosen"
																		item-value="user_id"
																		:rules="rule_dosen"
																		outlined/>    
																<v-switch
																		v-model="formdata.is_ketua"
																		label="SEBAGAI KETUA GROUP DOSEN PENGAMPU">
																</v-switch>                                                   
														</v-card-text>
														<v-card-actions>
																<v-spacer></v-spacer>
																<v-btn 
																		color="blue darken-1" 
																		text 
																		@click.stop="$router.push('/akademik/perkuliahan/penyelenggaraan/daftar')">
																				BATAL
																		</v-btn>
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
								<v-col cols="12">           
										<v-card>
												<v-card-title>
														DAFTAR DOSEN PENGAMPU
												</v-card-title>
												<v-card-text>
														<v-data-table                                
																:headers="headers"
																:items="datatable"                                
																item-key="id"                                                        
																:disable-pagination="true"
																:hide-default-footer="true"                                
																class="elevation-1"
																:loading="datatableLoading"
																loading-text="Loading... Please wait">
																<template v-slot:item.is_ketua="{ item }">         
																		<v-switch
																				v-model="item.is_ketua"
																				:label="item.is_ketua == 1?'YA':'TIDAK'"
																				@change="updateketua(item)">
																		</v-switch>  
																</template>
																<template v-slot:item.actions="{ item }">         
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
																<template v-slot:no-data>
																		Data dosen pengampu penyelenggaraan matakuliah ini belum tersedia
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
import DataMatakuliahPenyelenggaraan from '@/components/DataMatakuliahPenyelenggaraan';
export default {
		name: 'PerkuliahanPenyelenggaraanDosenPengampu',
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
								text:'DOSEN PENGAMPU',
								disabled:true,
								href:'#'
						},
				];
				let prodi_id=this.$store.getters['uiadmin/getProdiID'];
				this.prodi_id=prodi_id;
				this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);
				this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];          
				this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];            
				this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];    
				this.formdata.idpenyelenggaraan=this.$route.params.idpenyelenggaraan;
				this.initialize();
		}, 
		mounted()
		{
				this.fetchMatkul(); 
				this.fetchDosenPengampu(); 
		},
		data: () => ({ 
				firstloading:true,
				prodi_id:null,
				nama_prodi:null,
				tahun_akademik:null,      
				semester_akademik:null,

				btnLoading: false,      
				btnLoadingTable:false,

				//table        
				datatableLoading:false,
				expanded: [],
				datatable: [],    
				headers: [
						{ text: 'NIDN', value: 'nidn', sortable:false,width:120  }, 
						{ text: 'NAMA DOSEN', value: 'nama_dosen',sortable:false },             
						{ text: 'KETUA', value: 'is_ketua', sortable:false,width:120 },       
						{ text: 'AKSI', value: 'actions', sortable:false,width:120 },       
				],
				
				//formdata
				form_valid:true, 
				data_matkul:null,
				daftar_dosen: [],
				formdata: {
						idpenyelenggaraan:null,
						dosen_id:null,
						is_ketua:false,
				},      
				formdefault: {
						idpenyelenggaraan:null,
						dosen_id:null,
						is_ketua:false,
				},      
				rule_dosen: [
						value => !!value || "Mohon dipilih Dosen untuk matakuliah ini !!!"
				],
		}),
		methods: {   
				async initialize ()
				{
						await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/pengampu',          
						{
								idpenyelenggaraan: this.formdata.idpenyelenggaraan,
								pid:'terdaftar'
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                               
								this.datatable = data.dosen;    
						})  
				},
				async fetchMatkul ()
				{
						await this.$ajax.get('/akademik/perkuliahan/penyelenggaraanmatakuliah/'+this.formdata.idpenyelenggaraan,          
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                               
								this.data_matkul = data.penyelenggaraan;    
						})  
				},
				async fetchDosenPengampu ()
				{
						await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/pengampu',          
						{
								idpenyelenggaraan: this.formdata.idpenyelenggaraan,
								pid:'belumterdaftar'
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                               
								this.daftar_dosen = data.dosen;    
						})  
				},
				save:async function() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;
								await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/storedosenpengampu',
										{
												penyelenggaraan_id: this.formdata.idpenyelenggaraan, 
												dosen_id: this.formdata.dosen_id,                                
												is_ketua: this.formdata.is_ketua,                                                                                                           
										},
										{
												headers: {
														Authorization: this.$store.getters['auth/Token']
												}
										}
								).then(() => {   
										setTimeout(() => {
												this.btnLoading = false;        
												this.$router.go();                     
												}, 500
										);
										
								}).catch(() => {
										this.btnLoading = false;
								});
						}
				},
				deleteItem (item)
				{
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus DOSEN PENGAMPU matakuliah ('+item.nama_dosen+') ?', { color: 'red',width:600,'desc':'proses ini juga menghapus seluruh data yang terkait dalam penyelenggaraan matkul ini.' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoadingTable=true;
										this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/deletepengampu/'+item.id,
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
				async updateketua(item)
				{
						await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/updateketua/'+item.id,
								{
										_method:'put',
										penyelenggaraan_id:item.penyelenggaraan_id,                                                                                                           
										is_ketua:item.is_ketua,                                                                                                           
								},
								{
										headers: {
												Authorization: this.$store.getters['auth/Token']
										}
								}
						).then(() => {   
								setTimeout(() => {                                     
										this.initialize();                     
										}, 500
								);
								
						}).catch(() => {
								this.btnLoading = false;
						});
				}
		},
		components: {
				AkademikLayout,
				ModuleHeader, 
				DataMatakuliahPenyelenggaraan           
		},
}
</script>