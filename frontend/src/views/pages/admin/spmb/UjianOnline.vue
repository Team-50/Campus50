<template>
    <div>
        <v-system-bar app dark class="brown darken-2 white--text">
            <strong>No. Peserta :</strong> {{this.peserta.no_peserta}}
            <v-divider
                class="mx-4"
                inset
                vertical>
            </v-divider>
            <strong>Mulai Ujian :</strong> {{$date(this.peserta.mulai_ujian).format('DD/MM/YYYY HH:mm')}}
            <v-divider
                class="mx-4"
                inset
                vertical>
            </v-divider>
		</v-system-bar>	
        <v-main>
            <v-container fluid>
                <v-row>
                    <v-col cols="12">
                        <v-card 
                            class="grey lighten-5"                            
                            outlined>
                            <v-card-text>
                                <v-row
                                    justify="center"
                                    alignment="center">
                                    {{nama_soal}}
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col xs="12" sm="6" md="3" v-for="(item,index) in daftar_jawaban" v-bind:key="item.id">                                    
                        <JawabanSoal :index="index" :item="item" v-on:selesaiJawab="selesaiJawab" />
                    </v-col> 
                    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>   
                </v-row>
                <v-row v-if="isprosesujian">
                    <v-col cols="12">
                        <v-btn class="mr-2">
                            Review Kembali
                        </v-btn>
                        <v-btn 
                            @click.stop="selesaiUjian"
                            color="error">
                            Selesai
                        </v-btn>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </div>
</template>
<script>
import JawabanSoal from '@/components/JawabanSoal';
export default {
    name:'UjianOnline',
    created () 
    {           
        var page = this.$store.getters['uiadmin/Page']('ujianonline');
        this.jadwal_ujian=page.data_ujian;
        this.peserta=page.data_peserta;        
        this.initialize();
    },  
    data: () => ({
        jadwal_ujian:null,
        peserta:null,
        isprosesujian:false,

        nama_soal:'',
        daftar_jawaban:[]
    }),
    methods: {
        initialize:async function () 
        {
            await this.$ajax.get('/spmb/ujianonline/soal/'+this.peserta.user_id,           
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(({data})=>{                       
                if (data.status==0)
                {
                    this.isprosesujian=true;
                    this.nama_soal='UJIAN SELESAI ?';
                    this.daftar_jawaban=[];
                }
                else
                {
                    this.nama_soal = data.soal.soal;                 
                    this.daftar_jawaban = data.jawaban;
                }
            }).catch(()=>{
                
            }); 
        },
        selesaiJawab ()
        {
            this.initialize();
        },
        selesaiUjian:async function()
        {
            this.btnLoading=true;
            await this.$ajax.post('/spmb/ujianonline/selesaiujian',    
            {
                _method:'put',
                user_id:this.peserta.user_id
            },    
            {
                headers: {
                    Authorization:this.$store.getters['auth/Token']
                }
            }).then(()=>{                       
               this.btnLoading=false;
               this.$router.push('/dashboard/'+this.$store.getters['auth/AccessToken']);
            }).catch(()=>{
                this.btnLoading=false;
            }); 
        }
    },    
    components:{
        JawabanSoal,        
    },
}
</script>