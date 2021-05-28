<template>
    <SystemUserLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-account-key
            </template>
            <template v-slot:name>
                PERMISSIONS
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
                    Daftar aksi-aksi terhadap sebuah modul. Format penulisan permission, NAMAMODULE atau NAMA MODULE. Nama Permission tighly coupling dengan kode sumber.
                    </v-alert>
            </template>
        </ModuleHeader>
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col xs="12" sm="12" md="12">
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
                <v-col xs="12" sm="12" md="12">
                    <v-data-table
                        :headers="headers"
                        :items="daftar_permissions"
                        :search="search"
                        item-key="id"
                        sort-by="name"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait"
                        @click:row="dataTableRowClicked"
                    >
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR PERMISSION</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-dialog v-model="dialog" max-width="500px" persistent>
                                    <template v-slot:activator="{ on }">
                                        <v-btn color="indigo darken-3" small elevation="0" class="primary" v-on="on" :disabled="!CAN_ACCESS('PERMISSIONS_STORE')">
                                            <v-icon size="21px">mdi-plus-circle</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                                        <v-card>
                                            <v-card-title>
                                                <span class="headline">{{ formTitle }}</span>
                                            </v-card-title>
                                            <v-card-text>
                                                <v-container fluid>
                                                    <v-row>
                                                        <v-col cols="12" sm="12" md="12">
                                                            <v-text-field
                                                                v-model="editedItem.name"
                                                                label="NAMA PERMISSION"
                                                                :rules="rule_permission_name">
                                                            </v-text-field>
                                                        </v-col>
                                                    </v-row>
                                                </v-container>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="blue darken-1" text @click.stop="close">BATAL</v-btn>
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
                            </v-toolbar>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                small
                                @click.stop="deleteItem(item)"
                                :loading="btnLoading"
                                :disabled="!CAN_ACCESS('PERMISSIONS_DESTROY')||btnLoading"
                            >
                                mdi-delete
                            </v-icon>
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <strong>ID:</strong>{{ item.id }}
                                <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                                <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                            </td>
                        </template>
                        <template v-slot:no-data>
                            Data belum tersedia
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-container>
    </SystemUserLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import SystemUserLayout from '@/views/layouts/SystemUserLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name: 'Permissions',
    created()
    {
        this.breadcrumbs = [
            {
                text:'HOME',
                disabled:false,
                href:'/dashboard/'+this.ACCESS_TOKEN
            },
            {
                text:'USER SISTEM',
                disabled:false,
                href:'/system-users'
            },
            {
                text:'PERMISSIONS',
                disabled:true,
                href:'#'
            }
        ];
        this.initialize();
    },
    data: () => ({
        breadcrumbs: [],
        datatableLoading:false,
        btnLoading: false,
        expanded: [],
        daftar_permissions: [],
        //tables
        headers: [
            { text: 'NAMA PERMISSION', value: 'name' },
            { text: 'GUARD', value: 'guard_name' },
            { text: 'AKSI', value: 'actions', sortable: false,width:100 },
        ],
        search: "",
        //form
        form_valid:true,
        dialog: false,
        editedIndex: -1,
        editedItem: {
            id:0,
            name: '',
            guard: '',
            created_at: '',
            updated_at: '',
        },
        defaultItem: {
            id:0,
            name: '',
            guard: 'api',
            created_at: '',
            updated_at: '',
        },
        //form rules
        rule_permission_name: [
            value => !!value || "Mohon untuk di isi nama Permission !!!",
            value => /^[0-9\\a-zA-Z\\-]+$/.test(value) || 'Nama Permission hanya boleh angka,huruf,dan tanda -',              
        ],
    }),
    methods: {
        initialize ()
        {
            this.datatableLoading=true;
            this.$ajax.get('/system/setting/permissions',{
                headers: {
                    Authorization: this.TOKEN
                }
            }).then(({ data }) => {
                this.daftar_permissions = data.permissions;
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
        editItem (item) {
            this.editedIndex = this.daftar_permissions.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },
        close() {
            this.btnLoading = false;
            this.dialog = false;
            this.$refs.frmdata.reset();
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
                }, 300
            );
        },
        save() {
            if (this.$refs.frmdata.validate())
            {
                if (!(this.editedIndex > -1))
                {
                    this.btnLoading = true;
                    this.$ajax.post('/system/setting/permissions/store',
                        {
                            name: this.editedItem.name.toLowerCase()
                        },
                        {
                            headers: {
                                Authorization: this.TOKEN
                            }
                        }
                    ).then(() => {
                        this.initialize();
                        this.close();
                    }).catch(() => {
                        this.btnLoading = false;
                    });
                }
            }
        },
        deleteItem (item) {
            this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus permission '+item.name+' ?', { color: 'red' }).then((confirm) => {
                if (confirm)
                {
                    this.btnLoading = true;
                    this.$ajax.post('/system/setting/permissions/'+item.id,
                    {
                        '_method':'DELETE',
                    },
                    {
                        headers: {
                            Authorization: this.TOKEN
                        }
                    }
                    ).then(() => {
                        const index = this.daftar_permissions.indexOf(item);
                        this.daftar_permissions.splice(index, 1);
                        this.btnLoading = false;
                    }).catch(() => {
                        this.btnLoading = false;
                    });
                }
            });
        },
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'TAMBAH PERMISSION' : 'EDIT PERMISSION'
        },
        ...mapGetters('auth',{
            ACCESS_TOKEN:'AccessToken',
            TOKEN:'Token',
            CAN_ACCESS:'can',
            ATTRIBUTE_USER:'AttributeUser',
        }),
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
    },
    components: {
		SystemUserLayout,
		ModuleHeader,
	}
}
</script>
