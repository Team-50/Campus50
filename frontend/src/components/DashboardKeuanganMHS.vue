<template>
    <v-container fluid>            
        <v-row dense>
            <v-col xs="12" sm="4" md="3">
                <v-card                         
                    class="clickable green darken-1"
                    color="#385F73" 
                    @click.native="$router.push('/spmb/pendaftaranbaru')"
                    dark>
                    <v-card-title class="headline">
                        TOTAL TRANSAKSI
                    </v-card-title>    
                    <v-card-subtitle>
                        Total transaksi keseluruhan
                    </v-card-subtitle>
                    <v-card-text>
                        {{total_transaction|formatUang}}
                    </v-card-text>
                </v-card>
            </v-col>
            <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
            <v-col xs="12" sm="4" md="3">
                <v-card                         
                    class="clickable green darken-1"
                    color="#385F73" 
                    @click.native="$router.push('/spmb/pendaftaranbaru')"
                    dark>
                    <v-card-title class="headline">
                        TRANSAKSI PAID
                    </v-card-title>    
                    <v-card-subtitle>
                        Total transaksi dengan status PAID
                    </v-card-subtitle>
                    <v-card-text>
                        {{total_transaction_paid|formatUang}}
                    </v-card-text>
                </v-card>
            </v-col>
            <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
            <v-col xs="12" sm="4" md="3">
                <v-card                         
                    class="clickable green darken-1"
                    color="#385F73" 
                    @click.native="$router.push('/spmb/pendaftaranbaru')"
                    dark>
                    <v-card-title class="headline">
                        TRANSAKSI UNPAID
                    </v-card-title>    
                    <v-card-subtitle>
                        Total transaksi dengan status UNPAID
                    </v-card-subtitle>
                    <v-card-text>
                        {{total_transaction_unpaid|formatUang}}
                    </v-card-text>
                </v-card>
            </v-col>
            <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
            <v-col xs="12" sm="4" md="3">
                <v-card                         
                    class="clickable green darken-1"
                    color="#385F73" 
                    @click.native="$router.push('/spmb/pendaftaranbaru')"
                    dark>
                    <v-card-title class="headline">
                        TRANSAKSI CANCELLED
                    </v-card-title>    
                    <v-card-subtitle>
                        Total transaksi dengan status CANCELLED
                    </v-card-subtitle>
                    <v-card-text>
                        {{total_transaction_cancelled|formatUang}}
                    </v-card-text>
                </v-card>
            </v-col>
            <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
        </v-row>  
    </v-container>    
</template>
<script>
export default {
    name:'DashboardKeuanganMHS',
    created()
    {
        this.initialize();
    },
    data: () => ({
        datatableLoading:false,

        //daftar komponen biaya
        kombi_ganjil_unpaid: [],     
        kombi_genap_unpaid: [],     

        kombi_ganjil_paid: [],     
        kombi_genap_paid: [],     

        kombi_ganjil_cancelled: [],     
        kombi_genap_cancelled: [],     

        headers: [                        
            { text: 'NAMA KOMPONEN', value: 'nama_kombi', sortable:false},             
            { text: 'JUMLAH', align:'end',value: 'jumlah',width:250, sortable:false},              
        ], 
        //statistik
        total_transaction:0,
        total_transaction_paid:0,
        total_transaction_unpaid:0,
        total_transaction_cancelled:0
    }),
    props: {
        ta: {
            type:Number,
            required:true
        }
    },
    methods: {
        initialize:async function()
		{	
            this.datatableLoading=true;
            await this.$ajax.post('/dashboard/keuangan',
            {
                TA: this.ta,              
            },
            {
                headers: {
                    Authorization: this.$store.getters['auth/Token']
                }
            }).then(({ data }) => {                 
                this.total_transaction=data.total_transaction;
                this.total_transaction_paid=data.total_transaction_paid;          
                this.total_transaction_unpaid=data.total_transaction_unpaid;          
                this.total_transaction_cancelled=data.total_transaction_cancelled;          

                this.kombi_ganjil_unpaid=data.kombi_ganjil_unpaid;
                this.kombi_genap_unpaid=data.kombi_genap_unpaid;

                this.kombi_ganjil_paid=data.kombi_ganjil_paid;
                this.kombi_genap_paid=data.kombi_genap_paid;

                this.kombi_ganjil_cancelled=data.kombi_ganjil_cancelled;
                this.kombi_genap_cancelled=data.kombi_genap_cancelled;
                
                this.datatableLoading=false;
            }).catch(() => {
                this.datatableLoading=false;
            }); 

        }
    },
    computed: {        
        totalKombiGanjilPaid()
        {
            var total = 0;
            for (var i =0; i < this.kombi_ganjil_paid.length; i++)
            {
                var item = this.kombi_ganjil_paid[i];                    
                total=total+parseFloat(item.jumlah);
            }           
            return total;
        },
        totalKombiGenapPaid()
        {
            var total = 0;
            for (var i =0; i < this.kombi_genap_paid.length; i++)
            {
                var item = this.kombi_genap_paid[i];
                total=total+parseFloat(item.jumlah);
            }
            return total;
        }
    },  
    watch: {
        ta ()
        {
            this.initialize();
        }
    }
}
</script>