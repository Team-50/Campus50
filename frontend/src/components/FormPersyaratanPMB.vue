<template>
    <v-row>        
        <v-col xs="12" sm="6" md="4" v-for="(item,index) in daftar_persyaratan" v-bind:key="item.persyaratan_id">            
            <FileUpload :user_id="user_id" :item="item" :index="index" />
        </v-col>     
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>   
    </v-row>
</template>
<script>
import FileUpload from '@/components/FileUploadPersyaratan';
export default {
    name:'FormPersyaratanPMB',
    created()
    {
        this.initialize();
    },
    props:{
        user_id: {
            type:String,
            required:true
        }
    },
    data:()=>({
        //form        
        daftar_persyaratan:[],        
    }),    
    methods: {
        initialize:async function ()
        {
            await this.$ajax.get('/spmb/pmbpersyaratan/'+this.user_id,             
                {
                    headers:{
                        Authorization:this.$store.getters['auth/Token']
                    }
                }
            ).then(({data})=>{                   
                this.daftar_persyaratan=data.persyaratan;
            })
        },                
    },
    components:{
        FileUpload
    }
}
</script>