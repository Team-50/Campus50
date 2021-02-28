<template>
    <v-form v-model="form_valid" ref="frmpersyaratan" lazy-validation>
        <v-card class="mx-auto" max-width="400">               
            <v-img class="white--text align-end" height="200px" :src="photoPersyaratan"></v-img>                            
            <v-card-text class="text--primary">
                <div>
                    <v-file-input 
                        accept="image/jpeg,image/png" 
                        :label="item.nama_persyaratan+' (.png atau .jpg)'"
                        :rules="rule_foto"
                        show-size
                        v-model="filepersyaratan[index]"
                        @change="previewImage"
                        v-if="verified == 0">
                    </v-file-input>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-badge
                    bordered
                    :color="badgeColor"
                    :icon="badgeIcon">   
                </v-badge>
                <v-spacer/>          
                <v-btn
                    icon
                    :href="this.$api.url+'/'+this.item.path"
                    target="_blank"                    
                    v-if="verified == 1">
                    <v-icon>
                        mdi-download
                    </v-icon>
                </v-btn>
                <v-btn
                    color="orange"
                    text
                    @click="upload(index,item)"
                    :loading="btnLoading"                                
                    :disabled="btnLoading||btnSimpan"
                    v-if="verified == 0">                                   
                    Simpan
                </v-btn>
                <v-btn
                    color="orange"
                    text
                    @click="hapusfilepersysaratan(item)"
                    :loading="btnLoading"                                
                    :disabled="btnLoading||btnHapus"
                    v-if="verified == 0">                   
                    Hapus
                </v-btn>
                <v-btn
                    color="orange"
                    text
                    @click="verifikasipersyaratan(item)"
                    :loading="btnLoading"                                
                    :disabled="btnLoading||btnVerifikasi" 
                    v-if="dashboard != 'mahasiswabaru' && dashboard != 'mahasiswa' && verified == 0">                
                    Verifikasi
                </v-btn>
            </v-card-actions>
        </v-card>            
    </v-form>        
</template>
<script>
export default {
    name:'FileUploadPersyaratan',
    created ()
    {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];   
        if (this.item.path == null || this.item.persyaratan_pmb_id==null)
        {            
            this.image_prev=this.item.path;            
        }
        else
        {            
            this.btnHapus=this.isVerified(this.item);
            this.image_prev=this.$api.url+'/'+this.item.path;
            this.badgeColor=this.item.verified;
            this.badgeIcon=this.item.verified;
        }        
    },
    props:{
        user_id:{
            type:String,
            required:true
        },
        index:{
            type:Number,
            required:true
        },
        item:{
            type:Object,
            required:true
        }
    },
    data:()=>({     
        dashboard:null,

        btnSimpan:true,  
        btnHapus:true,  
        btnVerifikasi:true,       
        btnLoading:false,
        image_prev:null,

        //form
        verified:0,
        form_valid:true,
        filepersyaratan:[],
        //form rules  
        rule_foto:[
            value => !!value||"Mohon pilih foto !!!",  
            value =>  !value || value.size < 2000000 || 'File foto harus kurang dari 2MB.'                
        ],
    }),
    methods: {        
        previewImage (e)
        {
            if (typeof e === 'undefined')
            {
                this.image_prev=null;
                this.btnSimpan=true;
            }
            else
            {
                let reader = new FileReader();
                reader.readAsDataURL(e);
                reader.onload = img => {                    
                    this.image_prev=img.target.result;
                }
                this.btnSimpan=false;
            }          
        },
        upload:async function (index,item)
        {
            let data = item;   
            if (this.$refs.frmpersyaratan.validate())
            {
                if (typeof this.filepersyaratan[index] !== 'undefined')
                {
                    this.btnLoading=true;
                    var formdata = new FormData();                    
                    formdata.append('nama_persyaratan',data.nama_persyaratan);
                    formdata.append('persyaratan_id',data.persyaratan_id);
                    formdata.append('persyaratan_pmb_id',data.persyaratan_pmb_id);
                    formdata.append('foto',this.filepersyaratan[index]);
                    await this.$ajax.post('/spmb/pmbpersyaratan/upload/'+this.user_id,formdata,                    
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token'],
                                'Content-Type': 'multipart/form-data'                      
                            }
                        }
                    ).then(()=>{                                                   
                        this.btnHapus=false;
                        this.btnSimpan=true;
                        this.btnLoading=false;                        
                    }).catch(()=>{
                        this.btnLoading=false;
                    });                    
                }               
            }            
        },
        hapusfilepersysaratan(item)
        {
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus persyaratan '+item.nama_persyaratan+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.$ajax.post('/spmb/pmbpersyaratan/hapusfilepersyaratan/'+item.persyaratan_pmb_id,
                        {
                            _method:'DELETE'
                        },                    
                        {
                            headers:{
                                Authorization:this.$store.getters['auth/Token']                
                            }
                        }
                    ).then(()=>{                   
                        this.btnHapus=true;
                        this.photoPersyaratan=require('@/assets/no-image.png');        
                        this.btnLoading=false;                        
                    }).catch(()=>{
                        this.btnLoading=false;
                    });  
                }
            });
        },
        isVerified(item)
        {
            if (item.path==null)
            {
                this.btnVerifikasi=true;
            }
            else if(item.verified==true)
            {
                this.btnVerifikasi=true;
            }
            else
            {
                this.btnVerifikasi=false;
            }
            return this.btnVerifikasi;
        },
        verifikasipersyaratan: async function (item)
        {
            this.btnLoading=true;                    
            await this.$ajax.post('/spmb/pmbpersyaratan/verifikasipersyaratan/'+item.persyaratan_pmb_id,
            {                    
                
            },
            {
                headers:{
                    Authorization:this.$store.getters['auth/Token']
                }
            }
            ).then(({data})=>{   
                this.badgeColor=data.persyaratan.verified;              
                this.badgeIcon=data.persyaratan.verified;              
                this.btnHapus=true;          
                this.btnVerifikasi=true;     
                this.btnLoading=false;                        
            }).catch(() => {                                                   
                this.btnLoading=false;
            });                             
        }
    },
    computed: {
        photoPersyaratan:{
            get ()
            {   
                if (this.image_prev==null)
                {
                    return require('@/assets/no-image.png');
                }
                else
                {
                    return this.image_prev;
                }
            },
            set (val)
            {
                this.image_prev=val;
            }
            
        },
        badgeColor:{
            get()
            {
                return this.verified == 1 ? 'success':'error'
            },
            set(val)
            {
                this.verified=val;
            }
            
        },
        badgeIcon:{
            get()
            {
                return this.verified == 1 ? 'mdi-check-bold':'mdi-close-thick';
            },
            set(val)
            {
                return this.verified=val;
            }
            
        },  
    }
}
</script>