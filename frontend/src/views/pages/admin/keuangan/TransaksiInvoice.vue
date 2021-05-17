<template>
    <KeuanganLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-monitor-dashboard
            </template>
            <template v-slot:name>
                INVOICE
            </template>
            <template v-slot:subtitle>
                KODE BILLING {{data_transaksi.no_transaksi}}
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
                    Silahkan catat kode billing kemudian lakukan pembayaran, cantumkan kode billing tersebut sebagai berita.
                </v-alert>
            </template>
        </ModuleHeader>
    </KeuanganLayout>
</template>
<script>
import KeuanganLayout from '@/views/layouts/KeuanganLayout';
import ModuleHeader from '@/components/ModuleHeader';

export default {
    name: 'TransaksiInvoice',
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
                disabled:false,
                href:'/keuangan'
            },
            {
                text:'INVOICE',
                disabled:true,
                href:'#'
            }
        ];				             
    },
    data: () => ({
        dashboard:null,
        firstloading:true,
        breadcrumbs: [],   
        
        data_transaksi: {}
    }),
    components: {
        KeuanganLayout,
        ModuleHeader,                 
    },
}
</script>