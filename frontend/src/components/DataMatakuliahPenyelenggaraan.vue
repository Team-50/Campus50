<template>
    <v-card v-if="datamatkul">
        <v-card-title>
            <span class="headline">DATA PENYELENGGARAAN MATAKULIAH</span>
        </v-card-title>
        <v-card-text>
            <v-row no-gutters>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>ID PENYELENGGARAAN:</v-card-title>
                        <v-card-subtitle>
                            {{datamatkul.id}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>SKS :</v-card-title>
                        <v-card-subtitle>
                            {{datamatkul.sks}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>KODE MATAKULIAH:</v-card-title>
                        <v-card-subtitle>
                            {{datamatkul.kmatkul}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>TAHUN AKADEMIK/SEMESTER :</v-card-title>
                        <v-card-subtitle>
                            {{datamatkul.tahun}} {{$store.getters['uiadmin/getNamaSemester'](datamatkul.idsmt)}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>                
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>NAMA MATAKULIAH:</v-card-title>
                        <v-card-subtitle>
                            {{datamatkul.nmatkul}}
                        </v-card-subtitle>
                    </v-card>
                </v-col>
                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                <v-col xs="12" sm="6" md="6">
                    <v-card flat>
                        <v-card-title>CREATED / UPDATED :</v-card-title>
                        <v-card-subtitle>
                            {{$date(datamatkul.created_at).format('DD/MM/YYYY HH:mm')}} / {{$date(datamatkul.updated_at).format('DD/MM/YYYY HH:mm')}}
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
        datamatkul: {
            default:null,
            type:Object,          
        }
    },  
}
</script>