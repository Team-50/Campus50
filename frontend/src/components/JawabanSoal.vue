<template>    
    <v-card
        class="clickable"
        @click.native="jawabSoal">
        <v-card-text>
            {{item.jawaban}}
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    name:'JawabanSoal',
    created ()
    {
             
    },
    props:{
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
        
    }),
    methods: {        
        jawabSoal:async function ()
        {
            await this.$ajax.post('/spmb/ujianonline/store',
            {
                user_id:this.$store.getters['auth/AttributeUser']('id'),
                soal_id:this.item.soal_id,
                jawaban_id:this.item.id
            },
            {
                headers:{
                    Authorization:this.$store.getters['auth/Token'],                                          
                }
            }
            ).then(()=>{                                                   
                this.$emit('selesaiJawab');
            }).catch(()=>{
                
            });
        }
    }
}
</script>