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
										Halaman untuk melakukan penambahan  kelas pada tahun akademik dan semester terpilih.
								</v-alert>
						</template>
				</ModuleHeader>        
				<v-container fluid>         
						<v-row class="mb-4" no-gutters>
								<v-col cols="12">
										<v-form ref="frmdata" v-model="form_valid" lazy-validation>
												<v-card class="mb-2">
														<v-card-title>PILIH DOSEN / PENGAMPU</v-card-title>
														<v-card-text>
																<v-autocomplete
																		label="DOSEN"
																		v-model="dosen_id"
																		:items="daftar_dosen"
																		item-text="nama_dosen"
																		item-value="user_id"
																		:rules="rule_dosen"
																		outlined/>    
														</v-card-text>
												</v-card>
												<v-card class="mb-2">
														<v-card-title>DATA MATAKULIAH</v-card-title>
														<v-card-text>
																<v-select
																		v-model="formdata.penyelenggaraan_dosen_id"
																		:items="daftar_matakuliah"
																		label="MATAKULIAH YANG DISELENGGARAKAN"
																		multiple
																		chips
																		hint="Pilihlah satu atau lebih matakuliah yang akan digabungkan dalam satu kelas"
																		persistent-hint
																		:disabled="dosen_id==null"
																		outlined
																		item-text="nmatkul"
																		item-value="id">
																</v-select>     
																<v-text-field 
																		v-model="formdata.kmatkul" 
																		label="KODE MATAKULIAH"
																		outlined                                    
																		:rules="rule_kode_matkul"
																		:disabled="dosen_id==null">
																</v-text-field>
																<v-text-field 
																		v-model="formdata.nmatkul" 
																		label="NAMA MATAKULIAH + NAMA KELAS"
																		outlined
																		:rules="rule_nama_matakuliah"
																		:disabled="dosen_id==null">
																</v-text-field>
																<v-select 
																		v-model="formdata.sks" 
																		label="SKS"
																		:items="daftar_sks"                                                    
																		outlined
																		:rules="rule_sks"
																		:disabled="dosen_id==null">
																</v-select>
														</v-card-text>
												</v-card>
												<v-card class="mb-2">
														<v-card-title>DATA KELAS</v-card-title>
														<v-card-text>
																<v-select
																		v-model="formdata.idkelas"
																		:items="daftar_kelas"                                                    
																		label="KELAS"
																		:rules="rule_kelas"
																		item-text="text"
																		item-value="id"
																		:disabled="dosen_id==null"
																		outlined/> 

																<v-row>
																		<v-col cols="4">
																				<v-select
																						v-model="formdata.hari"
																						:items="daftar_hari"                                                    
																						label="HARI"
																						:rules="rule_hari"                                            
																						:disabled="dosen_id==null"
																						outlined/> 
																		</v-col>
																		<v-col cols="4">
																				<v-text-field 
																						v-model="formdata.jam_masuk" 
																						label="JAM MASUK (contoh: 04:00)"
																						outlined
																						:rules="rule_jam_masuk"
																						:disabled="dosen_id==null">
																				</v-text-field>
																		</v-col>
																		<v-col cols="4">
																				<v-text-field 
																						v-model="formdata.jam_keluar" 
																						label="JAM KELUAR (contoh: 06:00)"
																						outlined
																						:rules="rule_jam_keluar"
																						:disabled="dosen_id==null">
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
																		outlined
																		:disabled="dosen_id==null"/> 
														</v-card-text>
												</v-card>
												<v-card class="mb-2">
														<v-card-title>PILIH AKUN ZOOM</v-card-title>
														<v-card-text>  
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
																<v-btn color="blue darken-1" text @click.stop="$router.push('/akademik/perkuliahan/pembagiankelas/daftar')">KEMBALI</v-btn>
																<v-btn 
																		color="blue darken-1" 
																		text 
																		@click.stop="save" 
																		:loading="btnLoading"
																		:disabled="!form_valid||btnLoading||dosen_id==null">
																				BUAT
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
								text:'TAMBAH',
								disabled:true,
								href:'#'
						}
				];        
				this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];    
				this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];    
				this.initialize()
		},
		data: () => ({         
				tahun_akademik:null,
				semester_akademik:null,

				btnLoading: false,      
				//formdata
				form_valid:true, 
				daftar_dosen: [],      
				dosen_id:null,
				daftar_zoom: [],

				daftar_sks: [
						1,2,3,4,5,6,7,8,9,10,11,12
				],
				
				daftar_matakuliah: [],

				daftar_kelas: [],

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
				formdata: {            
						id: "",
						user_id: "",          
						zoom_id: "",
						kmatkul: "",          
						nmatkul: "",          
						sks: "",          
						idkelas: "",    
						hari: "",          
						jam_masuk: "",
						jam_keluar: "",
						penyelenggaraan_dosen_id: "",
						ruang_kelas_id: "",   
				},
				rule_dosen: [
						value => !!value || "Mohon dipilih Dosen pengampu matakuliah !!!"
				],
				rule_kode_matkul: [
						value => !!value || "Kode Program Studi mohon untuk diisi !!!",          
				], 
				rule_nama_matakuliah: [
						value => !!value || "Mohon Nama Program Studi untuk diisi !!!",            
				], 
				rule_sks: [
						value => !!value || "Mohon SKS Matakuliah untuk dipilih !!!",            
				],               
				rule_matakuliah: [
						value => !!value || "Mohon dipilih matakuliah yang diselenggaran untuk dosen pengampu ini!!!"
				],
				rule_kelas: [
						value => !!value || "Mohon dipilih kelas matakuliah ini!!!"
				],
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
				initialize:async function() 
				{
						await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/pengampu',          
						{
								ta: this.$store.getters['uiadmin/getTahunAkademik'],              
								semester_akademik: this.$store.getters['uiadmin/getSemesterAkademik'],
								pid:'daftarpengampu'
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                               
								this.daftar_dosen = data.dosen;    
						});  

						await this.$ajax.get(process.env.VUE_APP_API_HOST+'/h2h/zoom',     
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                               
								this.daftar_zoom = data.zoom;    
						});

						await this.$ajax.get('/datamaster/ruangankelas',{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {
								this.daftar_ruang_kelas = data.ruangan;    
						});
				},
				save:async function() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;    
								await this.$ajax.post('/akademik/perkuliahan/pembagiankelas/store',
										{
												user_id: this.dosen_id,
												zoom_id: this.formdata.zoom_id, 
												idkelas: this.formdata.idkelas,        
												kmatkul: this.formdata.kmatkul,        
												nmatkul: this.formdata.nmatkul,        
												sks: this.formdata.sks,        
												hari: this.formdata.hari,        
												jam_masuk: this.formdata.jam_masuk,
												jam_keluar: this.formdata.jam_keluar,    
												penyelenggaraan_dosen_id:JSON.stringify(Object.assign({},this.formdata.penyelenggaraan_dosen_id)),
												ruang_kelas_id: this.formdata.ruang_kelas_id,                               
												tahun: this.tahun_akademik,        
												idsmt: this.semester_akademik,
										},
										{
												headers: {
														Authorization: this.$store.getters['auth/Token']
												}
										}
								).then(() => {                        
										this.btnLoading = false;
										this.$router.push('/akademik/perkuliahan/pembagiankelas/daftar');
								}).catch(() => {
										this.btnLoading = false;
								});
						}            
				},      
		},
		watch: {
				async dosen_id(val)
				{
						await this.$ajax.post('/akademik/perkuliahan/penyelenggaraanmatakuliah/matakuliah',          
						{
								user_id:val,
								ta: this.$store.getters['uiadmin/getTahunAkademik'],              
								semester_akademik: this.$store.getters['uiadmin/getSemesterAkademik'],              
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                                               
								this.daftar_matakuliah = data.matakuliah;     
								
								this.daftar_kelas=this.$store.getters['uiadmin/getDaftarKelas'];  
						})  
				}
		},  
		components: {
				AkademikLayout,
				ModuleHeader,          
		},
}
</script>