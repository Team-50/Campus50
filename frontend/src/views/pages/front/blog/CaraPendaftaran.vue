<template>
	<FrontLayout>
		<v-container fluid>
			<v-row no-gutters>
				<v-col
					class="hidden-sm-and-down"
					sm="12"
					md="6"
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
					md="6">
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
													<ul>b. Scan Ijazah Terakhir.</ul>
													<ul>c. Scan KTP.</ul>
													<ul>d. Scan Kartu Keluarga.</ul>
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
												Konfirmasi Pembayaran
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
												Pembayaran Selesai.
											</p>
										</v-alert>
									</v-timeline-item>

								</v-slide-x-reverse-transition>
							</v-timeline>

					</v-card>
				</v-col>

			</v-row>
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