<template>
		<SystemUserLayout>
				<ModuleHeader>
						<template v-slot:icon>
								mdi-account
						</template>
						<template v-slot:name>
								USERS DOSEN
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
										>User dengan role DOSEN bertanggungjawab terhadap proses pembelajaran mahasiswa.
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
												:items="daftar_users"
												:search="search"
												item-key="id"
												sort-by="name"
												show-expand
												:expanded.sync="expanded"
												:single-expand="true"
												@click:row="dataTableRowClicked"
												class="elevation-1"
												:loading="datatableLoading"
												loading-text="Loading... Please wait"
										>
												<template v-slot:top>
														<v-toolbar flat color="white">
																<v-toolbar-title>DAFTAR USERS DOSEN</v-toolbar-title>
																<v-divider
																		class="mx-4"
																		inset
																		vertical
																></v-divider>
																<v-spacer></v-spacer> 
																<v-btn color="warning"
																		:loading="btnLoading"
																		:disabled="btnLoading"
																		class="mr-2"
																		small
																		elevation="0"
																		@click.stop="syncPermission" 
																		v-if="$store.getters['auth/can']('USER_STOREPERMISSIONS')">
																		SYNC
																</v-btn>    
																<v-btn
																		color="indigo darken-3"
																		class="primary"
																		:loading="btnLoading"
																		:disabled="btnLoading"
																		small
																		elevation="0"
																		@click.stop="showDialogTambahUserDosen">
																		<v-icon size="21px">mdi-plus-circle</v-icon>
																</v-btn>
																<v-dialog v-model="dialog" max-width="500px" persistent>         
																		<v-form ref="frmdata" v-model="form_valid" lazy-validation>
																				<v-card>
																						<v-card-title>
																								<span class="headline">{{ formTitle }}</span>
																						</v-card-title>                 
																						<v-card-text>     
																								<v-text-field 
																										v-model="editedItem.name" 
																										label="NAMA DOSEN"
																										outlined
																										:rules="rule_user_name">
																								</v-text-field>                                                                    
																								<v-text-field 
																										v-model="editedItem.nidn" 
																										label="NIDN (NOMOR INDUK DOSEN NASIONAL)"
																										outlined>
																								</v-text-field>                                                                    
																								<v-text-field 
																										v-model="editedItem.nipy" 
																										label="NIPY (NOMOR INDUK PEGAWAI YAYASAN)"
																										outlined
																										:rules="rule_nipy">
																								</v-text-field>                                                                    
																								<v-text-field 
																										v-model="editedItem.email" 
																										label="EMAIL"
																										outlined
																										:rules="rule_user_email">
																								</v-text-field>                             
																								<v-text-field 
																										v-model="editedItem.nomor_hp" 
																										label="NOMOR HP"
																										outlined
																										:rules="rule_user_nomorhp">
																								</v-text-field>
																								<v-text-field 
																										v-model="editedItem.username" 
																										label="USERNAME"
																										outlined
																										:rules="rule_user_username">
																								</v-text-field>
																								<v-text-field 
																										v-model="editedItem.password" 
																										label="PASSWORD"
																										:type="'password'"
																										outlined
																										:rules="rule_user_password">
																								</v-text-field>   
																								<v-switch
																										v-model="editedItem.is_dw"
																										label="SEBAGAI DOSEN WALI">
																								</v-switch>                                      
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
																<v-dialog v-model="dialogEdit" max-width="500px" persistent>
																		<v-form ref="frmdata" v-model="form_valid" lazy-validation>
																				<v-card>
																						<v-card-title>
																								<span class="headline">{{ formTitle }}</span>
																						</v-card-title>                 
																						<v-card-text>                                                                     
																								<v-text-field 
																										v-model="editedItem.name" 
																										label="NAMA DOSEN"
																										outlined
																										:rules="rule_user_name">
																								</v-text-field>
																								<v-text-field 
																										v-model="editedItem.nidn" 
																										label="NIDN (NOMOR INDUK DOSEN NASIONAL)"
																										outlined>
																								</v-text-field>                                                                    
																								<v-text-field 
																										v-model="editedItem.nipy" 
																										label="NIPY (NOMOR INDUK PEGAWAI YAYASAN)"
																										outlined
																										:rules="rule_nipy">
																								</v-text-field>     
																								<v-text-field 
																										v-model="editedItem.email" 
																										label="EMAIL"
																										outlined
																										:rules="rule_user_email">
																								</v-text-field>
																								<v-text-field 
																										v-model="editedItem.nomor_hp" 
																										label="NOMOR HP"
																										outlined
																										:rules="rule_user_nomorhp">
																								</v-text-field>
																								<v-text-field 
																										v-model="editedItem.username" 
																										label="USERNAME"
																										outlined
																										:rules="rule_user_username">
																								</v-text-field>
																								<v-text-field 
																										v-model="editedItem.password" 
																										label="PASSWORD"
																										:type="'password'"
																										outlined
																										:rules="rule_user_passwordEdit">
																								</v-text-field> 
																								<v-switch
																										v-model="editedItem.is_dw"
																										label="SEBAGAI DOSEN WALI">
																								</v-switch>                       
																						</v-card-text>
																						<v-card-actions>
																								<v-spacer></v-spacer>
																								<v-btn color="blue darken-1" text @click.stop="close">BATAL</v-btn>
																								<v-btn 
																										color="blue darken-1" 
																										text 
																										@click.stop="save" 
																										:loading="btnLoading"
																										:disabled="!form_valid||btnLoading">SIMPAN</v-btn>
																						</v-card-actions>
																				</v-card>
																		</v-form>
																</v-dialog>   
																<v-dialog v-if="dialogUserPermission" v-model="dialogUserPermission" max-width="800px" persistent>
																		<UserPermissions :user="editedItem" v-on:closeUserPermissions="closeUserPermissions" role_default="dosen" />
																</v-dialog>  
														</v-toolbar>
												</template>
												<template v-slot:item.nidn="{ item }">
														{{(item.nidn && item.nidn.length > 0) > 0 ? item.nidn:'N.A'}}
												</template>
												<template v-slot:item.is_dw="{ item }">
														{{item.is_dw == false ? 'BUKAN':'YA'}}
												</template>
												<template v-slot:item.actions="{ item }">               
														<v-tooltip bottom v-if="item.default_role=='dosen'">             
																<template v-slot:activator="{ on, attrs }">                  
																		<v-btn 
																				v-bind="attrs"
																				v-on="on"
																				color="primary" 
																				icon                                         
																				x-small                                        
																				@click.stop="setPermission(item)"
																				:loading="btnLoading"
																				:disabled="btnLoading">
																				<v-icon>mdi-axis-arrow-lock</v-icon>
																		</v-btn>     
																</template>
																<span>Setting Hak Akses</span>        
														</v-tooltip>   
														<v-tooltip bottom v-if="item.default_role=='dosen'">             
																<template v-slot:activator="{ on, attrs }">                  
																		<v-btn 
																				v-bind="attrs"
																				v-on="on"
																				color="primary" 
																				icon                                         
																				x-small
																				class="ma-2" 
																				@click.stop="editItem(item)"
																				:loading="btnLoading"
																				:disabled="btnLoading">
																				<v-icon>mdi-pencil</v-icon>
																		</v-btn>     
																</template>
																<span>Ubah data user dosen</span>        
														</v-tooltip>   
														<v-tooltip bottom v-if="item.default_role=='dosen'">             
																<template v-slot:activator="{ on, attrs }">                  
																		<v-btn 
																				v-bind="attrs"
																				v-on="on"
																				color="warning" 
																				icon                                         
																				x-small                                        
																				@click.stop="deleteItem(item)"
																				:loading="btnLoading"
																				:disabled="btnLoading">
																				<v-icon>mdi-delete</v-icon>
																		</v-btn>     
																</template>
																<span>Hapus data user dosen</span>        
														</v-tooltip>  
												</template>
												<template v-slot:item.foto="{ item }"> 
														<v-avatar size="30">
																<v-img :src="$api.url+'/'+item.foto" />     
														</v-avatar>                                                                       
												</template>
												<template v-slot:expanded-item="{ headers, item }">
														<td :colspan="headers.length" class="text-center">
																<v-col cols="12">
																		<strong>ID:</strong>{{ item.id }}
																		<strong>Email:</strong>{{ item.email }}
																		<strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
																		<strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
																</v-col>     
														</td>
												</template>
												<template v-slot:no-data>
														Data belum tersedia
												</template>
										</v-data-table>
										<p class="text--secondary">DW : Dosen Wali</p>
								</v-col>
						</v-row>
				</v-container>
		</SystemUserLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import SystemUserLayout from '@/views/layouts/SystemUserLayout';
import ModuleHeader from '@/components/ModuleHeader';
import UserPermissions from '@/views/pages/admin/system/UserPermissions';
export default {
		name: 'UsersDosen',
		created() {
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
								text:'USERS DOSEN',
								disabled:true,
								href:'#'
						}
				];
				this.initialize()
		},
		data: () => ({         
				datatableLoading:false,
				btnLoading: false,    
				//tables
				headers: [                        
						{ text: '', value: 'foto' },
						{ text: 'USERNAME', value: 'username',sortable:true, width:150 },
						{ text: 'NAMA DOSEN', value: 'name',sortable:true, width:250 },
						{ text: 'NIDN', value: 'nidn',sortable:true },   
						{ text: 'NIPY', value: 'nipy',sortable:true },   
						{ text: 'NOMOR HP', value: 'nomor_hp',sortable:true },   
						{ text: 'DW', value: 'is_dw',sortable:true },   
						{ text: 'ROLE ASAL', value: 'default_role',sortable:true },   
						{ text: 'AKSI', value: 'actions', sortable: false,width:120 },
				],
				expanded: [],
				search: "",
				daftar_users: [],   
				
				//form
				form_valid:true,
				dialog: false,
				dialogEdit: false, 
				dialogUserPermission: false,   
				editedIndex: -1,      
				editedItem: {
						id:0,
						username: '',         
						password: '',         
						name: '',    
						nidn: "", 
						nipy: "",       
						email: '',         
						nomor_hp: "",               
						is_dw:false,    
						created_at: '',         
						updated_at: '', 
				},
				defaultItem: {
						id:0,
						username: '',         
						password: '',         
						name: '',  
						nidn: "",
						nipy: "",     
						email: '',         
						nomor_hp: '',        
						is_dw:false,  
						created_at: '',         
						updated_at: '',      
				},
				//form rules        
				rule_user_name: [
						value => !!value || "Mohon untuk di isi nama Dosen !!!",
						value => /^[A-Za-z\s]*$/.test(value) || 'Nama Dosen hanya boleh string dan spasi',              
				],       
				rule_nidn: [                         
						value => /^[0-9]+$/.test(value) || 'NIDN hanya boleh angka',              
				],       
				rule_nipy: [            
						value => /^[0-9]+$/.test(value) || 'Nomor Induk Pegawai Yayasan (NIPY) hanya boleh angka',              
				], 
				rule_user_email: [
						value => !!value || "Mohon untuk di isi email User !!!",
						value => /.+@.+\..+/.test(value) || 'Format E-mail harus benar',     
				], 
				rule_user_nomorhp: [
						value => !!value || "Nomor HP mohon untuk diisi !!!",
						value => /^\+[1-9]{1}[0-9]{1,14}$/.test(value) || 'Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388',
				], 
				rule_user_username: [
						value => !!value || "Mohon untuk di isi username User !!!",
						value => /^[A-Za-z_]*$/.test(value) || 'Username hanya boleh string dan underscore',
				], 
				rule_user_password: [
						value => !!value || "Mohon untuk di isi password User !!!",
						value => {
								if (value && typeof value !== 'undefined' && value.length > 0){
										return value.length >= 8 || 'Minimial Password 8 karaketer';
								}
								else
								{
										return true;
								}
						}
				], 
				rule_user_passwordEdit: [
						value => {
								if (value && typeof value !== 'undefined' && value.length > 0){
										return value.length >= 8 || 'Minimial Password 8 karaketer';
								}
								else
								{
										return true;
								}
						}
				],
		}),
		methods: {
				initialize:async function() 
				{
						this.datatableLoading=true;
						await this.$ajax.get('/system/usersdosen',{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {               
								this.daftar_users = data.users;    
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
				showDialogTambahUserDosen:async function()
				{
						this.dialog = true;
				},
				editItem:async function (item) {
						this.editedIndex = this.daftar_users.indexOf(item)
						item.password='';
						this.editedItem = Object.assign({}, item);                  
						this.dialogEdit = true;
				},      
				close() {            
						this.btnLoading = false;
						this.dialog = false;
						this.dialogEdit = false;
						setTimeout(() => {
								this.$refs.frmdata.resetValidation(); 
								this.editedItem = Object.assign({}, this.defaultItem)
								this.editedIndex = -1                
								}, 300
						);
				},      
				closeUserPermissions() {
						this.btnLoading = false;
						this.dialogUserPermission = false;
				},
				save() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;
								if (this.editedIndex > -1) 
								{
										this.$ajax.post('/system/usersdosen/'+this.editedItem.id,
												{
														'_method':'PUT',
														name: this.editedItem.name,
														nidn: this.editedItem.nidn,
														nipy: this.editedItem.nipy,
														email: this.editedItem.email,
														nomor_hp: this.editedItem.nomor_hp,   
														username: this.editedItem.username,
														password: this.editedItem.password,  
														is_dw: this.editedItem.is_dw,            
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
										
								} else {
										this.$ajax.post('/system/usersdosen/store',
												{
														name: this.editedItem.name,
														nidn: this.editedItem.nidn,
														nipy: this.editedItem.nipy,
														email: this.editedItem.email,
														nomor_hp: this.editedItem.nomor_hp,   
														username: this.editedItem.username,
														password: this.editedItem.password,                    
														is_dw: this.editedItem.is_dw,                    
												},
												{
														headers: {
																Authorization: this.TOKEN
														}
												}
										).then(({ data }) => {   
												this.daftar_users.push(data.user);
												this.close();
										}).catch(() => {
												this.btnLoading = false;
										});
								}
						}
				},
				setPermission: async function (item) {
						this.editedItem=item;
						this.dialogUserPermission = true;
				},
				syncPermission:async function()
				{
						this.btnLoading = true;
						await this.$ajax.post('/system/users/syncallpermissions',
								{
										role_name:'dosen',
								},
								{
										headers: {
												Authorization: this.$store.getters['auth/Token']
										}
								}
						).then(() => {                   
								this.btnLoading = false;
						}).catch(() => {
								this.btnLoading = false;
						});     
				},
				deleteItem (item) {           
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus username '+item.username+' ?', { color: 'red' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoading = true;
										this.$ajax.post('/system/usersdosen/'+item.id,
												{
														'_method':'DELETE',
												},
												{
														headers: {
																Authorization: this.TOKEN
														}
												}
										).then(() => {   
												const index = this.daftar_users.indexOf(item);
												this.daftar_users.splice(index, 1);
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
						return this.editedIndex === -1 ? 'TAMBAH USER DOSEN' : 'EDIT USER DOSEN'
				},
				...mapGetters('auth',{            
						ACCESS_TOKEN:'AccessToken',        
						TOKEN:'Token',              
				}),
		},

		watch: {
				dialog (val) {
						val || this.close()
				},
				dialogEdit (val) {
						val || this.close()
				},      
		},  
		components: {
				SystemUserLayout,
				ModuleHeader, 
				UserPermissions       
		},
}
</script>