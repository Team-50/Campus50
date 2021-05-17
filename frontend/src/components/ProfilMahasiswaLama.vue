<template>
    <v-card color="grey lighten-4">
        <v-toolbar elevation="2"> 
            <v-toolbar-title>PROFIL MAHASISWA</v-toolbar-title>
            <v-divider
                class="mx-4"
                inset
                vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-icon                
                @click.stop="exit()">
                mdi-close-thick
            </v-icon>
        </v-toolbar>
        <v-card-text v-if="datamhs.hasOwnProperty('user_id')">            
            <v-row>
                <v-col xs="12" sm="12" md="2">
                    <v-card flat class="mb-2">
                        <v-img :src="$api.storageURL+'/'+datamhs.foto" />
                        <v-card-title>NOMOR HP:</v-card-title>  
                        <v-card-subtitle>
                            {{datamhs.nomor_hp}}
                        </v-card-subtitle>
                        <v-divider class="mx-4"></v-divider>
                        <v-card-text>
                            <v-chip label outlined color="info">{{datamhs.n_status}}</v-chip>
                        </v-card-text>
                    </v-card>         
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                <v-col xs="12" sm="12" md="10">
                    <v-row>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>USER ID:</v-card-title>  
                                <v-card-subtitle>
                                    {{datamhs.user_id}}
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>PROGRAM STUDI / KELAS:</v-card-title>  
                                <v-card-subtitle>
                                    {{datamhs.nama_prodi}} / {{datamhs.nkelas}}
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    </v-row>
                    <v-row>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>NIM / NIRM / NO. FORMULIR:</v-card-title>  
                                <v-card-subtitle>
                                    {{datamhs.nim}} / {{datamhs.nirm}} / {{datamhs.no_formulir}}
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>TAHUN PENDAFTARAN:</v-card-title>  
                                <v-card-subtitle>
                                    {{datamhs.tahun}}
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    </v-row>
                    <v-row>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>NAMA MAHASISWA:</v-card-title>  
                                <v-card-subtitle>
                                    {{datamhs.nama_mhs}} [{{datamhs.jk}}]
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>CREATED:</v-card-title>  
                                <v-card-subtitle>
                                    {{$date(datamhs.created_at).format('DD/MM/YYYY HH:mm')}}
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    </v-row>
                    <v-row>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>EMAIL:</v-card-title>  
                                <v-card-subtitle>
                                    {{datamhs.email}}
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                        <v-col xs="12" sm="12" md="6">
                            <v-card flat class="mb-2">
                                <v-card-title>UPDATED:</v-card-title>  
                                <v-card-subtitle>
                                    {{$date(datamhs.updated_at).format('DD/MM/YYYY HH:mm')}}
                                </v-card-subtitle>
                            </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
                    </v-row>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly"/>
            </v-row>
        </v-card-text>
    </v-card>
</template>
<script>    
export default {
    name:'ProfilMahasiswaLama',
    created()
    {
        this.initialize();         
    },
    props: {
        datamhs: {
            type:Object,          
            required:true
        },
        url: {
            type:String,
            default:null            
        }
    },
    
    methods: {
        initialize:async function()
        {
            console.log(this.datamhs);
        },
        exit()
        {
            if (this.url != null)
            {
                this.$router.push(this.url);
            }            
        }
    },  
}
</script>