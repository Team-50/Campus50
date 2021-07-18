<template>
		<AkademikLayout :showrightsidebar="false">
				<ModuleHeader>
						<template v-slot:icon>
								mdi-format-columns
						</template>
						<template v-slot:name>
								KARTU RENCANA STUDI
						</template>
						<template v-slot:subtitle  v-if="$store.getters['uiadmin/getDefaultDashboard']!='mahasiswa'">
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
										Halaman untuk melakukan tambah krs 
								</v-alert>
						</template>
				</ModuleHeader>        
				<v-container fluid>                         
						<v-row class="mb-4" no-gutters>
								<v-col cols="12">                    
										<v-form ref="frmdata" v-model="form_valid" lazy-validation>
												<v-card>
														<v-card-title>
																PILIH TAHUN & SEMESTER DAFTAR ULANG
														</v-card-title>
														<v-card-text>        
																<v-alert type="info">
																		Silahkan pilih tahun dan semester daftar ulang. Bila tidak ada disebabkan belum melakukan pembayaran atau status daftar ulang dinyatakan tidak aktif
																</v-alert>
																<v-text-field 
																		v-model="formdata.nim"
																		label="NIM"   
																		:rules="rule_nim"                                                                  
																		outlined
																		append-outer-icon="mdi-send"
																		@click:append-outer="cekNIM"
																		:disabled="(this.$store.getters['uiadmin/getDefaultDashboard']=='mahasiswa')"
																		/> 
																<v-select
																		v-model="formdata.dulang_id"
																		:items="daftar_dulang"                                    
																		label="DAFTAR ULANG"                                            
																		class="mr-2"
																		:rules="rule_dulang"
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
		name: 'PerkuliahanKRSTambah',
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
								text:'KRS',
								disabled:false,
								href:'/akademik/perkuliahan/krs/daftar'
						},
						{
								text:'TAMBAH',
								disabled:true,
								href:'#'
						},
				];
				let prodi_id=this.$store.getters['uiadmin/getProdiID'];
				this.prodi_id=prodi_id;
				this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);
				this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];          
				this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];    
				this.ta_matkul=this.tahun_akademik;
				this.daftar_semester=this.$store.getters['uiadmin/getDaftarSemester'];          
				this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];    
				if (this.$store.getters['uiadmin/getDefaultDashboard']=='mahasiswa')
				{
						this.formdata.nim=this.$store.getters['auth/AttributeUser']('username');
						this.fetchDulang();
				}
		},
		data: () => ({ 
				firstloading:true,
				prodi_id:null,
				nama_prodi:null,
				tahun_akademik:null,
				ta_matkul:null,
				semester_akademik:null,

				btnLoading: false,      

				//table
				dialogdetailitem:false,
				datatableLoading:false,
				expanded: [],
				datatable: [],    
				headers: [
						{ text: 'KODE', value: 'kmatkul', sortable:true,width:120  }, 
						{ text: 'NAMA MATAKULIAH', value: 'nmatkul',sortable:true },             
						{ text: 'KELOMPOK', value: 'group_alias', sortable:true,width:120 },             
						{ text: 'SKS', value: 'sks',sortable:true,width:80, align:'center' },             
						{ text: 'SMT', value: 'semester', sortable:true,width:80 },             
						{ text: 'AKSI', value: 'actions', sortable: false,width:100 },
				],
				search: "",  

				//formdata
				form_valid:true, 
				daftar_dulang: [],
				formdata: {
						nim: "",
						dulang_id: ""
				},      
				rule_nim: [
						value => !!value || "Nomor Induk Mahasiswa (NIM) mohon untuk diisi !!!",
						value => /^[0-9]+$/.test(value) || 'Nomor Induk Mahasiswa (NIM) hanya boleh angka',
				], 
				rule_dulang: [
						value => !!value || "Mohon dipilih Daftar Ulang yang telah dilakukan !!!"
				],       
		}),
		methods: {          
				async fetchDulang()
				{
						await this.$ajax.post('/akademik/dulang/dulangnotinkrs',
						{
								nim: this.formdata.nim,              
						},
						{
								headers: {
										Authorization: this.$store.getters['auth/Token']
								}
						}).then(({ data }) => {                               
								this.daftar_dulang=data.daftar_dulang;    
						})
				}, 
				cekNIM ()
				{
						if (this.formdata.nim.length > 0)
						{
								this.fetchDulang();
						}
				},
				save:async function() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;
								await this.$ajax.post('/akademik/perkuliahan/krs/store',
								{
										nim: this.formdata.nim,
										dulang_id: this.formdata.dulang_id,
								},
								{
										headers: {
												Authorization: this.$store.getters['auth/Token']
										}
								}).then(({ data }) => {               
										this.$router.push('/akademik/perkuliahan/krs/'+data.krs.id+'/detail');
										this.btnLoading = false;
								}).catch(() => {
										this.btnLoading = false;
								});      
						}
				},
				closedialogfrm() {                             
						setTimeout(() => {       
								this.formdata = Object.assign({}, this.formdefault);                    
								this.$router.push('/akademik/perkuliahan/krs/daftar');
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