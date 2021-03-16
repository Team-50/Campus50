<template>
	<FrontLayout>
		<v-container fluid>
			<v-row no-gutters>
				<v-col cols="12">
					<h2 class="title">ALUR PROSES PENDAFTARAN DAN SELEKSI</h2>
					<v-divider />
				</v-col>
				<v-col cols="12">
					<v-timeline
						align-top
						:dense="$vuetify.breakpoint.smAndDown"
					>
						<v-timeline-item small color="orange">
							<template v-slot:opposite>
								<span :class="`headline font-weight-bold orange--text`">
									TAHAP KE - 1
								</span>
							</template>
							<div class="py-4">
								<h2 :class="`headline font-weight-light mb-4`">
									MEMBUAT AKUN
								</h2>
								<div>
									Klik menu PENDAFTARAN maka muncul halaman pendaftaran, yang terdiri dari beberapa bagian yang wajib untuk diisi, yaitu :
									<ol type="a">
										<li>Nama Lengkap</li>
										<li>Nomor HP / WA dengan format seperti contoh +6281234567</li>
										<li>Email yang valid dan aktif</li>
										<li>Program studi yang di pilih Sistem Informasi atau Teknik Informatika</li>
										<li>Tentukan Username dan Password</li>
										<li>Ceklis Recaptcha / I'm Not Robot</li>                        
									</ol>
									Setelah tekan tombol "Daftar" maka akan muncul konfirmasi pendaftaran yang menyatakan proses pengisian form telah berhasil;
									Sistem akan mengirimkan kode aktivasi bila sudah tersedia kode tersebut tekan tombol "Konfirmasi".                                                                                                                                                       
								</div>
							</div>
						</v-timeline-item>
						<v-timeline-item small color="green">
							<template v-slot:opposite>
								<span
								:class="`headline font-weight-bold green--text`"
								>
									TAHAP KE - 2
								</span>
							</template>
							<div class="py-4">
								<h2 :class="`headline font-weight-light mb-4`">
								LENGKAPI FORMULIR PENDAFTARAN
								</h2>
								<div>
									Klik tombol Login di menu utama, Setelah berhasil login ke dalam SIMAK, klik tombol "Isi Formulir" yang terdiri dari beberapa bagian yang wajib untuk diisi, yaitu :
									<ol>
										<li>Identitas Diri</li>
										<li>Alamat Domisili</li>
										<li>Rencana Studi dan Menentukan Kelas (Pagi, Sore, Karyawan)</li>                        
									</ol>
									Setelah tekan tombol "Simpan" maka lanjutkan langkah 3.
									Selanjutnya lakukan pembayaran dengan cara ditransfer ke :
									<v-alert type="tkinfo">
										BRI (BANK RAKYAT INDONESIA) <br />
										NOMOR REKENING : 2147.01.000 129 307 <br />
										A.N : SEKOLAH TINGGI TEKNOLOGI INDONESIA TANJUNGPINANG <br />
									</v-alert>
								</div>
							</div>
						</v-timeline-item>
					</v-timeline>
				</v-col>
			</v-row>
			<v-row align="center" justify="center" no-gutters>
				<v-col xs="12" sm="6" md="4">
					<v-card outlined>
						<v-card-title class="justify-center">
							<h3 class="title">INFORMASI PENDAFTARAN</h3>
						</v-card-title>
						<v-card-subtitle class="justify-center">
							Silahkan hubungi kami, bila ada pertanyaan atau hal yang belum jelas di nomor kontak WA berikut.
						</v-card-subtitle>
						<v-card-text class="justify-center">
							<div>
								<h3 class="headline pink--text text--accent-2">TIM MARKETING</h3>
								<div><v-icon>mdi-card-account-phone-outline</v-icon> 08117002638</div>
							</div>							
						</v-card-text>
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
