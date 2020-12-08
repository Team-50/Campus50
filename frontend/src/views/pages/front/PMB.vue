<template>
    <FrontLayout>
        <v-container class="fill-height" fluid>
            <v-row align="center" justify="center" no-gutters>
                <v-col xs="12" sm="6" md="4">
                    <h1 class="text-center display-1 font-weight-black primary--text">
                        PENDAFTARAN MAHASISWA BARU
                    </h1>    
                    <h4 class="text-center title font-weight-black primary--text">
                        TAHUN AKADEMIK {{tahunPendaftaran|formatTA}}
                    </h4>
                    <v-form ref="frmpendaftaran" v-model="form_valid" lazy-validation>
                        <v-card outlined>
                            <v-card-text>                                
                                <v-text-field 
                                    v-model="formdata.name"
                                    label="NAMA LENGKAP" 
                                    :rules="rule_name"
                                    outlined 
                                    dense />                               
                                <v-text-field 
                                    v-model="formdata.nomor_hp"
                                    label="NOMOR HP (ex: +628123456789)" 
                                    :rules="rule_nomorhp"
                                    outlined 
                                    dense />                               
                                <v-text-field 
                                    v-model="formdata.email"
                                    label="EMAIL" 
                                    :rules="rule_email"
                                    outlined 
                                    dense /> 
                                <v-select
                                    v-model="kode_fakultas"
                                    label="FAKULTAS"
                                    outlined
                                    :rules="rule_fakultas"
                                    :items="daftar_fakultas"
                                    item-text="nama_fakultas"
                                    item-value="kode_fakultas"
                                    :loading="btnLoadingFakultas"
                                    v-if="$store.getters['uifront/getBentukPT']=='universitas'"
                                />
                                <v-select
                                    label="PROGRAM STUDI"
                                    v-model="prodi_id"
                                    :items="daftar_prodi"
                                    item-text="nama_prodi2"
                                    item-value="id"
                                    :rules="rule_prodi"
                                    outlined
                                />
                                <v-text-field 
                                    v-model="formdata.username"
                                    label="USERNAME" 
                                    :rules="rule_username"
                                    outlined 
                                    dense />   
                                <v-text-field 
                                    v-model="formdata.password"
                                    label="PASSWORD" 
                                    type="password"         
                                    :rules="rule_password"                
                                    outlined 
                                    dense />  
                                <v-alert color="error" class="mb-0" text v-if="formdata.captcha_response.length<=0">
                                    Mohon dicentang Google Captcha    
                                </v-alert>
                            </v-card-text>                            
                            <v-card-actions class="justify-center">
                                <vue-recaptcha 
                                    ref="recaptcha"
                                    :sitekey="sitekey" 
                                    @verify="onVerify"
                                    @expired="onExpired"
                                    :loadRecaptchaScript="true">
                                </vue-recaptcha>                                                                   
                            </v-card-actions>                            
                            <v-card-actions class="justify-center">                                
                                 <v-btn 
                                    color="primary" 
                                    @click="save" 
                                    :loading="btnLoading"
                                    :disabled="btnLoading"
                                    block>
                                        DAFTAR
                                </v-btn>	                                 
                            </v-card-actions>
                        </v-card>
                    </v-form>
                    <v-dialog v-model="dialogkonfirmasiemail" max-width="500px" persistent>                                    
                        <v-form ref="frmkonfirmasi" v-model="form_valid" lazy-validation>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">Konfirmasi Email</span>
                                </v-card-title>
                                <v-card-text>    
                                    <v-alert type="success">
                                        Proses pendaftaran berhasil, silahkan cek email Anda ({{formkonfirmasi.email}}) untuk mendapatkan kode aktivasi atau hubungi panitia PMB jika kode tidak masuk ke email.
                                    </v-alert>                                    
                                    <v-text-field 
                                        v-model="formkonfirmasi.code" 
                                        label="CODE"
                                        outlined
                                        :rules="rule_code">
                                    </v-text-field>                                            
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>  
                                    <v-btn color="blue darken-1" text @click.stop="closedialogfrm">KELUAR</v-btn>                                              
                                    <v-btn 
                                        color="blue darken-1" 
                                        text 
                                        @click.stop="konfirmasi" 
                                        :loading="btnLoading"
                                        :disabled="btnLoading">
                                            KONFIRMASI
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-form>
                    </v-dialog>   
                </v-col>
            </v-row>
        </v-container>
    </FrontLayout>
</template>
<script>
import { mapGetters } from 'vuex';
import VueRecaptcha from 'vue-recaptcha';
import FrontLayout from '@/views/layouts/FrontLayout';
export default {
    name: 'PMB',
    created()
    {
        this.initialize();
    },
    data: () => ({            
        btnLoading:false,
        btnLoadingFakultas:false,
        //form
        form_valid:true,                 
        dialogkonfirmasiemail:false,  
        daftar_fakultas:[],
        kode_fakultas:'',
        daftar_prodi:[],
        prodi_id:'',                  
        formdata: {
            name:'',
            email:'',            
            nomor_hp:'',
            username:'',
            password:'',
            captcha_response:''
        },     
        formdefault: {
            name:'',
            email:'',            
            nomor_hp:'',
            username:'',
            password:'',
            captcha_response:''       
        },    
        formkonfirmasi:{
            email:'',
            code:''
        },
        rule_name:[
            value => !!value||"Nama Mahasiswa mohon untuk diisi !!!",
            value => /^[A-Za-z\s\\,\\.]*$/.test(value) || 'Nama Mahasiswa hanya boleh string dan spasi',
        ], 
        rule_nomorhp:[
            value => !!value||"Nomor HP mohon untuk diisi !!!",
            value => /^\+[1-9]{1}[0-9]{1,14}$/.test(value) || 'Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388',
        ], 
        rule_email:[
            value => !!value||"Email mohon untuk diisi !!!",
            v => /.+@.+\..+/.test(v) || 'Format E-mail mohon di isi dengan benar',
        ],
        rule_fakultas:[
            value => !!value||"Fakultas mohon untuk dipilih !!!"
        ], 
        rule_prodi:[
            value => !!value||"Program studi mohon untuk dipilih !!!"
        ], 
        rule_username:[
            value => !!value||"Username mohon untuk diisi !!!"
        ], 
        rule_password:[
            value => !!value||"Password mohon untuk diisi !!!"
        ],        
        rule_code:[
            value => /^[0-9]+$/.test(value) || 'Code hanya boleh angka',
        ]
    }),
    methods: {
        initialize:async function ()
        {
            if (this.$store.getters['uifront/getBentukPT']=='universitas')
            {                
                await this.$ajax.get('/datamaster/fakultas').then(({data})=>{                    
                    this.daftar_fakultas=data.fakultas;
                });
            }
            else
            {
                await this.$ajax.get('/datamaster/programstudi').then(({data})=>{
                    this.daftar_prodi=data.prodi;
                });
            }                       
        }, 
        save: async function ()
        {
            if (this.$refs.frmpendaftaran.validate())
            {
                this.btnLoading=true;                
                await this.$ajax.post('/spmb/pmb/store',{                    
                    name:this.formdata.name,
                    email:this.formdata.email,                    
                    nomor_hp:this.formdata.nomor_hp,
                    username:this.formdata.username,                                      
                    prodi_id:this.prodi_id,
                    password:this.formdata.password,
                    captcha_response:this.formdata.captcha_response,
                }).then(({data})=>{
                    this.formkonfirmasi.email=data.email;
                    this.formkonfirmasi.code=data.code;
                    this.btnLoading=false;    
                    this.dialogkonfirmasiemail=true;  
                    
                    this.form_valid=true;                                                                                        
                    this.$refs.frmpendaftaran.reset(); 
                    this.formdata = Object.assign({}, this.formdefault)
                }).catch(() => {                                   
                    this.btnLoading=false;
                });     
            }
            this.resetRecaptcha();                        
        },
        konfirmasi: async function ()
        {
            if (this.$refs.frmkonfirmasi.validate())
            {
                this.btnLoading=true;                
                await this.$ajax.post('/spmb/pmb/konfirmasi',{                                        
                    email:this.formkonfirmasi.email,                    
                    code:this.formkonfirmasi.code,
                }).then(()=>{             
                    this.dialogkonfirmasiemail=false;       
                    this.btnLoading=false;                                                                                           
                }).catch(() => {                                   
                    this.btnLoading=false;
                });                       
                this.form_valid=true;                          
                this.$refs.frmkonfirmasi.reset(); 
                this.frmkonfirmasi = Object.assign({}, {email:'',code:''});
                this.$router.replace('/login');
            }                           
        },
        onVerify: function (response) {
            this.formdata.captcha_response=response;            
        },
        onExpired: function () {
            this.formdata.captcha_response='';
        },
        resetRecaptcha()
        {
            this.$refs.recaptcha.reset();  
            this.formdata.captcha_response='';
        },
        closedialogfrm () {
            this.dialogkonfirmasiemail = false;            
            setTimeout(() => {
                this.frmpendaftaran = Object.assign({}, this.formdefault);                                
                this.$refs.frmpendaftaran.reset(); 
                }, 300
            );
        },
    },
    computed :{
        ...mapGetters('uifront',{
            sitekey: 'getCaptchaKey',
            tahunPendaftaran: 'getTahunPendaftaran',
        })
    },
    watch:{
        kode_fakultas (val)
        {
            if (val != null && val != '')
            {
                this.btnLoadingFakultas=true;
                this.$ajax.get('/datamaster/fakultas/'+val+'/programstudi').then(({data})=>{                                
                    this.daftar_prodi=data.programstudi;
                    this.btnLoadingFakultas=false;
                });
            }            
        }
    },  
    components: {
        FrontLayout,
        VueRecaptcha
    },
    
    
}
</script>