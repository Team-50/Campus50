<template>
    <DataMasterLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-calendar-outline
            </template>
            <template v-slot:name>
                TAHUN AKADEMIK
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
                    Halaman untuk mengelola tahun akademik pada perguruan tinggi.
                </v-alert>
            </template>
        </ModuleHeader>
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-text-field
                                v-model="search"
                                append-icon="mdi-database-search"
                                label="Search"
                                single-line
                                hide-details
                            ></v-text-field>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="datatable"
                        :search="search"
                        item-key="tahun"
                        sort-by="tahun"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">

                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR TAHUN AKADEMIK</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-dialog v-model="dialogfrm" max-width="600px" persistent>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="primary" icon outlined small class="ma-2" v-on="on">
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>                                        
                                    </template>
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }}</span>
                                            </v-card-title>
                                            <v-card-text>
                                                <v-text-field
                                                    v-model="formdata.tahun"
                                                    label="TAHUN"
                                                    outlined
                                                    :rules="rule_tahun">
                                                </v-text-field>
                                                <v-text-field
                                                    v-model="formdata.tahun_akademik"
                                                    label="TAHUN AKADEMIK"
                                                    outlined
                                                    :rules="rule_tahun_akademik">
                                                </v-text-field>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
                                                <v-btn
                                                    color="blue darken-1"
                                                    text
                                                    @click.stop="save"
                                                    :loading="btnLoading"
                                                    :disabled="!form_valid||btnLoading">
                                                        SIMPAN
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </v-dialog>
                                <v-dialog v-model="dialogdetailitem" max-width="600px" persistent>
                                    <v-card>
                                        <v-card-title>
                                            <span class="headline">DETAIL DATA</span>
                                        </v-card-title>
                                        <v-card-text>
                                            <v-row no-gutters>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>TAHUN:</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.tahun}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                                <v-col xs="12" sm="6" md="6">
                                                    <v-card flat>
                                                        <v-card-title>TAHUN AKADEMIK :</v-card-title>
                                                        <v-card-subtitle>
                                                            {{formdata.tahun_akademik}}
                                                        </v-card-subtitle>
                                                    </v-card>
                                                </v-col>
                                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                            </v-row>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">KELUAR</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="viewItem(item)">
                                mdi-eye
                            </v-icon>
                            <v-icon
                                small
                                class="mr-2"
                                @click.stop="editItem(item)">
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                small
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="deleteItem(item)">
                                mdi-delete
                            </v-icon>
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">
                                    <strong>ID:</strong>{{ item.tahun }}
                                </v-col>
                            </td>
                        </template>
                        <template v-slot:no-data>
                            Data belum tersedia
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-container>
    </DataMasterLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import DataMasterLayout from '@/views/layouts/DataMasterLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name:'TahunAkademik',
    created () {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.ACCESS_TOKEN
            },
            {
                text:'DATA MASTER',
                disabled:false,
                href:'#'
            },
            {
                text:'TAHUN AKADEMIK',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize()
    },
    data: () => ({
        btnLoading:false,
        datatableLoading:false,
        expanded:[],
        datatable:[],
        headers: [
            { text: 'TA', value: 'tahun' },
            { text: 'TAHUN AKADEMIK', value: 'tahun_akademik' },
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },
        ],
        search:'',

        //dialog
        dialogfrm:false,
        dialogdetailitem:false,

        //form data
        old_tahun:'',
        form_valid:true,
        formdata: {
            tahun:'',
            tahun_akademik:'',
        },
        formdefault: {
            tahun:'',
            tahun_akademik:'',
        },
        editedIndex: -1,

        //form rules
        rule_tahun:[
            value => !!value||"Tahun Akademik mohon untuk diisi Misalnya 2020 !!!",
            value => /^[0-9]+$/.test(value) || 'Tahun Akademik hanya boleh angka',
            value => (value && value.length == 4) || 'Kode kelas hanya boleh 4 karakter'
        ],
        rule_tahun_akademik:[
            value => !!value||"Mohon untuk di isi nama tahun akademik !!!",
        ],
    }),
    methods: {
        initialize:async function ()
        {
            this.datatableLoading=true;
            await this.$ajax.get('/datamaster/tahunakademik',{
                headers: {
                    Authorization:this.TOKEN
                }
            }).then(({data})=>{
                this.datatable = data.ta;
                this.datatableLoading=false;
            }).catch(()=>{
                this.datatableLoading=false;
            });
        },
        dataTableRowClicked(item)
        {
            if ( item === this.expanded[0])
            {
                this.expanded=[];
            }
            else
            {
                this.expanded=[item];
            }
        },
        viewItem (item) {
            this.formdata=item;
            this.dialogdetailitem=true;
        },
        editItem (item) {
            this.editedIndex = this.datatable.indexOf(item);
            this.formdata = Object.assign({}, item);
            this.old_tahun=item.tahun;
            this.dialogfrm = true
        },
        save:async function () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading=true;
                if (this.editedIndex > -1)
                {
                    await this.$ajax.post('/datamaster/tahunakademik/'+this.old_tahun,
                        {
                            '_method':'PUT',
                            tahun:this.formdata.tahun,
                            tahun_akademik:this.formdata.tahun_akademik,
                        },
                        {
                            headers:{
                                Authorization:this.TOKEN
                            }
                        }
                    ).then(({data})=>{
                        Object.assign(this.datatable[this.editedIndex], data.ta);
                        this.closedialogfrm();
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });

                } else {
                    await this.$ajax.post('/datamaster/tahunakademik/store',
                        {
                            tahun:this.formdata.tahun,
                            tahun_akademik:this.formdata.tahun_akademik,
                        },
                        {
                            headers:{
                                Authorization:this.TOKEN
                            }
                        }
                    ).then(({data})=>{
                        this.datatable.push(data.ta);
                        this.closedialogfrm();
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }
            }
        },
        deleteItem (item) {
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus data dengan ID '+item.tahun+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoading=true;
                    this.$ajax.post('/datamaster/tahunakademik/'+item.tahun,
                        {
                            '_method':'DELETE',
                        },
                        {
                            headers:{
                                Authorization:this.TOKEN
                            }
                        }
                    ).then(()=>{
                        const index = this.datatable.indexOf(item);
                        this.datatable.splice(index, 1);
                        this.btnLoading=false;
                    }).catch(()=>{
                        this.btnLoading=false;
                    });
                }
            });
        },
        closedialogdetailitem () {
            this.dialogdetailitem = false;
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault)
                this.editedIndex = -1
                }, 300
            );
        },
        closedialogfrm () {
            this.dialogfrm = false;
            setTimeout(() => {
                this.formdata = Object.assign({}, this.formdefault);
                this.$refs.frmdata.reset();
                this.editedIndex = -1
                }, 300
            );
        },
    },
    computed: {
        ...mapGetters('auth',{
            ACCESS_TOKEN:'AccessToken',
            TOKEN:'Token',
        }),
        formTitle () {
            return this.editedIndex === -1 ? 'TAMBAH DATA' : 'UBAH DATA'
        },
    },
    components:{
        DataMasterLayout,
        ModuleHeader,
    },

}
</script>
