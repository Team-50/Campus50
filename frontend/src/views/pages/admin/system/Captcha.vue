<template>
    <SystemConfigLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-google-circles-group
            </template>
            <template v-slot:name>
                CAPTCHA
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
                    Setting pengaman dari spammer menggunakan captcha
                    </v-alert>
            </template>
        </ModuleHeader>
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card>
                            <v-card-title>
                                Google Recaptcha
                            </v-card-title>
                            <v-card-text>
                                <v-text-field
                                    v-model="formdata.siteKey"
                                    label="SITE KEY"
                                    outlined
                                    :rules="rule_site_key">
                                </v-text-field>
                                <v-text-field
                                    v-model="formdata.privateKey"
                                    label="PRIVATE KEY"
                                    outlined
                                    :rules="rule_private_key">
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
    name: 'Captcha',
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
                text:'SERVER - CAPTCHA',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize();
    },
    data: () => ({
        breadcrumbs:[],
        datatableLoading:false,
        btnLoading:false,
        //form
        form_valid:true,
        formdata: {
            siteKey:'',
            privateKey:''
        },
        //form rules
        rule_site_key:[
            value => !!value||"Mohon untuk di isi site key !!!",
        ],
        rule_private_key:[
            value => !!value||"Mohon untuk di isi private key !!!",
        ],
    }),
    methods: {
        initialize:async function ()
        {
            this.datatableLoading=true;
            await this.$ajax.get('/system/setting/variables',
            {
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data})=>{
                let setting = data.setting;
                this.formdata.siteKey=setting.CAPTCHA_SITE_KEY;
                this.formdata.privateKey=setting.CAPTCHA_PRIVATE_KEY;
            });

        },
        save () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method':'PUT',
                        'pid':'captcha google',
                        setting:JSON.stringify({
                            901:this.formdata.siteKey,
                            902:this.formdata.privateKey,
                        }),
                    },
                    {
                        headers:{
                            Authorization:this.TOKEN
                        }
                    }
                ).then(()=>{
                    this.btnLoading=false;
                }).catch(()=>{
                    this.btnLoading=false;
                });
            }
        }
    },
    computed:{
        ...mapGetters('auth',{
            ACCESS_TOKEN:'AccessToken',
            TOKEN:'Token',
        }),
    },
    components:{
		SystemConfigLayout,
        ModuleHeader,
	}
}
</script>
