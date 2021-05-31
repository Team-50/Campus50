<template>
    <KeuanganLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-monitor-dashboard
            </template>
            <template v-slot:name>
                KEUANGAN 
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
                    color="orange"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                    Dashboard untuk memperoleh ringkasan informasi keuangan perguruan tinggi.
                    </v-alert>
            </template>
        </ModuleHeader>
        <template v-slot:filtersidebar>
            <Filter1 v-on:changeTahunAkademik="changeTahunAkademik" ref="filter1" />
        </template>
        <DashboardKeuanganMHS :ta="tahun_akademik" v-if="dashboard=='mahasiswa'"></DashboardKeuanganMHS>        
        <DashboardKeuanganAdmin :ta="tahun_akademik" v-else></DashboardKeuanganAdmin>        
    </KeuanganLayout>
</template>
<script>
import KeuanganLayout from '@/views/layouts/KeuanganLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter1 from '@/components/sidebar/FilterMode1';
import DashboardKeuanganMHS from '@/components/DashboardKeuanganMHS';
import DashboardKeuanganAdmin from '@/components/DashboardKeuanganAdmin';
export default {
    name: 'Keuangan',
    created ()
	{
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard']; 
		this.breadcrumbs = [
			{
				text:'HOME',
				disabled:false,
				href:'/dashboard/'+this.$store.getters['auth/AccessToken']
			},
			{
				text:'KEUANGAN',
				disabled:true,
				href:'#'
			}
        ];				
        this.tahun_akademik = this.$store.getters['uiadmin/getTahunAkademik'];         
    },
    mounted()
    {
        this.firstloading=false;
        this.$refs.filter1.setFirstTimeLoading(this.firstloading);
    },
    data: () => ({
        firstloading:true,
        breadcrumbs: [],      
        tahun_akademik:0,

        dashboard:null
    }),
    methods : {
        changeTahunAkademik (tahun)
        {
            this.tahun_akademik=tahun;
        },		
    },  
    components: {
        KeuanganLayout,
        ModuleHeader,         
        Filter1,    
        DashboardKeuanganMHS,
        DashboardKeuanganAdmin 
    },
}
</script>