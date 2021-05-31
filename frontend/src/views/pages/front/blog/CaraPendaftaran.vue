<template>
	<FrontLayout>
		<v-container fluid>
			<v-row no-gutters>
				<v-col
					class="hidden-sm-and-down"
					sm="12"
					md="4"
					align="center">
					<v-card
						class="pa-0 rounded-0"
						elevation="0"
						color="white">
							<v-img
								max-width="100%"
								max-height="100%"
								:src="$api.url+'/storage/images/petunjuk_pendaftaran.png'">
							</v-img>
					</v-card>
				</v-col>

				<v-col
					sm="12"
					md="8">
					<v-card
						class="pa-0 rounded-0"
						elevation="0"
						color="white">
							
							<v-timeline dense>
								<v-slide-x-reverse-transition
									group
									hide-on-leave
								>
									<v-timeline-item
										key="item.id"
										color="indigo darken-3"
										small
										fill-dot
									>
										<v-alert
											type="success"
											color="indigo darken-3"
											icon="mdi-account-plus pt-2"
											class="white--text"
										>
											<h2 class="headline font-weight-bold">
												Membuat Akun
											</h2>
											<br>
											<p class="text-left">
												Klik menu PENDAFTARAN maka muncul halaman pendaftaran,
												yang terdiri dari beberapa bagian yang wajib untuk diisi,
												yaitu :
											</p>
											<ol type="1">
												<li>Nama Lengkap.</li>
												<li>Nomor HP / WA dengan format seperti contoh +6281234567.</li>
												<li>Email yang valid dan aktif.</li>
												<li>Program studi yang di pilih Sistem Informasi atau Teknik Informatika.</li>
												<li>Tentukan Username dan Password.</li>
												<li>Ceklis Recaptcha / I'm Not Robot.</li>
												<li>Konfirmasi kode aktivasi yang dikirim melalui Email pada saat registrasi.</li>
												<li>Pendaftaran selesai.</li>
											</ol>

										</v-alert>
									</v-timeline-item>

									<v-timeline-item
										key="item.id"
										color="green darken-3"
										small
										fill-dot
									>
										<v-alert
											type="success"
											color="green darken-3"
											icon="mdi-file-upload pt-2"
											class="white--text"
										>
											<h2 class="headline font-weight-bold">
												Lengkapi Formulir Pendaftaran
											</h2>
											<br>
											<p class="text-left">
												Ikuti langkah-langkah dibawah ini untuk melengkapi formulir pendaftaran :
											</p>
											<ol>
												<li>Silahkan anda klik menu login pada menu utama.</li>
												<li>Inputkan username dan password anda yang telah didaftarkan.</li>
												<li>
													Setelah login berhasil, upload persyaratan berikut :
													<ul>a. Scan Pas Foto.</ul>
													<ul>b. Scan Ijazah Terakhir atau Surat Keterangan Lulus.</ul>
													<ul>c. Bagi mahasiswa konversi atau pindahan, Scan Transkrip Nilai.</ul>
													<ul>d. Scan KTP.</ul>
													<ul>e. Scan Kartu Keluarga.</ul>
												</li>
											</ol>
											<v-divider class="mt-2 mb-2"></v-divider>
											<p class="font-weight-bold">Mengisi Formulir Pendaftaran</p>
											<ol>
												<li>Klik tombol isi Formulir.</li>	
												<li>Harap mengisi identitas diri sesuai data KTP dan Kartu Keluarga.</li>
												<li>Pada Menu Alamat diisi sesuai Alamat Domisili.</li>
												<li>Setelah semua data terisi dengan benar, klik tombol Simpan.</li>
												<li>Kemudian akan muncul Kode Biling.</li>
												<li>Kode Biling akan digunakan saat proses pembayaran.</li>
												<li>Setelah formulir terisi, langkah selanjutnya adalah mengupload persyaratan.</li>
											</ol>

										</v-alert>
									</v-timeline-item>

									<v-timeline-item
										key="item.id"
										color="amber darken-3"
										small
										fill-dot
									>
										<v-alert
											type="success"
											color="amber darken-3"
											icon="mdi-contactless-payment-circle pt-2"
											class="white--text"
										>
											<h2 class="headline font-weight-bold">
												Konfirmasi Pembayaran Pendaftaran
											</h2>
											<br>
											<p class="text-left">
												Klik menu KONFIRMASI PEMBAYARAN maka muncul halaman Konfirmasi Pembayaran.
											</p>
											<ol type="a">
												<li>Klik tombol konfirmasi.</li>
												<li>
													Pilih data pembayaran yang tampil pada Daftar Transaksi.
												</li>
												<li>Pilih Upload Pembayaran.</li>
											</ol>
											<br>
											<p class="text-left">
												Pembayaran selesai.
											</p>
										</v-alert>
									</v-timeline-item>

								</v-slide-x-reverse-transition>
							</v-timeline>

					</v-card>
				</v-col>
			</v-row>
			<v-container fluid class="pa-0 ma-0 mt-4 mb-0">
				<v-alert
					text
					color="primary"
					icon="mdi-phone pt-2"
					class="white--text"
					>
						<h3 class="headline font-weight-bold">
							Informasi dan Pendaftaran
						</h3>
						<br>
						<p class="text-left">
							Silahkan hubungi kami, bila ada pertanyaan atau hal yang belum jelas di nomor kontak WhatsApp berikut.
						</p>
						<h3 class="font-weight-black">
							Tim Marketing
						</h3>
						<v-icon color="blue darken-2">mdi-cellphone-message</v-icon>
						<strong> Hendi - 0878-3934-3009 | Vivi - 0812-1188-9515 | Iim - 0812-6164-4329</strong>
				</v-alert>
			</v-container>
		</v-container>

		
	</FrontLayout>
</template>
<script>
	import FrontLayout from "@/views/layouts/FrontLayout";
	export default {
		name: "CaraPendaftaran",
		data: () => ({}),
		components: {
			FrontLayout,
		},
	};
</script>