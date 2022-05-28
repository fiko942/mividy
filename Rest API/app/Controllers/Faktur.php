<?php
namespace App\Controllers;

use \Config\Services;
use CodeIgniter\HTTP\IncomingRequest;

class Faktur extends BaseController
{

	public function index(){
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	}

	private function minify($string) {
		$blackChars = ['	', "\r\n", "\r", "\n", '   '];
		return str_replace($blackChars, '', $string);
	}

	public function getFaktur(string $id = ""){
		$db = \Config\Database::connect();
		$resultFaktur = $db->query("SELECT * FROM transaksi WHERE id = '{$id}'")->getRowArray();
		$data = [
			'faktur' => $resultFaktur,
			'namaAplikasi' => $this->appName,
			'developerAplikasi' => $this->appDeveloper,
		];
		return $this->minify(view('faktur/single', $data));
	}

	public function css(){
		header('Content-Type: text/css');
		return $this->minify(view('faktur/65167.css'));
	}
}