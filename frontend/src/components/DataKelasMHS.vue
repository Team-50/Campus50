<template>
    <v-card v-if="datakelas">
        <v-card-title>
            <span class="headline">DATA KELAS</span>
        </v-card-title>
        <v-card-text>
            <v-row no-gutters>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>ID KELAS:</v-card-title>
                        <v-card-subtitle>
                            {{datakelas.id}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>SKS :</v-card-title>
                        <v-card-subtitle>
                            {{datakelas.sks}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>KODE MATAKULIAH:</v-card-title>
                        <v-card-subtitle>
                            {{datakelas.kmatkul}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>HARI/WAKTU:</v-card-title>
                        <v-card-subtitle>
                            {{datakelas.nama_hari}} {{datakelas.jam_masuk}}-{{datakelas.jam_keluar}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>NAMA KELAS/MATAKULIAH:</v-card-title>
                        <v-card-subtitle>
                            {{datakelas.nmatkul}} - {{$store.getters['uiadmin/getNamaKelas'](datakelas.idkelas)}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>CREATED / UPDATED :</v-card-title>
                        <v-card-subtitle>
                            {{$date(datakelas.created_at).format('DD/MM/YYYY HH:mm')}} / {{$date(datakelas.updated_at).format('DD/MM/YYYY HH:mm')}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
            </v-row>            
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    name:'DataMatakuliahPenyelenggaraan',
    props: {
        datakelas: {
            default:null,
            type:Object,          
        }
    },  
}
</script>