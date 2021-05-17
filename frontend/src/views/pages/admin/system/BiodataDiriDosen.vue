<template>
    <SystemUserLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-account
            </template>
            <template v-slot:name>
                BIODATA DIRI DOSEN
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
                        berisi informasi biodata diri dosen.
                </v-alert>
            </template>
        </ModuleHeader>   
        <v-container fluid>   
            <form-biodata-diri-dosen />
        </v-container>
    </SystemUserLayout>
</template>
<script>
import SystemUserLayout from '@/views/layouts/SystemUserLayout';
import ModuleHeader from '@/components/ModuleHeader';
import FormBiodataDiriDosen from '@/components/FormBiodataDiriDosen';
export default {
    name:'UsersProfile',
    created() {
        this.dashboard=this.$store.getters['uiadmin/getDefaultDashboard'];
        this.formdata=this.$store.getters['auth/User'];
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text:'SYSTEM',
                disabled:false,
                href:'#'
            },
            {
                text:'BIODATA DIRI DOSEN',
                disabled:true,
                href:'#'
            }
        ];
        if (this.dashboard=='mahasiswa')
        {
            this.fetchMahasiswa();
        }
    }, 
    data ()
    {
        return {
        }
    },
    methods: {
    },
    components: {
        SystemUserLayout,
        ModuleHeader,
        'form-biodata-diri-dosen':FormBiodataDiriDosen,
    },
}
</script>