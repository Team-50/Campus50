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
                TAHUN AKADEMIK {{tahun_akademik}}
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
                    dashboard untuk memperoleh daftar kelas yang diambil.
                    </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter1 v-on:changeTahunAkademik="changeTahunAkademik" ref="filter1" />
        </template>
        <v-container fluid>            
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-card
                        class="mx-auto"
                        max-width="344"
                    >
                        <v-img
                        src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"
                        height="200px"
                        ></v-img>

                        <v-card-title>
                        Top western road trips
                        </v-card-title>

                        <v-card-subtitle>
                        1,000 miles of wonder
                        </v-card-subtitle>

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
                            @click="show = !show"
                        >
                            <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                        </v-btn>
                        </v-card-actions>

                        <v-expand-transition>
                        <div v-show="show">
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
import Filter1 from '@/components/sidebar/FilterMode1';
export default {
    name: 'Elearning',
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
        this.tahun_akademik = this.$store.getters['uiadmin/getTahunAkademik'];         
    },
    mounted()
    {
        this.initialize();
    },
    data: () => ({
        datatableLoading:false,
        firstloading:true,
        breadcrumbs:[],        
        tahun_akademik:0,
        
    }),
    methods : {
        changeTahunAkademik (tahun)
        {
            this.tahun_akademik=tahun;
        },
		initialize:async function()
		{	
            this.datatableLoading=true;            
            
            this.firstloading=false;            
            this.$refs.filter1.setFirstTimeLoading(this.firstloading); 

        }
    },
    watch:{
        tahun_akademik()
        {
            if (!this.firstloading)
            {
                this.initialize();
            }            
        },
    },
    components:{
        ElearningLayout,
        ModuleHeader,           
        Filter1,        
    },
}
</script>