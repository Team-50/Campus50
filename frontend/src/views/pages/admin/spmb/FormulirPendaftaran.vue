<template>
    <SPMBLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-playlist-edit
            </template>
            <template v-slot:name>
                BIODATA
            </template>
            <template v-slot:subtitle v-if="dashboard!='mahasiswabaru'">
                TAHUN PENDAFTARAN {{tahun_pendaftaran}} - {{nama_prodi}}
            </template>
            <template v-slot:breadcrumbs>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0">
                    <template v-slot:divider>
                        <v-icon>mdi-chevron-right</v-icon>
                    </template>
                </v-breadcrumbs>
            </template>
            <template v-slot:desc v-if="dashboard=='mahasiswabaru'">
                <v-alert                                        
                    color="orange"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                        Halaman ini berisi pengisian formulir pendaftaran, mohon diisi dengan lengkap dan benar.
                </v-alert>
            </template>
            <template v-slot:desc v-else>
                <v-alert                                        
                    color="orange"
                    border="left"                    
                    colored-border
                    type="info"
                    >
                        Berisi kelengkapan biodata, silahkan melakukan filter tahun akademik dan program studi.
                </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid v-if="dashboard=='mahasiswabaru'">
            <FormMhsBaru/>
        </v-container>
        <v-container fluid v-else>
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
                        item-key="id"
                        sort-by="name"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">
                        <template v-slot:top>
                            <v-toolbar flat color="white">
                                <v-toolbar-title>DAFTAR MAHASISWA BARU</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-dialog v-model="dialogprofilmhsbaru" :fullscreen="true">         
                                    <ProfilMahasiswaBaru :item="datamhsbaru" v-on:closeProfilMahasiswaBaru="closeProfilMahasiswaBaru" />         
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.foto="{ item }">    
                            <v-badge
                                    bordered
                                    :color="badgeColor(item)"
                                    :icon="badgeIcon(item)"
                                    overlap
                                >                
                                    <v-avatar size="30">             
                                        <v-img :src="$api.storageURL+'/'+item.foto" />                                          
                                    </v-avatar>                                                                       
                            </v-badge>
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
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">
                                    <strong>ID:</strong>{{ item.id }}
                                    <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                                    <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
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
        <template v-slot:filtersidebar v-if="dashboard!='mahasiswabaru'">
            <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeProdi="changeProdi" ref="filter7" />	
        </template>
    </SPMBLayout>
</template>
<script>
import SPMBLayout from '@/views/layouts/SPMBLayout';
import ModuleHeader from '@/components/ModuleHeader';
import FormMhsBaru from '@/components/FormMahasiswaBaru';
import ProfilMahasiswaBaru from '@/components/ProfilMahasiswaBaru';
import Filter7 from '@/components/sidebar/FilterMode7';
export default {
		name: 'FormulirPendaftaran', 
		created()
		{
				this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];
				this.breadcrumbs = [
						{
								text:'HOME',
								disabled:false,
								href:'/dashboard/'+this.$store.getters['auth/AccessToken']
						},
						{
								text:'SPMB',
								disabled:false,
								href:'/spmb'
						},
						{
								text:'BIODATA',
								disabled:true,
								href:'#'
						}
				];
				this.breadcrumbs[1].disabled=(this.dashboard=='mahasiswabaru'||this.dashboard=='mahasiswa');
				
				let prodi_id=this.$store.getters['uiadmin/getProdiID'];
				this.prodi_id=prodi_id;
				this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](prodi_id);
				this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];    
				this.initialize()   
		},
		data: () => ({
				firstloading:true,
				prodi_id:null,
				tahun_pendaftaran:null,
				nama_prodi:null,

				dialogprofilmhsbaru:false,
				breadcrumbs: [],      
				dashboard:null,

				btnLoading: false,
				datatableLoading:false,
				expanded: [],
				datatable: [],
				headers: [                        
						{ text: '', value: 'foto', width:70 },             
						{ text: 'NAMA MAHASISWA', value: 'name',width:350,sortable:true },
						{ text: 'JK', value: 'jk',width:50 },
						{ text: 'NOMOR HP', value: 'nomor_hp',width:100},
						{ text: 'KELAS', value: 'nkelas',width:150,sortable:true },
						{ text: 'AKSI', value: 'actions', sortable: false,width:100 },
				],
				search: "",
				
				datamhsbaru: {}
		}),
		methods : {
				changeTahunPendaftaran (tahun)
				{
						this.tahun_pendaftaran=tahun;
				},
				changeProdi (id)
				{
						this.prodi_id=id;
				},
		initialize:async function()
		{	
						switch(this.dashboard)
						{
								case 'mahasiswabaru':

								break;
								default :
										this.datatableLoading=true;
										await this.$ajax.post('/spmb/formulirpendaftaran',
										{
												TA: this.tahun_pendaftaran,
												prodi_id: this.prodi_id,
										},
										{
												headers: {
														Authorization: this.$store.getters['auth/Token']
												}
										}).then(({ data }) => {               
												this.datatable = data.pmb;    
												this.datatableLoading=false;
										});         
										this.firstloading=false;
										this.$refs.filter7.setFirstTimeLoading(this.firstloading); 
						}
						
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
				badgeColor(item)
				{
						return item.active == 1 ? 'success':'error'
				},
				badgeIcon(item)
				{
						return item.active == 1 ? 'mdi-check-bold':'mdi-close-thick'
				},   
				viewItem(item)
				{
						this.datamhsbaru = item;
						this.dialogprofilmhsbaru = true;
				},
				editItem(item)
				{
						this.$router.push('/spmb/formulirpendaftaran/'+item.id+'/edit')
				},
				closeProfilMahasiswaBaru ()
				{
						this.dialogprofilmhsbaru = false;          
				}        
		},
		watch: {
				tahun_pendaftaran()
				{
						if (!this.firstloading)
						{
								this.initialize();
						}            
				},
				prodi_id(val)
				{
						if (!this.firstloading)
						{
								this.nama_prodi=this.$store.getters['uiadmin/getProdiName'](val);
								this.initialize();
						}            
				}
		},
		components: {
				SPMBLayout,
				ModuleHeader,      
				FormMhsBaru,
				ProfilMahasiswaBaru,
				Filter7    
		},
}
</script>