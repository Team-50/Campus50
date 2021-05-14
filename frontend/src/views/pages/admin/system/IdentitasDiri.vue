<template>
    <SystemConfigLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-identifier
            </template>
            <template v-slot:name>
                IDENTITAS DIRI
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
                    Mengatur halaman informasi dan bentuk perguruan tinggi. Perubahan berlaku pada Login selanjutnya.
                    </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid>  
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card>
                            <v-card-title>
                                PERGURUAN TINGGI
                            </v-card-title>
                            <v-card-text>
                                <v-text-field 
                                    v-model="formdata.nama_pt" 
                                    label="NAMA PERGURUAN TINGGI"
                                    outlined
                                    :rules="rule_nama_pt">
                                </v-text-field>                                                                    
                                <v-text-field 
                                    v-model="formdata.nama_alias_pt" 
                                    label="NAMA SINGKATAN PERGURUAN TINGGI"
                                    outlined
                                    :rules="rule_nama_singkatan_pt">
                                </v-text-field>
                                <v-radio-group v-model="formdata.bentuk_pt" row>
                                    BENTUK PERGURUAN TINGGI : 
                                    <v-radio label="SEKOLAH TINGGI" value="sekolahtinggi"></v-radio>
                                    <v-radio label="UNIVERSITAS" value="universitas"></v-radio>
                                </v-radio-group>
                                <v-text-field 
                                    v-model="formdata.kode_pt" 
                                    label="KODE PERGURUAN TINGGI (SESUAI FORLAP)"
                                    outlined
                                    :rules="rule_kode_pt">
                                </v-text-field>                                                                    
                            </v-card-text>
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
    </SystemConfigLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import SystemConfigLayout from '@/views/layouts/SystemConfigLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name: 'IdentitasDiri',
    created()
    {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.ACCESS_TOKEN
            },
            {
                text:'KONFIGURASI SISTEM',
                disabled:false,
                href:'/system-setting'
            },
            {
                text:'PERGURUAN TINGGI',
                disabled:false,
                href:'#'
            },
            {
                text:'IDENTITAS DIRI',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize();
    },
    data: () => ({
        breadcrumbs: [],      
        btnLoading: false, 
        //form
        form_valid:true, 
        formdata: {
            nama_pt: "",
            nama_alias_pt: "",
            bentuk_pt: "",
            kode_pt:0,
        },
        //form rules        
        rule_nama_pt: [
            value => !!value || "Mohon untuk di isi Nama Perguruan Tinggi !!!",           
        ], 
        rule_nama_singkatan_pt: [
            value => !!value || "Mohon untuk di isi Nama Alias Perguruan Tinggi !!!",           
        ],
        rule_kode_pt: [
            value => !!value || "Mohon untuk di isi Kode Perguruan Tinggi !!!", 
            value => /^[0-9]+$/.test(value) || 'Kode Perguruan Tinggi hanya boleh angka',
        ]
    }),
    methods: {
        initialize:async function() 
        {
            await this.$ajax.get('/system/setting/variables',
            {
                headers: {
                    Authorization: this.TOKEN
                }
            }).then(({ data }) => {  
                let setting = data.setting;               
                this.formdata.nama_pt=setting.NAMA_PT;
                this.formdata.nama_alias_pt=setting.NAMA_PT_ALIAS;
                this.formdata.bentuk_pt=setting.BENTUK_PT;
                this.formdata.kode_pt=setting.KODE_PT;
            });          
            
        },
        save() {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading = true;    
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method':'PUT', 
                        'pid':'Identitas Perguruan Tinggi',
                        setting:JSON.stringify({
                            101: this.formdata.nama_pt,
                            102: this.formdata.nama_alias_pt,
                            103: this.formdata.bentuk_pt,
                            104: this.formdata.kode_pt,
                        }),                                                                                                        
                    },
                    {
                        headers: {
                            Authorization: this.TOKEN
                        }
                    }
                ).then(() => {                       
                    this.btnLoading = false;
                }).catch(() => {
                    this.btnLoading = false;
                });        
            }
        }
    },
    computed: { 
        ...mapGetters('auth',{            
            ACCESS_TOKEN:'AccessToken',        
            TOKEN:'Token',              
        }),
    },
    components: {
		SystemConfigLayout,
        ModuleHeader,      
	}
}
</script>