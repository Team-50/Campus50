<template>
	<AdminLayout>
		<v-container fluid class="pa-0 mt-n16">
			<v-alert dense text color="primary" class="text-center">
				Hak akses sebagai :
				<strong>
					{{ $store.getters["auth/Role"] }}
				</strong>
				.
			</v-alert>
			<v-row no-gutters>
				<v-col class="hidden-sm-and-down" sm="12" md="4" align="left">
					<v-card class="pa-0 rounded-0" elevation="0" color="white">
						<v-img
							max-width="100%"
							max-height="100%"
							:src="$api.url + '/storage/images/petunjuk_pendaftaran.png'"
						>
						</v-img>
					</v-card>
				</v-col>
				<v-col sm="12" md="8">
					<v-card class="pa-0 rounded-0" elevation="0" color="white">
						<v-timeline dense>
							<v-slide-x-reverse-transition group hide-on-leave>
								<v-timeline-item
									key="transition[0]"
									color="green darken-3"
									small
									fill-dot
								>
									<v-alert
										type="success"
										color="green darken-3"
										icon="mdi-note-text pt-2"
										class="white--text"
									>
										<h2 class="headline font-weight-bold">
											Formulir Pendaftaran
										</h2>
										<br />
										<p class="text-left">
											Isi formulir pendaftaran dan lengkapi persyaratannya yaitu
											:
										</p>
										<ul>
											<li>Scan Pas Foto.</li>
											<li>Scan Ijazah Terakhir.</li>
											<li>Scan KTP.</li>
											<li>Scan Kartu Keluarga.</li>
										</ul>
										<v-btn
											color="white"
											dark
											class="ma-2 mx-2 mr-2 green--text"
											elevation="0"
											to="/spmb/formulirpendaftaran"
										>
											Isi Formulir
										</v-btn>
										<v-btn
											color="white"
											dark
											class="ma-2 mx-2 mr-2 green--text"
											elevation="0"
											to="/spmb/persyaratan"
										>
											Upload Persyaratan
										</v-btn>
									</v-alert>
								</v-timeline-item>
								<v-timeline-item
									key="transition[1]"
									color="yellow darken-3"
									small
									fill-dot
									v-if="status_ujian"
								>
									<v-alert
										type="success"
										color="yellow darken-3"
										icon="mdi-beaker-question pt-2"
										class="white--text"
									>
										<h2 class="headline font-weight-bold">
											Ujian Online
										</h2>
										<br />
										<ol>
											<li>No. Peserta : {{ peserta.no_peserta }}</li>
											<li>
												Tanggal Daftar :
												{{
													$date(peserta.created_at).format("DD/MM/YYYY HH:mm")
												}}
											</li>
											<li>
												Tanggal Ujian :
												{{
													$date(jadwal_ujian.tanggal_ujian).format("DD/MM/YYYY")
												}}
											</li>
											<li>
												Waktu Ujian :
												{{ jadwal_ujian.jam_mulai_ujian }} -
												{{ jadwal_ujian.jam_selesai_ujian }}
												({{ durasiUjian(jadwal_ujian) }})
											</li>
											<li>Keterangan : {{ keterangan_ujian }}</li>
										</ol>
										<v-btn
											color="white"
											dark
											class="ma-2 mx-2 mr-2 orange--text"
											elevation="0"
											@click.stop="mulaiUjian"
											:disabled="ismulai"
										>
											Mulai
										</v-btn>
									</v-alert>
								</v-timeline-item>
								<v-timeline-item
									key="transition[2]"
									color="yellow darken-3"
									small
									fill-dot
									v-else
								>
									<v-alert
										type="success"
										color="yellow darken-3"
										icon="mdi-beaker-question pt-2"
										class="white--text"
									>
										<h2 class="headline font-weight-bold">
											Ujian Online
										</h2>
										<br />
										<p class="text-left">
											Untuk mengikuti ujian online, silahkan pilih jadwal
											terlebih dahulu.
										</p>
										<v-btn
											color="white"
											dark
											class="mx-0 mr-2 orange--text"
											elevation="0"
											@click.stop="showPilihJadwal"
										>
											Pilih Jadwal Ujian
										</v-btn>
									</v-alert>
								</v-timeline-item>
								<v-timeline-item
									key="transition[3]"
									color="indigo darken-3"
									small
									fill-dot
								>
									<v-alert
										type="success"
										color="indigo darken-3"
										icon="mdi-beaker-question pt-2"
										class="white--text"
									>
										<h2 class="headline font-weight-bold">
											Konfirmasi Pembayaran
										</h2>
										<br />
										<p class="text-left">
											Konfirmasi pembayaran Biaya Pendaftaran.
										</p>
										<v-btn
											color="white"
											dark
											class="mx-0 mr-2 indigo--text"
											elevation="0"
											to="/keuangan/konfirmasipembayaran"
										>
											Konfirmasi
										</v-btn>
									</v-alert>
								</v-timeline-item>
								<v-timeline-item
									key="transition[4]"
									color="red darken-3"
									small
									fill-dot
									v-if="status_ujian"
								>
									<v-alert
										type="success"
										color="red darken-3"
										icon="mdi-email-newsletter pt-2"
										class="white--text"
									>
										<h2 class="headline font-weight-bold">
											Surat Keterangan Lulus
										</h2>
										<br />
										<p class="text-left">
											Silahkan download Surat Keterangan Kelulusan kemudian
											cetak dan dibawa ke kampus saat daftar ulang beserta
											persyaratan asli lainnya.
										</p>
										<v-btn
											color="white"
											dark
											class="mx-0 mr-2 red--text"
											elevation="0"
											:disabled="
												btnLoading ||
													(nilai.ket_lulus == 0 &&
														(nilai.kjur == null || nilai.kjur == 0))
											"
											@click.stop="printpdf"
										>
											Download
										</v-btn>
									</v-alert>
								</v-timeline-item>
								<v-timeline-item
									key="transition[5]"
									color="red darken-3"
									small
									fill-dot
									v-else
								>
									<v-alert
										type="success"
										color="red darken-3"
										icon="mdi-email-newsletter pt-2"
										class="white--text"
									>
										<h2 class="headline font-weight-bold">
											Surat Keterangan Lulus
										</h2>
										<br />
										<p class="text-left">
											Silahkan download Surat Keterangan Kelulusan kemudian
											cetak dan dibawa ke kampus saat daftar ulang beserta
											persyaratan asli lainnya.
										</p>
										<v-btn
											color="white"
											dark
											class="mx-0 mr-2 red--text"
											elevation="0"
											:disabled="true"
										>
											Download
										</v-btn>
									</v-alert>
								</v-timeline-item>
							</v-slide-x-reverse-transition>
						</v-timeline>
					</v-card>
				</v-col>

				<v-dialog v-model="dialogpilihjadwal" persistent>
					<v-card>
						<v-card-title>
							<span class="headline">Pilih Jadwal Ujian PMB</span>
						</v-card-title>
						<v-card-text>
							<v-data-table
								:headers="headers"
								:items="datatable"
								item-key="id"
								sort-by="name"
								class="elevation-1"
								:loading="datatableLoading"
								loading-text="Loading... Please wait"
							>
								<template v-slot:item.tanggal_ujian="{ item }">
									{{ $date(item.tanggal_ujian).format("DD/MM/YYYY") }}
								</template>
								<template v-slot:item.tanggal_akhir_daftar="{ item }">
									{{ $date(item.tanggal_akhir_daftar).format("DD/MM/YYYY") }}
								</template>
								<template v-slot:item.durasi_ujian="{ item }">
									{{ item.jam_mulai_ujian }} - {{ item.jam_selesai_ujian }}
									<br />
									({{ durasiUjian(item) }})
								</template>
								<template v-slot:item.actions="{ item }">
									<v-icon
										@click.stop="pilihJadwal(item)"
										:loading="btnLoading"
										:disabled="btnLoading"
									>
										mdi-select-drag
									</v-icon>
								</template>
							</v-data-table>
						</v-card-text>
						<v-card-actions>
							<v-spacer></v-spacer>
							<v-btn color="blue darken-1" text @click.stop="closedialogfrm">
								BATAL
							</v-btn>
						</v-card-actions>
					</v-card>
				</v-dialog>
			</v-row>			
			<dialog-printout
				pid="sklulus"
				title="SK kelulusan"
				ref="dialogprint"
			>
			</dialog-printout>
		</v-container>
		<v-container fluid class="pa-0 mt-4">
			<v-alert text color="primary" icon="mdi-phone pt-2" class="white--text">
				<h3 class="headline font-weight-bold">
					Informasi dan Pendaftaran
				</h3>
				<br />
				<p class="text-left">
					Silahkan hubungi kami, bila ada pertanyaan atau hal yang belum jelas di nomor kontak WhatsApp berikut.
				</p>
				<h3 class="font-weight-black">
					Tim Marketing
				</h3>
				<v-icon color="blue darken-2">mdi-cellphone-message</v-icon>
				<strong>
					Hendi - 0878-3934-3009 | Vivi - 0812-1188-9515 | Iim -
					0812-6164-4329
				</strong>
			</v-alert>
		</v-container>
	</AdminLayout>
</template>
<script>
	import DialogPrintoutSPMB from "@/components/DialogPrintoutSPMB";
	import AdminLayout from "@/views/layouts/AdminLayout";
	export default {
		name: "DashboardMahasiswaBaru",
		created() {
			this.initialize();
			this.$store.dispatch("uiadmin/deletePage", "ujianonline");
		},
		data: () => ({
			reverse_transition: [1, 2, 3, 4, 5, 6, 7],
			btnLoading: false,
			datatableLoading: false,
			datatable: [],
			headers: [
				{
					text: "NAMA UJIAN",
					value: "nama_kegiatan",
					sortable: true,
					width: 300,
				},
				{
					text: "TGL. UJIAN",
					value: "tanggal_ujian",
					sortable: true,
					width: 100,
				},
				{
					text: "TGL. AKHIR PENDAFTARAN",
					value: "tanggal_akhir_daftar",
					sortable: true,
					width: 100,
				},
				{
					text: "DURASI UJIAN",
					value: "durasi_ujian",
					sortable: true,
					width: 100,
				},
				{
					text: "RUANGAN",
					value: "namaruang",
					sortable: true,
					width: 100,
				},
				{ text: "AKSI", value: "actions", sortable: false, width: 100 },
			],
			dialogpilihjadwal: false,
			ismulai: true,

			status_ujian: false,
			jadwal_ujian: null,
			peserta: null,
			nilai: {},
			keterangan_ujian: "",
		}),
		methods: {
			initialize: async function() {
				await this.$ajax
					.get(
						"/spmb/ujianonline/peserta/" +
							this.$store.getters["auth/AttributeUser"]("id"),
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						if (data.status == 1) {							
							this.status_ujian = true;
							this.peserta = data.peserta;
							this.jadwal_ujian = data.jadwal_ujian;
							this.ismulai = this.jadwal_ujian.status_ujian == 0 ? true : false;
							if (this.peserta.isfinish == 1) {
								this.nilai = data.nilai;
								this.ismulai = true;
								this.keterangan_ujian = this.nilai.ket_lulus == 1
									? "SELESAI UJIAN HASIL LULUS"
									: "SELESAI UJIAN HASIL TIDAK LULUS";
							} else {
								this.keterangan_ujian = "BELUM UJIAN";
							}
						}
					});
			},
			showPilihJadwal: async function() {
				this.dialogpilihjadwal = true;
				let tahun_pendaftaran = this.$store.getters["auth/AttributeUser"]("ta");
				let semester_pendaftaran = this.$store.getters["auth/AttributeUser"](
					"idsmt"
				);

				this.datatableLoading = true;
				await this.$ajax
					.post(
						"/spmb/ujianonline/jadwal",
						{
							tahun_pendaftaran: tahun_pendaftaran,
							semester_pendaftaran: semester_pendaftaran,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.datatable = data.jadwal_ujian;
						this.datatableLoading = false;
					})
					.catch(() => {
						this.datatableLoading = false;
					});
			},
			pilihJadwal: async function(item) {
				this.btnLoading = true;
				await this.$ajax
					.post(
						"/spmb/ujianonline/daftar",
						{
							user_id: this.$store.getters["auth/AttributeUser"]("id"),
							jadwal_ujian_id: item.id,
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(() => {
						this.initialize();
						this.closedialogfrm();
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			durasiUjian(item) {
				let waktu_mulai = this.$date(
					item.tanggal_ujian + " " + item.jam_mulai_ujian
				);
				let waktu_selesai = this.$date(
					item.tanggal_ujian + " " + item.jam_selesai_ujian
				);
				return waktu_selesai.diff(waktu_mulai, "minute") + " menit";
			},
			mulaiUjian: async function() {
				this.btnLoading = false;
				await this.$ajax
					.post(
						"/spmb/ujianonline/mulaiujian",
						{
							_method: "put",
							user_id: this.$store.getters["auth/AttributeUser"]("id"),
						},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.btnLoading = false;
						this.$store.dispatch("uiadmin/addToPages", {
							name: "ujianonline",
							data_ujian: this.jadwal_ujian,
							data_peserta: data.peserta,
						});
						this.$router.push("/spmb/ujianonline");
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			async printpdf() {
				this.btnLoading = true;
				var user_id = this.$store.getters["auth/AttributeUser"]("id");
				await this.$ajax
					.post(
						"/spmb/skkelulusan/printtopdf1/" + user_id,
						{},
						{
							headers: {
								Authorization: this.$store.getters["auth/Token"],
							},
						}
					)
					.then(({ data }) => {
						this.$refs.dialogprint.open({
							message:
								"Silahkah download Formulir Pendaftaran dan SK Kelulusan",
							file: data.pdf_file,
							nama_file: "SK KELULUSAN",
						});
						this.btnLoading = false;
					})
					.catch(() => {
						this.btnLoading = false;
					});
			},
			closedialogfrm() {
				this.dialogpilihjadwal = false;
			},
		},
		components: {
			AdminLayout,
			"dialog-printout": DialogPrintoutSPMB,
		},
	};
</script>
