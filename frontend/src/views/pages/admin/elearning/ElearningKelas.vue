<template>
    <ElearningLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-monitor-dashboard
            </template>
            <template v-slot:name>
                E-LEARNING - KELAS 
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
                    dashboard untuk memperoleh daftar kelas yang diambil.
                    </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter2 v-on:changeTahunAkademik="changeTahunAkademik" v-on:changeSemesterAkademik="changeSemesterAkademik" ref="filter2" />	
        </template>
        <v-container fluid>            
            <v-row class="mb-4" no-gutters>
                <v-col
                cols="4"
                v-for="item in datatable" :key="item.id">
                    <v-card
                        class="mx-auto"
                        max-width="344"

                    >
                        <v-img
                        src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"
                        height="200px"
                        ></v-img>

                        <v-card-title>
                        {{item.nmatkul}}
                        </v-card-title>

                        <v-card-subtitle>
                        Hari : {{item.nama_hari}} <br> Jam : {{item.jam_masuk}} - {{item.jam_keluar}}
                        </v-card-subtitle>

                        <v-card-title>
                        {{item.nama_dosen}}
                        </v-card-title>

                        <v-card-actions>
                        <v-btn
                            color="orange lighten-2"
                            text
                        >
                            Explore
                        </v-btn>

                        <v-spacer></v-spacer>

                        <v-btn
                            icon
                            
                        >
                            <v-icon>mdi-chevron-down</v-icon>
                        </v-btn>
                        </v-card-actions>

                        <v-expand-transition>
                        <div>
                            <v-divider></v-divider>

                            <v-card-text>
                            I'm a thing. But, like most politicians, he promised more than he could deliver. You won't have time for sleeping, soldier, not with all the bed making you'll be doing. Then we'll go with that data file! Hey, you add a one and two zeros to that or we walk! You're going to do his laundry? I've got to find a way to escape.
                            </v-card-text>
                        </div>
                        </v-expand-transition>
                    </v-card>

                    
                </v-col>
            </v-row>
        </v-container>
    </ElearningLayout>
</template>
<script>
import ElearningLayout from '@/views/layouts/ElearningLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter2 from '@/components/sidebar/FilterMode2';
export default {
    name: 'ElearningKelas',
    created ()
	{
		this.breadcrumbs = [
			{
				text:'HOME',
				disabled:false,
				href:'/dashboard/'+this.$store.getters['auth/AccessToken']
			},
			{
				text:'E-LEARNING',
				disabled:true,
				href:'#'
            },
            {
				text:'KELAS',
				disabled:true,
				href:'#'
			}
        ];				
        this.tahun_akademik=this.$store.getters['uiadmin/getTahunAkademik'];    
        this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];        
    },
    mounted()
    {
        this.initialize();
    },
    data: () => ({
        datatableLoading:false,
        firstloading:true,
        breadcrumbs: [],      
        datatable: [],    
        tahun_akademik:null,
        semester_akademik:null,
        
    }),
    methods : {
        changeTahunAkademik (tahun)
        {
            this.tahun_akademik=tahun;
        },
        changeSemesterAkademik (semester)
        {
            this.semester_akademik=semester;
        },
		initialize:async function() 
        {
            this.datatableLoading=true;
            await this.$ajax.post('/akademik/perkuliahan/pembagiankelas',
            {
                ta: this.tahun_akademik,
                semester_akademik: this.semester_akademik,
            },
            {
                headers: {
                    Authorization: this.$store.getters['auth/Token']
                }
            }).then(({ data }) => {                               
                this.datatable = data.pembagiankelas;
                this.datatableLoading=false;
            }).catch(() => {
                this.datatableLoading=false;
            });  
            this.firstloading=false;
            this.$refs.filter2.setFirstTimeLoading(this.firstloading); 
        },
    },
    watch: {
        tahun_akademik()
        {
            if (!this.firstloading)
            {
                this.initialize();
            }            
        },
        semester_akademik()
        {
            if (!this.firstloading)
            {
                this.initialize();
            }            
        },
    },
    components: {
        ElearningLayout,
        ModuleHeader,         
        Filter2,      
    },
}
</script>