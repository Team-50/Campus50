<template>
	<SPMBLayout :showrightsidebar="false">
		<ModuleHeader>
			<template v-slot:icon>
				mdi-calendar-account
			</template>
			<template v-slot:name>
				JADWAL UJIAN PMB
			</template>
			<template v-slot:subtitle>
				TAHUN PENDAFTARAN {{ tahun_pendaftaran }} - SEMESTER
				{{ nama_semester_pendaftaran }}
			</template>
			<template v-slot:breadcrumbs>
				<v-breadcrumbs :items="breadcrumbs" class="pa-0">
					<template v-slot:divider>
						<v-icon>mdi-chevron-right</v-icon>
					</template>
				</v-breadcrumbs>
			</template>
			<template v-slot:desc>
				<v-alert color="cyan" border="left" colored-border type="info">
					Berisi daftar dan pengelolaan jadwal ujian PMB.
				</v-alert>
			</template>
		</ModuleHeader>
		<v-container fluid v-if="jadwal_ujian_id && data_jadwal">
			<v-row class="mb-4" no-gutters>
				<v-col cols="12">
					<v-card>
						<v-card-title>
							<span class="headline">DETAIL DATA JADWAL UJIAN</span>
						</v-card-title>
						<v-card-text>
							<v-row no-gutters>
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>ID :</v-card-title>
										<v-card-subtitle>
											{{ data_jadwal.id }}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>JUMLAH SOAL :</v-card-title>
										<v-card-subtitle>
											{{ data_jadwal.jumlah_soal }}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
							</v-row>
							<v-row no-gutters>
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>NAMA UJIAN ONLINE :</v-card-title>
										<v-card-subtitle>
											{{ data_jadwal.nama_kegiatan }}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>TANGGAL AKHIR DAFTAR :</v-card-title>
										<v-card-subtitle>
											{{
												$date(data_jadwal.tanggal_akhir_daftar).format(
													"DD/MM/YYYY"
												)
											}}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
							</v-row>
							<v-row no-gutters>
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>TANGGAL UJIAN :</v-card-title>
										<v-card-subtitle>
											{{
												$date(data_jadwal.tanggal_ujian).format("DD/MM/YYYY")
											}}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>WAKTU UJIAN PMB :</v-card-title>
										<v-card-subtitle>
											{{
												$date(data_jadwal.tanggal_ujian).format("DD/MM/YYYY")
											}}
											{{ data_jadwal.jam_mulai_ujian }} -
											{{ data_jadwal.jam_selesai_ujian }}
											({{ durasiUjian(data_jadwal) }} Menit)
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
							</v-row>
							<v-row no-gutters>
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>STATUS PENDAFTARAN :</v-card-title>
										<v-card-subtitle>
											{{
												data_jadwal.status_pendaftaran == 0 ? "BUKA" : "TUTUP"
											}}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>CREATED :</v-card-title>
										<v-card-subtitle>
											{{
												$date(data_jadwal.created_at).format("DD/MM/YYYY HH:mm")
											}}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
							</v-row>
							<v-row no-gutters>
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>STATUS UJIAN :</v-card-title>
										<v-card-subtitle>
											{{ StatusJadwanUjian }}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
								<v-col xs="12" sm="6" md="6">
									<v-card flat>
										<v-card-title>UPDATED :</v-card-title>
										<v-card-subtitle>
											{{
												$date(data_jadwal.updated_at).format("DD/MM/YYYY HH:mm")
											}}
										</v-card-subtitle>
									</v-card>
								</v-col>
								<v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
							</v-row>
						</v-card-text>
					</v-card>
				</v-col>
			</v-row>
			<v-row>
				<v-col cols="12">
					<v-bottom-navigation color="purple lighten-1">
						<v-btn
							@click.stop="mulaiUjian"
							:disabled="btnLoading"
							v-if="data_jadwal.status_ujian == 0"
						>
							<span>MULAI UJIAN</span>
							<v-icon>mdi-play</v-icon>
						</v-btn>
						<v-btn
							@click.stop="selesaiUjian"
							:disabled="btnLoading"
							v-else-if="data_jadwal.status_ujian == 1"
						>
							<span>SELESAI UJIAN</span>
							<v-icon>mdi-exit-run</v-icon>
						</v-btn>
						<v-btn @click.stop="closedetail">
							<span>Keluar</span>
							<v-icon>mdi-close</v-icon>
						</v-btn>
					</v-bottom-navigation>
				</v-col>
			</v-row>
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
						item-key="user_id"
						sort-by="nama_mhs"
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
								<v-toolbar-title>DAFTAR PESERTA UJIAN</v-toolbar-title>
								<v-divider class="mx-4" inset vertical></v-divider>
								<v-spacer></v-spacer>
							</v-toolbar>
						</template>
						<template v-slot:item.isfinish="{ item }">
							{{ getStatusUjianPeserta(item) }}
						</template>
						<template v-slot:item.actions="{ item }">
							<v-icon
								small
								:disabled="
									btnLoading || item.isfinish != 0 || item.mulai_ujian != null
								"
								@click.stop="deleteItem(item)"
								class="ma-2"
								title="Hapus Peserta"
							>
								mdi-delete
							</v-icon>
							<v-icon
								small
								:disabled="
									btnLoading || item.isfinish == 0 || item.ket_lulus == 1
								"
								@click.stop="recalculate(item)"
								class="ma-2"
								title="Hitung Ulang"
							>
								mdi-refresh-circle
							</v-icon>
							<v-icon
								small
								:disabled="
									btnLoading ||
										data_jadwal.status_ujian == 0 ||
										(item.isfinish == 1 &&
											item.mulai_ujian != null &&
											item.selesai_ujian != null)
								"
								@click.stop="selesaiUjianMhs(item)"
								title="Selesaikan Ujian"
							>
								mdi-alarm-note-off
							</v-icon>
						</template>
						<template v-slot:expanded-item="{ headers, item }">
							<td :colspan="headers.length" class="text-center">
								<v-col cols="12">
									<strong>ID:</strong>
									{{ item.user_id }}
									<strong>Username:</strong>
									{{ item.username }}
									<strong>created_at:</strong>
									{{ $date(item.created_at).format("DD/MM/YYYY HH:mm") }}
									<strong>updated_at:</strong>
									{{ $date(item.updated_at).format("DD/MM/YYYY HH:mm") }}
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
	</SPMBLayout>
</template>
<script>
	import SPMBLayout from "@/views/layouts/SPMBLayout";
	import ModuleHeader from "@/components/ModuleHeader";
	export default {
		name: "JadwalUjianPMBDetail",
		created() {
			this.jadwal_ujian_id = this.$route.params.jadwal_ujian_id;
			this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
			this.breadcrumbs = [
				{
					text: "HOME",
					disabled: false,
					href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
				},
				{
					text: "SPMB",
					disabled: false,
					href: "/spmb",
				},
				{
					text: "JADWAL UJIAN PMB",
					disabled: false,
					href: "/spmb/jadwalujianpmb",
				},
				{
					text: "DETAIL",
					disabled: true,
					href: "#",
				},
			];
			this.tahun_pendaftaran = this.$store.getters[
				"uiadmin/getTahunPendaftaran"
			];
			this.semester_pendaftaran = this.$store.getters[
				"uiadmin/getSemesterPendaftaran"
			];
			this.nama_semester_pendaftaran = this.$store.getters[
				"uiadmin/getNamaSemester"
			](this.semester_pendaftaran);
		},
		mounted() {
			this.initialize();
		},
		data() {
			return {
				jadwal_ujian_id: null,
				data_jadwal: null,
				status_jadwan_ujian: null,
				breadcrumbs: [],
				dashboard: null,
				tahun_pendaftaran: null,
				semester_pendaftaran: null,
				nama_semester_pendaftaran: null,
				btnLoading: false,
				datatableLoading: false,
				expanded: [],
				datatable: [],
				headers: [
					{ text: "NO", value: "no_peserta", sortable: true, width: 70 },
					{ text: "NAMA", value: "nama_mhs", sortable: true, width: 250 },
					{ text: "JK", value: "jk", sortable: true, width: 70 },
					{ text: "NOMOR HP", value: "telp_hp", sortable: true, width: 125 },
					{ text: "NILAI", value: "nilai", sortable: true, width: 100 },
					{ text: "KET.", value: "status", sortable: true, width: 90 },
					{ text: "STATUS", value: "isfinish", sortable: true, width: 100 },
					{ text: "AKSI", value: "actions", sortable: false, width: 100 },
				],
				search: "",
			};
		},
		methods: {
			initialize: async function() {
				this.datatableLoading = true;
				await this.$ajax
					.get("/spmb/jadwalujianpmb/" + this.jadwal_ujian_id, {
						headers: {
							Authorization: this.$store.getters["auth/Token"],
						},
					})
					.then(({ data }) => {
						this.datatable = data.peserta;
						this.data_jadwal = data.jadwal_ujian;
						this.StatusJadwanUjian = this.data_jadwal.status_ujian;
						this.datatableLoading = false;
					})
					.catch(() => {
						this.datatableLoading = false;
					});
			},
			getStatusUjianPeserta(item) {
				if (item.isfinish == 1) {
					return "SELESAI";
				} else if (item.mulai_ujian) {
					return "SEDANG UJIAN";
				} else {
					return "BELUM MULAI";
				}
			},
			dataTableRowClicked(item) {
				if (item === this.expanded[0]) {
					this.expanded = [];
				} else {
					this.expanded = [item];
				}
			},
			durasiUjian(item) {
				let waktu_mulai = this.$date(
					item.tanggal_ujian + " " + item.jam_mulai_ujian
				);
				let waktu_selesai = this.$date(
					item.tanggal_ujian + " " + item.jam_selesai_ujian
				);
				return waktu_selesai.diff(waktu_mulai, "minute");
			},
			mulaiUjian: async function() {
				this.btnLoading = true;
				await this.$ajax
					.post(
						"/spmb/jadwalujianpmb/updatestatusujian/" + this.jadwal_ujian_id,
						{
							_method: "PUT",
							status_ujian: 1,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(() => {
						this.btnLoading = false;
						this.$router.go();
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			selesaiUjianMhs(item) {
				this.$root.$confirm
					.open(
						"Selesai Ujian",
						"Apakah Anda ingin menyatakan ujian mahasiswa telah selesai ?",
						{ color: "red" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/spmb/ujianonline/selesaiujian",
									{
										_method: "put",
										user_id: item.user_id,
									},
									{
										headers: {
											Authorization: this.$store.getters["auth/Token"],
										},
									}
								)
								.then(() => {
									this.btnLoading = false;
									this.$router.go();
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
			selesaiUjian: async function() {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menyatakan ujian telah selesai ?",
						{ color: "red" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/spmb/jadwalujianpmb/updatestatusujian/" +
										this.jadwal_ujian_id,
									{
										_method: "PUT",
										status_ujian: 2,
									},
									{
										headers: {
											Authorization: this.$store.getters["auth/Token"],
										},
									}
								)
								.then(() => {
									this.btnLoading = false;
									this.$router.go();
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
			async recalculate(item) {
				this.$ajax
					.post(
						"/spmb/ujianonline/recalculate",
						{
							_method: "PUT",
							user_id: item.user_id,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(() => {
						this.btnLoading = false;
						this.$router.go();
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			closedetail() {
				this.jadwal_ujian_id = null;
				this.data_jadwal = null;
				this.$router.push("/spmb/jadwalujianpmb");
			},
			deleteItem(item) {
				this.$root.$confirm
					.open(
						"Delete",
						"Apakah Anda ingin menghapus peserta ujian denga user_id " +
							item.user_id +
							" ?",
						{ color: "red" }
					)
					.then(confirm => {
						if (confirm) {
							this.btnLoading = true;
							this.$ajax
								.post(
									"/spmb/ujianonline/keluar",
									{
										_method: "DELETE",
										user_id: item.user_id,
									},
									{
										headers: {
											Authorization: this.$store.getters["auth/Token"],
										},
									}
								)
								.then(() => {
									const index = this.datatable.indexOf(item);
									this.datatable.splice(index, 1);
									this.btnLoading = false;
								})
								.catch(() => {
									this.btnLoading = false;
								});
						}
					});
			},
		},
		computed: {
			StatusJadwanUjian: {
				set(newStatus) {
					this.status_jadwan_ujian = newStatus;
				},
				get() {
					switch (this.status_jadwan_ujian) {
						case 0:
							return "BELUM MULAI";
						case 1:
							return "BERJALAN";
						case 2:
							return "SELESAI";
						default:
							return "";
					}
				},
			},
		},
		components: {
			SPMBLayout,
			ModuleHeader,
		},
	};
</script>
