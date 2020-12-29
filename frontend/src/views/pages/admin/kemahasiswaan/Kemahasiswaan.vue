<template>
    <KemahasiswaanLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-monitor-dashboard
            </template>
            <template v-slot:name>
                KEMAHASISWAAN 
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
                    dashboard untuk memperoleh ringkasan informasi kemahasiswaan perguruan tinggi.
                    </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter1 v-on:changeTahunAkademik="changeTahunAkademik" ref="filter1" />
        </template>
        <v-container fluid>            
            
        </v-container>
    </KemahasiswaanLayout>
</template>
<script>
import KemahasiswaanLayout from '@/views/layouts/KemahasiswaanLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter1 from '@/components/sidebar/FilterMode1';
export default {
    name: 'Kemahasiswaan',
    created ()
	{
		this.breadcrumbs = [
			{
				text:'HOME',
				disabled:false,
				href:'/dashboard/'+this.$store.getters['auth/AccessToken']
			},
			{
				text:'KEMAHASISWAAN',
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
        KemahasiswaanLayout,
        ModuleHeader,           
        Filter1,        
    },
}
</script>