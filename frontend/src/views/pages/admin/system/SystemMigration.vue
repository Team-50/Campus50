<template>
    <SystemMigrationLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-bank-transfer-in
            </template>
            <template v-slot:name>
                MIGRASI SISTEM 
            </template>
            <template v-slot:subtitle>
                TAHUN PENDAFTARAN {{tahun_pendaftaran}}
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
                    color="cyan"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                    Halaman ini digunakan untuk melakukan migrasi data mahasiswa.
                </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter9 v-on:changeTahunPendaftaran="changeTahunPendaftaran" ref="filter9" />
        </template>
        <v-container fluid>
            <v-row>
                <v-col cols="12">                    
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card class="mb-4">
                            <v-card-title>
                                BIODATA MAHASISWA
                            </v-card-title>
                            <v-card-text>
                                <v-alert                                    
                                    type="info">
                                    Isi form mahasiswa dengan lengkap dan benar; hasil inputan bisa dilihat di Akademik->Daftar Mahasiswa atau Akademik->Daftar Ulang
                                </v-alert>
                                <v-text-field 
                                    v-model="formdata.nim"
                                    label="NIM"   
                                    :rules="rule_nim"                                                               
                                    outlined /> 
                                <v-text-field 
                                    v-model="formdata.nirm"
                                    label="NIRM" 
                                    :rules="rule_nirm"                                                                    
                                    outlined /> 
                                <v-text-field
                                    label="NAMA LENGKAP"    
                                    v-model="formdata.nama_mhs"    
                                    :rules="rule_nama_mhs"
                                    outlined/>
                                 <v-select
                                    label="PROGRAM STUDI"
                                    v-model="formdata.prodi_id"
                                    :items="daftar_prodi"
                                    item-text="text"
                                    item-value="id"
                                    :rules="rule_prodi"
                                    outlined />
                                <v-select
                                    label="KELAS"
                                    v-model="formdata.idkelas"
                                    :items="daftar_kelas"
                                    item-text="text"
                                    item-value="id"
                                    :rules="rule_kelas"
                                    outlined
                                />          
                                <v-select
                                    label="DOSEN WALI"
                                    v-model="formdata.dosen_id"
                                    :items="daftar_dw"
                                    item-text="name"
                                    item-value="id"
                                    :rules="rule_dw"
                                    outlined/>                                                        
                            </v-card-text>                            
                        </v-card>         
                        <v-card class="mb-4">
                            <v-card-title>
                                Daftar Ulang Mahasiswa
                            </v-card-title>
                            <v-card-text>
                                <v-data-table 
                                    :loading="datatableLoading"
                                    loading-text="Loading... Please wait"                                                                                      
                                    :disable-pagination="true"
                                    :hide-default-footer="true"
                                    :headers="headers"
                                    item-key="id"
                                    :items="daftar_tasmt"
                                    dense> 
                                    <template v-slot:item.k_status="{ item }">                                                                    
                                        <v-select       
                                            v-model="formdata.status_mhs[daftar_tasmt.indexOf(item)]"                                                                                
                                            :items="daftar_status_mhs"
                                            item-text="text"
                                            item-value="id" />
                                    </template>                                        
                                    <template v-slot:no-data>                            
                                        belum ada data tahun akademik dan semester, silahkan ganti Tahun Pendaftaran ke yang lebih kecil dari 2020
                                    </template>                           
                                </v-data-table>
                            </v-card-text>                            
                        </v-card>
                        <v-card>
                            <v-card-actions>                       
                                <v-spacer></v-spacer>                        
                                <v-btn 
                                    color="blue darken-1" 
                                    text                                     
                                    @click.stop="save" 
                                    :loading="btnLoading"
                                    :disabled="!form_valid||btnLoading">SIMPAN</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-col>
            </v-row>
        </v-container>
    </SystemMigrationLayout>
</template>
<script>
import SystemMigrationLayout from '@/views/layouts/SystemMigrationLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter9 from '@/components/sidebar/FilterMode9';
export default {
    name: 'SystemMigration',
    created ()
	{
		this.breadcrumbs = [
			{
				text:'HOME',
				disabled:false,
				href:'/dashboard/'+this.$store.getters['auth/AccessToken']
			},
			{
				text:'MIGRASI SISTEM',
				disabled:true,
				href:'#'
			}
        ];				
        this.tahun_pendaftaran = this.$store.getters['uiadmin/getTahunPendaftaran']; 
    },
    mounted()
    {
        this.initialize();
    },
    data: () => ({        
        firstloading:true,
        breadcrumbs:[],        
        tahun_pendaftaran:0,  
        
        //form
        form_valid:true, 
        btnLoading:false,

        daftar_prodi:[],
        daftar_kelas:[],                
        daftar_dw:[],     

        daftar_tasmt:[],
        daftar_status_mhs:[],        
        formdata: {
            nim:'',
            nirm:'',
            nama_mhs:'',            
            dosen_id:'',           
            prodi_id:'',
            idkelas:'',
            status_mhs:[],
        },
        rule_nim:[
            value => !!value||"Nomor Induk Mahasiswa (NIM) mohon untuk diisi !!!",
            value => /^[0-9]+$/.test(value) || 'Nomor Induk Mahasiswa (NIM) hanya boleh angka',
        ], 
        rule_nirm:[
            value => !!value||"Nomor Induk Registrasi Masuk (NIRM) mohon untuk diisi !!!",
            value => /^[0-9]+$/.test(value) || 'Nomor Induk Registrasi Masuk (NIRM) hanya boleh angka',
        ], 
        rule_nama_mhs:[
            value => !!value||"Nama Mahasiswa mohon untuk diisi !!!",
            value => /^[A-Za-z\s\\,\\.]*$/.test(value) || 'Nama Mahasiswa hanya boleh string dan spasi',
        ],         
        rule_prodi:[
            value => !!value||"Program studi mohon untuk dipilih !!!"
        ], 
        rule_kelas:[
            value => !!value||"Kelas mohon untuk dipilih !!!"
        ],
        rule_dw:[
            value => !!value||"Mohon dipilih Dosen Wali untuk Mahasiswa ini !!!"
        ],         
        
        datatableLoading:false,
        headers: [                                                
            { text: 'TAHUN AKADEMIK', value: 'ta',sortable:false },
            { text: 'SEMESTER', value: 'semester',sortable:false },
            { text: 'STATUS', value: 'k_status',sortable:false, width:250 },                 
        ],
    }),
    methods : {
        changeTahunPendaftaran (tahun)
        {
            this.tahun_pendaftaran=tahun;
        },
		initialize:async function()
		{	
            this.daftar_prodi=this.$store.getters['uiadmin/getDaftarProdi'];  
            this.daftar_kelas=this.$store.getters['uiadmin/getDaftarKelas'];                      

            await this.$ajax.get('/akademik/dosenwali',{
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                              
                this.daftar_dw = data.users; 
            });

            this.datatableLoading=true;
            await this.$ajax.post('/system/migration',
            {
                TA:this.tahun_pendaftaran
            },
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                              
                this.daftar_tasmt = data.daftar_tasmt; 
                var dt = this.daftar_tasmt;
                var i=0;
                dt.forEach(() => {
                    this.formdata.status_mhs[i]='A';
                    i+=1;
                });
                this.datatableLoading=false;
            });
            this.daftar_status_mhs=this.$store.getters['uiadmin/getDaftarStatusMahasiswa'];  

            this.firstloading=false;            
            this.$refs.filter9.setFirstTimeLoading(this.firstloading); 
        },
        save () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                
                this.$ajax.post('/system/migration/store',
                    {
                        nim:this.formdata.nim,
                        nirm:this.formdata.nirm,
                        nama_mhs:this.formdata.nama_mhs,
                        dosen_id:this.formdata.dosen_id,
                        prodi_id:this.formdata.prodi_id,     
                        idkelas:this.formdata.idkelas,       
                        tahun_pendaftaran:this.tahun_pendaftaran,                 
                        status_mhs:JSON.stringify(Object.assign({},this.formdata.status_mhs)),                                                                                                          
                    },
                    {
                        headers:{
                            Authorization:this.$store.getters['auth/Token']
                        }
                    }
                ).then(({data})=>{    
                    console.log(data);                   
                    setTimeout(() => {
                        this.$router.go();    
                        this.btnLoading=false;
                        }, 300
                    );                                  
                }).catch(()=>{
                    this.btnLoading=false;
                });                                   
                 
            }
        },
    },
    watch:{
        tahun_pendaftaran()
        {
            if (!this.firstloading)
            {
                this.initialize();
            }            
        },
    },
    components:{
        SystemMigrationLayout,
        ModuleHeader,           
        Filter9,        
    },
}
</script>