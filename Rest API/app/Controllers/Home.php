<?php

namespace App\Controllers;

class Home extends BaseController
{

	public function __construct(){
		
	}

	public function download(){
		$blokirPesan = "Akses anda ditolak";
		if(!$this->request->getGet('q')) {
			return $blokirPesan;
		} else {
			// $dataFile = file_get;
			$namaFile = "{$this->appName} - {$this->appDeveloper}.zip";
			return $this->response->download('uploads/' . $namaFile, null);
		}
	}


	public function index()
	{
		$karakterRandom = 'abcdefghijklmnopqrstujvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$tokenDownload = '';
		$panjangToken = 35;
		for($i = 0; $i < $panjangToken ; $i++) {
			$tokenDownload .= $karakterRandom[rand(0, strlen($karakterRandom) - 1)];
		}
		$data = [
			'randomString' => $tokenDownload,
			'appName' => $this->appName,
			'downloadUrlClient' => base_url('download-client?q='.$tokenDownload.'&utm_source='.urlencode(base_url())),
			'appDeveloper' => $this->appDeveloper,
			'previews' => [
				['title' => 'Mividy - Loading', 'imageName' => 'loading.jpg', 'description' => 'Adalah halaman yang pertama kali ditampilkan ketika membuka aplikasi.'],
				['title' => 'Mividy - Login', 'imageName' => 'login.jpg', 'description' => 'Adalah portal pertama yang akan digunakan untuk autentikasi admin sebelum melakukan pengolahan data transaksi.'],
				['title' => 'Mividy - Reset Password [Step 1]', 'imageName' => 'reset-password-step1.jpg', 'description' => 'Adalah form untuk mengisikan email yang terdaftar sebelum melanjutkan proses reset password.'],
				['title' => 'Mividy - Reset Password [Step 2]', 'imageName' => 'reset-password-step2.jpg', 'description' => 'Adalah form untuk mengisi kode yang telah dikirimkan kepada email tersebut untuk melakukan verifikasi bahwa yang melakukan reset password adalah pemilik akun tersebut.'],
				['title' => 'Mividy - Reset Password [Step 3]', 'imageName' => 'reset-password-step3.jpg', 'description' => 'Adalah form terakhir dari fitur reset password, yaitu mengisikan password baru dan konfirmasi password baru digunakan untuk melakukan reset password. Jika password telah direset maka seluruh perangkat yang login menggunakan akun tersebut akan dikeluarkan secara otomatis dan diminta untuk login kembali.'],
			],
			'teams' => [
				[
					'name' => 'Wiji Fiko Teren',
					'role' => 'Programmer',
					'pictures' => 'wiji-fiko-teren.jpg',
					'social' => [
						['type' => 'facebook', 'url' => 'https://facebook.com/fiko.tobel'],
						['type' => 'whatsapp', 'url' => 'https://wa.me/6285935099343']
					]
				],
			]
		];
		return view('landing', $data);
	}
}
