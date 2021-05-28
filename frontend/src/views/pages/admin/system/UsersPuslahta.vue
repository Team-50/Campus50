<template>
		<SystemUserLayout>
				<ModuleHeader>
						<template v-slot:icon>
								mdi-account
						</template>
						<template v-slot:name>
								USERS PUSLAHTA
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
										>User dengan role Puslahta bertanggungjawab terhadap proses akademik mahasiswa.
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
																<v-toolbar-title>DAFTAR USERS PUSLAHTA</v-toolbar-title>
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
																	:loading="btnLoading"
																	:disabled="btnLoading"
																	color="indigo darken-3"
																	class="primary"
																	small
																	elevation="0"
																	@click.stop="showDialogTambahUserPuslahta">
																	<v-icon size="21px">mdi-plus-circle</v-icon>
																</v-btn>
																<v-dialog v-model="dialog" max-width="500px" persistent>
																		<v-form ref="frmdata" v-model="form_valid" lazy-validation>
																				<v-card>
																						<v-card-title>
																								<span class="headline">{{ formTitle }}</span>
																						</v-card-title>
																						<v-card-subtitle>
																								Bila program studi, tidak dipilih artinya user ini dapat mengakses seluruh data program studi.
																						</v-card-subtitle>
																						<v-card-text>
																								<v-text-field
																										v-model="editedItem.name"
																										label="NAMA USER"
																										outlined
																										:rules="rule_user_name">
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
																								<v-autocomplete
																										:items="daftar_prodi"
																										v-model="editedItem.prodi_id"
																										label="PROGRAM STUDI"
																										item-text="text"
																										item-value="id"
																										multiple
																										small-chips
																										outlined>
																								</v-autocomplete>
																								<v-autocomplete
																										:items="daftar_roles"
																										v-model="editedItem.role_id"
																										label="ROLES"
																										multiple
																										small-chips
																										outlined>
																								</v-autocomplete>
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
																						<v-card-subtitle>
																								Bila program studi, tidak dipilih artinya user ini dapat mengakses seluruh data program studi
																						</v-card-subtitle>
																						<v-card-text>
																								<v-text-field
																										v-model="editedItem.name"
																										label="NAMA USER"
																										outlined
																										:rules="rule_user_name">
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
																								<v-autocomplete
																										:items="daftar_prodi"
																										v-model="editedItem.prodi_id"
																										label="PROGRAM STUDI"
																										item-text="text"
																										item-value="id"
																										multiple
																										small-chips
																										outlined>
																								</v-autocomplete>
																								<v-autocomplete
																										:items="daftar_roles"
																										v-model="editedItem.role_id"
																										label="ROLES"
																										multiple
																										small-chips
																										outlined>
																								</v-autocomplete>
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
																<v-dialog v-model="dialogUserPermission" max-width="800px" persistent>
																		<UserPermissions :user="editedItem" :daftarpermissions="daftar_permissions" :permissionsselected="permissions_selected" v-on:closeUserPermissions="closeUserPermissions" />
																</v-dialog>
														</v-toolbar>
												</template>
												<template v-slot:item.actions="{ item }">
														<v-icon
																small
																class="mr-2"
																:loading="btnLoading"
																:disabled="btnLoading"
																@click.stop="setPermission(item)"
														>
																mdi-axis-arrow-lock
														</v-icon>
														<v-icon
																small
																class="mr-2"
																:loading="btnLoading"
																:disabled="btnLoading"
																@click.stop="editItem(item)"
														>
																mdi-pencil
														</v-icon>
														<v-icon
																small
																:loading="btnLoading"
																:disabled="btnLoading"
																@click.stop="deleteItem(item)"
														>
																mdi-delete
														</v-icon>
												</template>
												<template v-slot:item.foto="{ item }">
														<v-avatar size="30">
																<v-img :src="$api.storageURL+'/'+item.foto" />
														</v-avatar>
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
		</SystemUserLayout>
</template>
<script>
import {mapGetters} from 'vuex';
import SystemUserLayout from '@/views/layouts/SystemUserLayout';
import ModuleHeader from '@/components/ModuleHeader';
import UserPermissions from '@/views/pages/admin/system/UserPermissions';
export default {
		name: 'UsersPuslahta',
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
								text:'USERS PUSLAHTA',
								disabled:true,
								href:'#'
						}
				];
				this.initialize()
		},

		data: () => ({
				role_id:0,
				datatableLoading:false,
				btnLoading: false,
				//tables
				headers: [
						{ text: '', value: 'foto' },
						{ text: 'USERNAME', value: 'username',sortable:true },
						{ text: 'NAME', value: 'name',sortable:true },
						{ text: 'EMAIL', value: 'email',sortable:true },
						{ text: 'NOMOR HP', value: 'nomor_hp',sortable:true },
						{ text: 'AKSI', value: 'actions', sortable: false,width:100 },
				],
				expanded: [],
				search: "",
				daftar_users: [],
				daftar_permissions: [],
				permissions_selected: [],

				//form
				form_valid:true,
				daftar_roles: [],
				dialog: false,
				dialogEdit: false,
				dialogUserPermission: false,
				editedIndex: -1,
				daftar_prodi: [],
				editedItem: {
						id:0,
						username: '',
						password: '',
						name: '',
						email: '',
						nomor_hp: "",
						prodi_id: [],
						role_id: ['puslahta'],
						created_at: '',
						updated_at: '',
				},
				defaultItem: {
						id:0,
						username: '',
						password: '',
						name: '',
						email: '',
						nomor_hp: '',
						prodi_id: [],
						role_id: ['puslahta'],
						created_at: '',
						updated_at: '',
				},
				//form rules
				rule_user_name: [
						value => !!value || "Mohon untuk di isi nama User !!!",
						value => /^[A-Za-z\s]*$/.test(value) || 'Nama User hanya boleh string dan spasi',
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
						await this.$ajax.get('/system/userspuslahta',{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {
								this.daftar_users = data.users;
								this.role_id=data.role.id;
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
				syncPermission:async function()
				{
						this.btnLoading = true;
						await this.$ajax.post('/system/users/syncallpermissions',
								{
										role_name:'puslahta',
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
				showDialogTambahUserPuslahta:async function()
				{
						await this.$ajax.get('/system/setting/roles',{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {
								let roles = data.roles;
								var daftar_roles=[];
								roles.forEach(element => {
										if (element.name=='puslahta')
										{
												daftar_roles.push({
														text:element.name,
														disabled:true,
												});
										}
										else if (element.name=='dosen'||element.name=='dosenwali')
										{
												daftar_roles.push({
														text:element.name,
														disabled:false,
												});
										}
								});
								this.daftar_roles=daftar_roles;
								this.daftar_prodi=this.$store.getters['uiadmin/getDaftarProdi'];
								this.dialog = true;
						});
				},
				editItem:async function (item) {
						this.editedIndex = this.daftar_users.indexOf(item)
						item.password='';
						this.editedItem = Object.assign({}, item);
						this.daftar_prodi=this.$store.getters['uiadmin/getDaftarProdi'];

						await this.$ajax.get('/system/users/'+item.id+'/prodi',
								{
										headers: {
												Authorization: this.TOKEN
										}
								}
						).then(({ data }) => {
								let daftar_prodi = data.daftar_prodi;
								var prodi=[];
								daftar_prodi.forEach(element => {
										prodi.push(element.id);
								});
								this.editedItem.prodi_id=prodi;
						});
						await this.$ajax.get('/system/setting/roles',{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {
								let roles = data.roles;
								var daftar_roles=[];
								roles.forEach(element => {
										if (element.name=='puslahta')
										{
												daftar_roles.push({
														text:element.name,
														disabled:true,
												});
										}
										else if (element.name=='dosen'||element.name=='dosenwali')
										{
												daftar_roles.push({
														text:element.name,
														disabled:false,
												});
										}
								});
								this.daftar_roles=daftar_roles;
						});

						this.btnLoading = true;
						await this.$ajax.get('/system/users/'+item.id+'/roles',
						{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {
								this.editedItem.role_id=data.roles;
								this.btnLoading = false;
								this.dialogEdit = true;
						});
				},
				setPermission: async function (item) {
						this.btnLoading = true;
						this.$ajax.get('/system/setting/roles/'+this.role_id+'/permission',{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {
								this.daftar_permissions = data.permissions;
						}).catch(() => {
								this.btnLoading = false;
						});

						await this.$ajax.get('/system/users/'+item.id+'/permission',{
								headers: {
										Authorization: this.TOKEN
								}
						}).then(({ data }) => {
								this.permissions_selected = data.permissions;
								this.btnLoading = false;

						}).catch(() => {
								this.btnLoading = false;
						});
						this.dialogUserPermission = true;
						this.editedItem=item;

				},
				close() {
						this.btnLoading = false;
						this.dialog = false;
						this.dialogEdit = false;
						setTimeout(() => {
								this.editedItem = Object.assign({}, this.defaultItem);
								this.$refs.frmdata.resetValidation();     
								this.editedIndex = -1                
								}, 300
						);
				},
				closeUserPermissions() {
						this.btnLoading = false;
						this.permissions_selected=[];
						this.dialogUserPermission = false;
				},
				save() {
						if (this.$refs.frmdata.validate())
						{
								this.btnLoading = true;
								if (this.editedIndex > -1)
								{
										this.$ajax.post('/system/userspuslahta/'+this.editedItem.id,
												{
														'_method':'PUT',
														name: this.editedItem.name,
														email: this.editedItem.email,
														nomor_hp: this.editedItem.nomor_hp,
														username: this.editedItem.username,
														password: this.editedItem.password,
														prodi_id:JSON.stringify(Object.assign({},this.editedItem.prodi_id)),
														role_id:JSON.stringify(Object.assign({},this.editedItem.role_id)),
												},
												{
														headers: {
																Authorization: this.TOKEN
														}
												}
										).then(({ data }) => {
												Object.assign(this.daftar_users[this.editedIndex], data.user);
												this.close();
										}).catch(() => {
												this.btnLoading = false;
										});

								} else {
										this.$ajax.post('/system/userspuslahta/store',
												{
														name: this.editedItem.name,
														email: this.editedItem.email,
														nomor_hp: this.editedItem.nomor_hp,
														username: this.editedItem.username,
														password: this.editedItem.password,
														prodi_id:JSON.stringify(Object.assign({},this.editedItem.prodi_id)),
														role_id:JSON.stringify(Object.assign({},this.editedItem.role_id)),
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
				deleteItem (item) {
						this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus username '+item.username+' ?', { color: 'red' }).then((confirm) => {
								if (confirm)
								{
										this.btnLoading = true;
										this.$ajax.post('/system/userspuslahta/'+item.id,
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
						return this.editedIndex === -1 ? 'TAMBAH USER PUSLAHTA' : 'EDIT USER PUSLAHTA'
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
