<?php
namespace App\Controllers;

use \Config\Services;
use CodeIgniter\HTTP\IncomingRequest;
use \Mpdf\Mpdf;

class Api extends BaseController
{
    // public function hapus_semua_transaksi(){
    //     $this->sqlQuery("DELETE FROM transaksi");
    //     echo 'true';
    // }
    public function cekKeadaanFaktur($sesi = "", $id = ""){
        $id = trim(preg_replace('/[^0-9]/','',$id));
        $faktur = $this->sqlQuery("SELECT * FROM transaksi WHERE id = '{$id}'");
        $sesiLogin = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$sesi}'")->getNumRows();
        if($sesiLogin < 1) {
            $this->throwResponse([
                'catchException' => true,
                'message' => 'Akses anda ditolak'
            ]);
        } else {
            $this->tambahkanRiwayatAktifitasAdmin($sesi, "Melakukan cek keadaan faktur");
            if($faktur->getNumRows() < 1) {
                $this->throwResponse([
                    'catchException' => true,
                    'message' => 'Faktur dengan ID: ' . $id . ' tidak tersedia'
                ]);
            } else {
                $faktur = $faktur->getRowArray();
                $this->throwResponse([
                    'catchException' => false,
                    'message' => 'Faktur ditemukan',
                    'fileName' => "Faktur #{$faktur['id']} - {$this->appName}.pdf"
                ]);
            }
        }
    }

    // public function generate_transaksi($data = 1) {
    //     for($i = 0; $i < $data; $i++) {
    //         $tanggal = date('d-m-Y  h:i:s A');
    //         $this->sqlQuery("INSERT INTO transaksi (id, nama, ditambahkan_oleh, tanggal, nominal, barang) VALUES({$this->generateIdTransaksi(8)}, 'SYSTEM', 'SYSTEM', '{$tanggal}', '1', 'Undefined')");
    //     }
    //     echo 'SUCCESS';
    // }

    public function minimalDashboard($sesi){
        $dataSesi = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$sesi}'");
        if($dataSesi->getNumRows() < 1) {
            $this->throwResponse([
                'catchException' => true,
                'message' => 'Akses anda ditolak'
            ]);
        } else {
            $this->tambahkanRiwayatAktifitasAdmin($sesi, "Melihat data pada minimal dashboard");
            $pendapatan = 0;
            $totalTransaksi = 0;

            $dataTransaksi = $this->sqlQuery("SELECT * FROM transaksi");
            foreach($dataTransaksi->getResultArray() as $dt) {
                $pendapatan = $pendapatan + $dt['nominal'];
                $totalTransaksi++;
            }
            $this->throwResponse([
                'catchException' => false,
                'message' => 'OK',
                'data' => [
                    'pendapatan' => $this->formatRupiah($pendapatan),
                    'totalTransaksi' => number_format($totalTransaksi, 0, ',', '.')
                ]
            ]);
        }
    }

    public function downloadPdf($id) {
        $id = trim(preg_replace('/[^0-9]/','',$id));
        $result = $this->sqlQuery("SELECT * FROM transaksi WHERE id = {$id}");
        if($result->getNumRows() < 1) {
            $this->throwResponse([
                'catchException' => true,
                'message' => "Id transaksi: {$id} tidak ditemukan"
            ]);
        } else {
            $this->response->setHeader('Content-Type', 'application/pdf');
            $faktur = $result->getRowArray();
            $namaAplikasi = $this->appName;
            $developerAplikasi = $this->appDeveloper;
            $ff = "'Open Sans', sans-serif";
            $html = '<!DOCTYPE html>
            <html lang="id">
            <head><style>
            body {
                margin: 0;
                padding: 0;
                background: #48515A;
            }

            div,
            p,
            a,
            li,
            td {
                -webkit-text-size-adjust: none;
            }

            * {
            font-family: '.$ff.' !important;
        }

        .ReadMsgBody {
            width: 100%;
            background-color: #48515A;
        }

        .ExternalClass {
            width: 100%;
            background-color: #48515A;
        }

        body {
            width: 100%;
            height: 100%;
            background-color: #48515A;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        html {
            width: 100%;
        }

        p {
            padding: 0 !important;
            margin-top: 0 !important;
            margin-right: 0 !important;
            margin-bottom: 0 !important;
            margin-left: 0 !important;
        }

        .visibleMobile {
            display: none;
        }

        .hiddenMobile {
            display: block;
        }

        @media only screen and (max-width: 600px) {
            body {
                width: auto !important;
            }
            table[class=fullTable] {
                width: 100% !important;
                clear: both;
            }
            table[class=fullPadding] {
                width: 100% !important;
                clear: both;
            }
            table[class=col] {
                width: 45% !important;
            }
            .erase {
                display: none;
            }
        }

        @media only screen and (max-width: 420px) {
            table[class=fullTable] {
                width: 100% !important;
                clear: both;
            }
            table[class=fullPadding] {
                width: 85% !important;
                clear: both;
            }
            table[class=col] {
                width: 100% !important;
                clear: both;
            }
            table[class=col] td {
                text-align: left !important;
            }
            .erase {
                display: none;
                font-size: 0;
                max-height: 0;
                line-height: 0;
                padding: 0;
            }
            .visibleMobile {
                display: block !important;
            }
            .hiddenMobile {
                display: none !important;
            }
            }</style></head>
            <body>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#48515A">
            <tr>
            <td height="10"></td>
            </tr>
            <tr>
            <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#1C1C1C" style="border-radius: 0 0 0 0;">
            <tr class="hiddenMobile">
            <td height="20"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="10"></td>
            </tr>
            <tr>
            <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
            <tbody>
            <tr>
            <td>
            <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
            <tbody>
            <tr>
            <td align="left"> <img src="'.base_url('favicon.ico').'" width="60" height="60" alt="logo" border="0" /></td>
            </tr>
            <tr class="hiddenMobile">
            <td height="20"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="10"></td>
            </tr>
            <tr>
            <td style="font-size: 12px; color: #16B2B2; font-family: '.$ff.', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">Halo '.$faktur['nama'].', <br>Terimakasih telah melakukan pembelian.</td>
            </tr>
            </tbody>
            </table>
            <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
            <tbody>
            <tr class="visibleMobile">
            <td height="20"></td>
            </tr>
            <tr>
            <td height="5"></td>
            </tr>
            <tr>
            <td style="font-size: 21px; color: #ff0000; letter-spacing: -1px; font-family: '.$ff.', sans-serif; line-height: 1; vertical-align: top; text-align: right; letter-spacing: 1px;"> Faktur #'.$faktur['id'].' </td>
            </tr>
            <tr> 
            <tr class="hiddenMobile">
            <td height="30"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="20"></td>
            </tr>
            <tr>
            <td style="font-size: 12px; color: #16B2B2; font-family: '.$ff.'; line-height: 18px; vertical-align: top; text-align: right;"> <small>STATUS:</small> Telah Dibayar <br> <small>DATE: '.$faktur['tanggal'].'</small> </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </table>
            </td>
            </tr>
            </table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#48515A">
            <tbody>
            <tr>
            <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#1C1C1C">
            <tbody>
            <tr> 
            <tr class="hiddenMobile">
            <td height="20"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="10"></td>
            </tr>
            <tr>
            <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
            <tbody>
            <tr>
            <th style="font-size: 14px; letter-spacing: 1px; '.$ff.'; color: #16B2B2; font-weight: 500; line-height: 1; vertical-align: top; padding: 0 10px 7px 0;" width="52%" align="left"> Item </th>
            <th style="font-size: 14px;letter-spacing: 1px; '.$ff.'; color: #16B2B2; font-weight: 500; line-height: 1; vertical-align: top; padding: 0 0 3px;" align="right"> Harga </th>
            </tr>
            <tr>
            <td height="1" style="background: #17B3B4;" colspan="4"></td>
            </tr>
            <tr>
            <td height="10" colspan="4"></td>
            </tr>
            <tr>
            <td style="font-size: 13px; '.$ff.'; color: #ff0000; line-height: 18px; vertical-align: top; padding:10px 0; padding-top: 0;" class="article"> '.$faktur['barang'].' </td>
            <td style="font-size: 13px; '.$ff.'; color: #16B2B2; line-height: 18px; vertical-align: top; padding:10px 0; padding-top: 0;" align="right">'.$this->formatRupiah($faktur['nominal']).'</td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            <tr>
            <td height="10"></td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>
            <!-- /Order Details --> <!-- Total --> 
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#48515A">
            <tbody>
            <tr>
            <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#1C1C1C">
            <tbody>
            <tr>
            <td>
            <!-- Table Total --> 
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
            <tbody>
            <tr>
            <td style="font-size: 12px; '.$ff.'; color: #16B2B2; line-height: 22px; vertical-align: top; text-align:right; "> <strong>Jumlah: '.$this->formatRupiah($faktur['nominal']).'</strong> </td>
            </tr>
            </tbody>
            </table>
            <!-- /Table Total --> 
            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>
            <!-- /Total --> <!-- Information --> 
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#48515A">
            <tbody>
            <tr>
            <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#1C1C1C">
            <tbody>
            <tr> 
            <tr class="hiddenMobile">
            <td height="10"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="10"></td>
            </tr>
            <tr>
            <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
            <tbody>
            <tr>
            <td>
            <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
            <tbody>
            <tr class="hiddenMobile">
            <td height="35"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="20"></td>
            </tr>
            <tr>
            <td style="font-size: 11px; '.$ff.'; color: #16B2B2; line-height: 1; vertical-align: top; "> <strong>TAGIHAN KEPADA</strong> </td>
            </tr>
            <tr>
            <td width="100%" height="10"></td>
            </tr>
            <tr>
            <td style="font-size: 12px; '.$ff.'; color: #16B2B2; line-height: 20px; vertical-align: top; "> '.$faktur['nama'].'</td>
            </tr>
            </tbody>
            </table>
            <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col" style="width: fit-content;">
            <tbody>
            <tr class="hiddenMobile">
            <td height="35"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="20"></td>
            </tr>
            <tr>
            <td style="font-size: 11px; '.$ff.'; color: #16B2B2; line-height: 1; vertical-align: top; "> <strong>DIBAYARKAN KEPADA</strong> </td>
            </tr>
            <tr>
            <td width="100%" height="10"></td>
            </tr>
            <tr>
            <td style="font-size: 12px; '.$ff.'; color: #16B2B2; line-height: 20px; vertical-align: top; ">'.$namaAplikasi.' - '.$developerAplikasi.'</td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            <tr class="hiddenMobile">
            <td height="30"></td>
            </tr>
            <tr class="visibleMobile">
            <td height="15"></td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>
            <!-- /Information --> 
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#48515A">
            <tr>
            <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#1C1C1C" style="border-radius: 0 0 0 0;">
            <tr>
            <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
            <tbody>
            <tr>
            <td style="font-size: 12px; color: #16B2B2; '.$ff.'; line-height: 18px; vertical-align: top; text-align: left;"> Terimakasih dan salam hormat,<br>'.$faktur['ditambahkan_oleh'].' - '.$namaAplikasi.'</td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            <tr class="spacer">
            <td height="30"></td>
            </tr>
            </table>
            </td>
            </tr>
            <tr>
            <td height="20"></td>
            </tr>
            </table>
            </body>
            </html>';
            // die($html);

// http://localhost:8080/faktur/cetak-faktur/01759384

            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            $this->response->setHeader('Content-Type', 'application/pdf');
            $mpdf->Output('arjun.pdf','I');
        }
    }

    public function testpdf(){
        $mpdf = new \Mpdf\Mpdf();
        $html = file_get_contents('http://localhost:8080/faktur/01759384.html');
        $mpdf->writeHTML($html);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf->Output('muhendo.pdf', 'I');


    }

    public function index()
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    private function emailTemplating($message, $namaPengirim)
    {
        $ff = "'Consolas'";
        $template = '<!DOCTYPE html><html><head> <meta charset="utf-8"> <meta name="viewport" content="width=device-width, initial-scale=1"></head><body style="background-color: #ffffff; left: 0; top: 0; margin-bottom: 10px; color: #48515A; border-radius: 0px; padding-bottom: 10px;"> <div style="top: 0; left: 0; border-top-left-radius: 10px; border-top-right-radius: 10px; height: 85px; background-color: #ffffff; background-size: cover; position: relative; display: flex; text-align: center; justify-content: center; margin: auto; text-decoration: none;"> <a href="https://resume.tobelsoft.my.id/" target="_blank" style="text-decoration: none; display: flex; position: relative; margin-top: 0; padding: 0; text-align: center; justify-items: center; align-items: center;"><h3 style="font-family: ' . $ff . '; font-size: 25px; color: #16B2B3; margin-left: 20px; text-align: center; padding: 0; text-decoration: none;">' . trim(htmlspecialchars($this->appName)) . '</h3> </a> </div> <div style="top: 0; left: 0; padding: 0 20px 0 20px; border: 2px solid #2E2946; margin: 10px; margin-bottom: 10px; text-decoration: none;"> <h4 style="font-family: ' . $ff . '; font-size: 17px; color: #2E2946; text-decoration: none;">' . $message . '<br><br>Salam hangat, ' . $this->appName . '<br>' . $this->appDeveloper  . '. </h4> <hr style="border: 1px solid #2E2946; width: 70%; text-decoration: none;"> <p style="font-family: ' . $ff . '; font-weight: 700; text-align: center; font-size: 17px; color: #2E2946;"><a style="text-decoration: none; color: #0ABABB;" href="https://resume.tobelsoft.my.id">Copyright &copy; '.date('Y'). ' ' . trim(htmlspecialchars($this->appName)) .'</a>.</p> </div></body></html>';
        return $template;
    }

    public function ambil_semua_barang($session)
    {
        $resSession = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'");
        if ($resSession->getNumRows() > 0)
        {
            $this->tambahkanRiwayatAktifitasAdmin($session, "Melihat seluruh data barang");
            $resBarang = $this->sqlQuery("SELECT * FROM barang ORDER BY id DESC");
            $result = [];
            foreach ($resBarang->getResultArray() as $row)
            {
                array_push($result, ["barang" => $row["nama_barang"]]);
            }
            $this->throwResponse($result);
        }
    }

    public function resetPassword($email)
    {
        $email = trim($email);
        if ($this
            ->request
            ->getVar('kode'))
        {
            $getCode = $this
            ->request
            ->getVar('kode');
            $resultCode = $this->sqlQuery("SELECT * FROM kode_reset_password WHERE email = '{$email}' AND kode = '{$getCode}'");
            if ($resultCode->getNumRows() < 1)
            {
                $this->throwResponse(['catchException' => true, 'message' => 'Kode yang anda masukkan salah']);
            }
            else
            {
                $password = $this
                ->request
                ->getVar('newPassword');
                if ($password)
                {
                    $passwordEncoded = password_hash($password, PASSWORD_DEFAULT);
                    $q = $this->sqlQuery("UPDATE akun SET password = '{$passwordEncoded}' WHERE email = '{$email}'");
                    if ($q)
                    {
                        $mailEngine = \Config\Services::email();
                        $mailConfig = ['protocol' => 'smtp', 'wordWrap' => true, 'userAgent' => $this->appName . ' Engine', 'SMTPHost' => $this->mailHost, 'SMTPUser' => $this->mailSender, 'SMTPPass' => $this->mailPassword, 'SMTPPort' => $this->mailPort, 'SMTPTimeout' => 10, 'SMTPCrypto' => $this->mailCrypto, 'mailType' => 'html', 'priority' => 1, 'CRLF' => "\r\n", 'newline' => "\r\n", ];
                        $mailEngine->initialize($mailConfig);
                        $mailEngine->setFrom($this->mailSender, $this->appName);
                        $mailEngine->setTo($email);
                        $mailEngine->setSubject('Password anda telah berhasil diubah');
                        $mailEngine->setMessage($this->emailTemplating('Password anda telah berhasil diubah.', $this->appName));
                        $mailEngine->send();
                        $this->sqlQuery("DELETE FROM sesi_login WHERE email = '{$email}'");
                        $this->sqlQuery("DELETE FROM kode_reset_password WHERE email = '{$email}'");
                        $this->throwResponse(['catchException' => false, 'message' => 'Password anda telah berhasil diubah, sesi login yang menggunakan akun anda telah berhasil dihapus, silahkan login kembali untuk melanjutkan']);
                    }
                }
                else
                {
                    $this->throwResponse(['catchException' => false, 'message' => 'Kode yang anda masukkan benar']);
                }

                $passwordEncoded = password_hash($password, PASSWORD_DEFAULT);
                $q = $this->sqlQuery("UPDATE akun SET password = '{$passwordEncoded}' WHERE email = '{$email}'");
                if ($q)
                {
                    $mailEngine = \Config\Services::email();
                    $mailConfig = ['protocol' => 'smtp', 'wordWrap' => true, 'userAgent' => $this->appName . ' Engine', 'SMTPHost' => $this->mailHost, 'SMTPUser' => $this->mailSender, 'SMTPPass' => $this->mailPassword, 'SMTPPort' => $this->mailPort, 'SMTPTimeout' => 10, 'SMTPCrypto' => $this->mailCrypto, 'mailType' => 'html', 'priority' => 1, 'CRLF' => "\r\n", 'newline' => "\r\n", ];
                    $mailEngine->initialize($mailConfig);
                    $mailEngine->setFrom($this->mailSender, $this->appName);
                    $mailEngine->setTo($email);
                    $mailEngine->setSubject('Password anda telah berhasil diubah');
                    $mailEngine->setMessage($this->emailTemplating('Password anda telah berhasil diubah.', $this->appName));
                    $this->sqlQuery("DELETE FROM sesi_login WHERE email = '{$email}'");
                    $this->sqlQuery("DELETE FROM kode_reset_password WHERE email = '{$email}'");
                    $this->throwResponse(['catchException' => false, 'message' => 'Password anda telah berhasil diubah, sesi login yang menggunakan akun anda telah berhasil dihapus, silahkan login kembali untuk melanjutkan']);
                }
                $passwordEncoded = password_hash($password, PASSWORD_DEFAULT);
                $q = $this->sqlQuery("UPDATE akun SET password = '{$passwordEncoded}' WHERE email = '{$email}'");
                if ($q)
                {
                    $mailEngine = \Config\Services::email();
                    $mailConfig = ['protocol' => 'smtp', 'wordWrap' => true, 'userAgent' => $this->appName . ' Engine', 'SMTPHost' => $this->mailHost, 'SMTPUser' => $this->mailSender, 'SMTPPass' => $this->mailPassword, 'SMTPPort' => $this->mailPort, 'SMTPTimeout' => 10, 'SMTPCrypto' => $this->mailCrypto, 'mailType' => 'html', 'priority' => 1, 'CRLF' => "\r\n", 'newline' => "\r\n", ];
                    $mailEngine->initialize($mailConfig);
                    $mailEngine->setFrom($this->mailSender, $this->appName);
                    $mailEngine->setTo($email);
                    $mailEngine->setSubject('Password anda telah berhasil diubah');
                    $mailEngine->setMessage($this->emailTemplating('Password anda telah berhasil diubah.', $this->appName));
                    $this->sqlQuery("DELETE FROM sesi_login WHERE email = '{$email}'");
                    $this->sqlQuery("DELETE FROM kode_reset_password WHERE email = '{$email}'");
                    $this->throwResponse(['catchException' => false, 'message' => 'Password anda telah berhasil diubah, sesi login yang menggunakan akun anda telah berhasil dihapus, silahkan login kembali untuk melanjutkan']);
                }
            }
            die;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->throwResponse(['catchException' => true, 'message' => 'Email yang anda masukkan tidak valid']);
        }
        else
        {
            $resultAccount = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$email}'");
            if ($resultAccount->getNumRows() < 1)
            {
                $this->throwResponse(['catchException' => true, 'message' => 'Email yang anda masukkan tidak terdaftar']);
            }
            else
            {
                $account = $resultAccount->getRowArray();
                if ($account['email_verified'] != 1)
                {
                    $this->throwResponse(['catchException' => true, 'message' => 'Email yang anda masukkan belum terverifikasi']);
                }
                else
                {
                    $resultKode = $this->sqlQuery("SELECT * FROM kode_reset_password WHERE email = '{$email}'");
                    if ($resultKode->getNumRows() > 0)
                    {
                        $this->sqlQuery("DELETE FROM kode_reset_password WHERE email = '{$email}'");
                    }
                    helper('text');
                    regenerateKode:
                    $kode = random_string('numeric', 6);
                    $researchCode = $this->sqlQuery("SELECT * FROM kode_reset_password WHERE kode = {$kode}")->getNumRows();
                    if ($researchCode > 0)
                    {
                        gotoregenerateKode;
                    }
                    $q = $this->sqlQuery("INSERT INTO kode_reset_password (id, email, kode) VALUES(NULL, '{$email}', {$kode})");
                    if ($q)
                    {
                        $mailEngine = \Config\Services::email();
                        $mailConfig = ['protocol' => 'smtp', 'wordWrap' => true, 'userAgent' => $this->appName . ' Engine', 'SMTPHost' => $this->mailHost, 'SMTPUser' => $this->mailSender, 'SMTPPass' => $this->mailPassword, 'SMTPPort' => $this->mailPort, 'SMTPTimeout' => 10, 'SMTPCrypto' => $this->mailCrypto, 'mailType' => 'html', 'priority' => 1, 'CRLF' => "\r\n", 'newline' => "\r\n", ];
                        $mailEngine->initialize($mailConfig);
                        $mailEngine->setFrom($this->mailSender, $this->appName);
                        $mailEngine->setTo($email);
                        $mailEngine->setSubject('Kode reset password');
                        $mailEngine->setMessage($this->emailTemplating($kode, $this->appName));
                        if ($mailEngine->send())
                        {
                            $this->throwResponse(['catchException' => false, 'message' => "Kode reset password telah dikirim melalui email: {$email}"]);
                        }
                        else
                        {
                            $this->sqlQuery("DELETE FROM kode_reset_password WHERE kode = {$kode}");
                            $this->throwResponse(['catchException' => true, 'message' => 'Terjadi kesalahan saat mengirim email kode reset password, silahkan coba kembali nanti']);
                        }
                    }
                    else
                    {
                        $this->throwResponse(['catchException' => 'Terjadi kesalahan saat melakukan proses data, silahkan coba kembali nanti', 'error' => true]);
                    }
                }
            }
        }
    }

    private function formatRupiah(int $nominal)
    {
        return "Rp " . number_format($nominal, 0, ',', '.');
    }

    public function tambahkan_admin($session, $nama, $email)
    {
        $sessionResult = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'");
        if ($sessionResult->getNumRows() > 0)
        {
            $this->tambahkanRiwayatAktifitasAdmin($session, "Menambahkan admin baru");
            $emailAdmin = $sessionResult->getRowArray() ["email"];
            $resultAdmin = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$emailAdmin}'")->getRowArray();
            if ($resultAdmin["read_only"] == 1)
            {
                $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena tidak mempunyai hak terhadap perubahan server");
                $this->throwResponse(['catchException' => true, 'message' => 'Anda tidak mempunyai hak untuk melakukan perubahan data pada server']);
                exit();
            }
            else
            {
                $nama = trim($nama);
                $email = trim($email);
                if (strlen($nama) < 3)
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena memasukkan nama kurang dari 3 karakter");
                    $this->throwResponse(['catchException' => true, 'message' => 'Masukkan nama lebih dari 3 karakter']);
                }
                elseif (strlen($nama) > 50)
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena memasukkan nama lebih dari karakter");
                    $this->throwResponse(['catchException' => true, 'message' => 'Masukkan nama kurang dari 50 karakter']);
                }
                elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena memasukkan email yang tidak valid");
                    $this->throwResponse(['catchException' => true, 'message' => 'Email yang anda masukkan tidak valid']);
                }
                elseif (!strstr($email, '@gmail.com') && !strstr($email, '@outlook.com'))
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena layanan email yang digunakan bukan dari sumber yang terpercaya");
                    $this->throwResponse(['catchException' => true, 'message' => 'Gunakan email dari layanan email yang terpercaya, seperti Gmail atau Outlook']);
                }
                else
                {
                    $emailResult = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$email}'");
                    $namaResult = $this->sqlQuery("SELECT * FROM akun WHERE name = '{$nama}'");
                    if ($emailResult->getNumRows() > 0)
                    {
                        $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena email {$email} sudah terdaftar sebagai admin");
                        $this->throwResponse(['catchException' => true, 'message' => 'Email sudah terdaftar sebagai admin. tidak dapat melakukan duplikasi']);
                    }
                    elseif ($namaResult->getNumRows() > 0)
                    {
                        $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena nama {$nama} sudah terdaftar sebagai admin");
                        $this->throwResponse(['catchException' => true, 'message' => 'Nama sudah terdaftar sebagai admin. Tidak dapat melakukan duplikasi']);
                    }
                    else
                    {

                        $dataUpLink = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'")->getRowArray();
                        $upLinkEmail = $dataUpLink['email'];
                        $dataUpLink = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$upLinkEmail}'")->getRowArray();

                        $mail = \Config\Services::email();
                        $mailConfig = ['protocol' => 'smtp', 'wordWrap' => true, 'userAgent' => $this->appName . ' Engine', 'SMTPHost' => $this->mailHost, 'SMTPUser' => $this->mailSender, 'SMTPPass' => $this->mailPassword, 'SMTPPort' => $this->mailPort, 'SMTPTimeout' => 10, 'SMTPCrypto' => $this->mailCrypto, 'mailType' => 'html', 'priority' => 1, 'CRLF' => "\r\n", 'newline' => "\r\n", ];
                        $mail->initialize($mailConfig);
                        $mail->setTo($email);
                        $mail->setFrom($this->mailSender, $this->appName);
                        $mail->setSubject($this->appName . " - Hai {$nama}, " . $dataUpLink['name'] . " mengirimkan pesan kepada anda");
                        $namaUplink = htmlspecialchars(trim($dataUpLink['name']));
                        $tokenVerifikasi = $this->randomString(40);
                        $linkVerifikasi = base_url('api/akun/verifikasi-email/' . $tokenVerifikasi);
                        $mail->setMessage($this->emailTemplating("Hai {$nama}, saya {$namaUplink} ingin mengajak anda untuk bergabung dengan kami {$this->appName} sebagai admin. Jika anda setuju untuk bergabung bersama kami anda dapat klik <a style='color: #0ABABB; text-decoration: none;' href='{$linkVerifikasi}'>link disini</a>.<br><br>Jika link diatas tidak bisa diklik anda dapat copy url dibawah dan navigasi manual via browser yang terinstall pada komputer anda.<br><br><span style='color: #0ABABB;'>LINK VERIFIKASI: <a style='color: #0ABABB; text-decoration: none;' href='{$linkVerifikasi}'>{$linkVerifikasi}</a></span>", $dataUpLink['name']));
                        if ($mail->send())
                        {
                            $this->tambahkanRiwayatAktifitasAdmin($session, "Berhasil menambahkan admin baru dengan nama {$nama} dan email {$email}");
                            $namaUpLink = $dataUpLink['name'];
                            $this->sqlQuery("INSERT INTO akun VALUES(null, '{$nama}', '{$email}', 'NO-PASSWORD', 1, 0, '{$namaUpLink}')");
                            $this->sqlQuery("INSERT INTO token_verifikasi VALUES(null, '{$email}', '{$tokenVerifikasi}')");
                            $this->throwResponse(['catchException' => false, 'message' => "Akun telah berhasil dibuat dengan baik. Link untuk melakukan verifikasi sebagai admin sudah dikirim ke alamat email {$email}, password pada akun email tersebut akan otomatis digenerate setelah berhasil melakukan verifikasi."]);
                        }
                        else
                        {
                            die($mail->printDebugger(['headers']));
                            $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan admin ditolak karena terjadi kesalahan pada mailer");
                            $this->throwResponse(['catchException' => true, 'message' => "Terjadi kesalahan saat menambahkan admin baru karena mailer terjadi kesalahan"]);
                        }

                    }
                }
            }
        }
    }

    private function tambahkanRiwayatAktifitasAdmin($sesiAdmin, $log)
    {
        $resultSesi = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$sesiAdmin}'");
        $email = $resultSesi->getRowArray() ["email"];
        $resultAdmin = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$email}'");
        $admin = $resultAdmin->getRowArray();
        $identitasAdmin = $admin['name'] . ' - ' . $admin['email'];
        $pada = date('d-m-Y  h:i:s A');
        $query = $this->sqlQuery("INSERT INTO aktifitas_admin VALUES(null, '{$identitasAdmin}', '{$log}', '{$pada}')");
        return true;
    }

    private function generateIdTransaksi($length = 1) {
        generateUlang:
        $randId = null;
        for($i = 0; $i < $length; $i++) {
            $randId .= random_int(0, 9);
        }
        $resultTransaksi = $this->sqlQuery("SELECT * FROM transaksi WHERE id = {$randId}")->getNumRows();
        if($resultTransaksi > 0) {
            goto generateUlang;
        } else {
            return $randId;
        }
    }

    public function tambahkan_data_transaksi($session, $nama, $nominal, $barang)
    {
        $resultSession = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'");
        if ($resultSession->getNumRows() > 0)
        {
            $sessEmail = $resultSession->getRowArray() ["email"];
            $resultUser = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$sessEmail}'");
            if ($resultUser->getNumRows() > 0)
            {
                $user = $resultUser->getRowArray();
                $this->tambahkanRiwayatAktifitasAdmin($session, "Menambahkan data transaksi");
                if ($user["read_only"] == 1)
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena tidak mempunyai hak terhadap perubahan data pada server");
                    $this->throwResponse(['catchException' => true, 'message' => 'Maaf, kamu tidak mempunyai hak untuk melakukan perubahan terhadap data pada server']);
                }
                elseif ($user["email_verified"] == 0)
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena email belum terverifikasi");
                    $this->throwResponse(['catchException' => true, 'message' => 'Email anda belum terverifikasi, silahkan verifikasi terlebih dahulu']);
                }
                elseif (strlen($nama) < 3)
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena memasukkan nama kurang dari 3 karakter");
                    $this->throwResponse(['catchException' => true, 'message' => 'Masukkan nama lebih dari 3 karakter']);
                }
                elseif (strlen($nama) > 50)
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena memasukkan nama lebih dari 50 karakter");
                    $this->throwResponse(['catchException' => true, 'message' => 'Masukkan nama kurang dari 50 karakter']);
                }
                elseif (!is_numeric($nominal))
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena mencoba memberikan value nominal bukan angka");
                    $this->throwResponse(['catchException' => true, 'message' => 'Nominal hanya dapat di isi hanya angka']);
                }
                elseif (strlen($nominal) > 12)
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena memasukkan nominal lebih dari 12 karkater");
                    $this->throwResponse(['catchException' => true, 'message' => 'Masukkan nominal kurang dari 12 karkter']);
                }
                elseif (empty($barang) || $barang == "Pilih barang")
                {
                    $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena belum memilih barang");
                    $this->throwResponse(['catchException' => true, 'message' => 'Silahkan pilih barang terlebih dahulu']);
                }
                else
                {
                    $resultBarang = $this->sqlQuery("SELECT * FROM barang WHERE nama_barang = '{$barang}'");
                    if ($resultBarang->getNumRows() < 1)
                    {
                        $this->tambahkanRiwayatAktifitasAdmin($session, "Penambahan data transaksi ditolak karena barang yang dipilih tidak ada didalam database");
                        $this->throwResponse(['catchException' => true, 'message' => 'Barang yang anda pilih tidak ada didalam database']);
                        exit;
                    }
                    $ditambahkanOleh = trim($user['name']);
                    $tanggal = date('d-m-Y  h:i:s A');
                    $idTransaksi = $this->generateIdTransaksi(8);
                    $query = $this->sqlQuery("INSERT INTO transaksi VALUES('{$idTransaksi}', '{$nama}', '{$ditambahkanOleh}', '{$tanggal}', '{$nominal}', '{$barang}')");
                    if ($query)
                    {
                        $nominal = $this->formatRupiah($nominal);
                        $this->tambahkanRiwayatAktifitasAdmin($session, "Berhasil menambahkan data transaksi dari {$nama} sebesar {$nominal} untuk pembelian {$barang}");
                        $this->throwResponse(['catchException' => false, 'message' => 'Data transaksi berhasil ditambahkan', 'urlFaktur' => base_url('faktur/'.$idTransaksi . '.html'), 'idFaktur' => $idTransaksi]);
                    }
                    else
                    {
                        $this->tambahkanRiwayatAktifitasAdmin($session, "Terjadi kesalahan yang tidak dapat teridentifikasi pada saat melakukan penambahan data");
                        $this->throwResponse(['catchException' => true, 'message' => 'Gagal menambahkan data transaksi']);
                    }
                }
            }
        }
    }

    public function ambil_semua_transaksi($session, $searchQuery = "")
    {
        $resSession = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'");
        if ($resSession->getNumRows() > 0)
        {
            if ($searchQuery == "")
            {
                $resTransaction = $this->sqlQuery("SELECT * FROM transaksi ORDER BY id DESC");
            }
            else
            {
                $resTransaction = $this->sqlQuery("
                    SELECT * FROM transaksi 
                    WHERE id LIKE '%{$searchQuery}%'
                    OR nama LIKE '%{$searchQuery}%'
                    OR ditambahkan_oleh LIKE '%{$searchQuery}%'
                    OR barang LIKE '%{$searchQuery}%'
                    OR tanggal LIKE '%{$searchQuery}%'
                    ORDER BY id DESC
                    ");
            }
            $res = [];
            foreach ($resTransaction->getResultArray() as $row)
            {
                array_push($res, ["id" => $row["id"], "nama" => $row["nama"], "barang" => $row["barang"], "ditambahkan_oleh" => $row["ditambahkan_oleh"], "tanggal" => $row["tanggal"], "nominal" => $this->formatRupiah($row["nominal"]) ]);
            }
            $this->tambahkanRiwayatAktifitasAdmin($session, "Melihat seluruh data transaksi");
            $this->throwResponse($res);
        }
    }

    public function hapus_transaksi($session, $id)
    {

        $this->tambahkanRiwayatAktifitasAdmin($session, "Menghapus data transaksi dengan ID: {$id}");
        $sessionResult = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'")->getRowArray() ["email"];
        $adminResult = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$sessionResult}'")->getRowArray();
        if ($adminResult["read_only"] == 1)
        {
            $this->tambahkanRiwayatAktifitasAdmin($session, "Penghapusan data transaksi ditolak karena tidak mempunyai hak untuk melakukan perubahan data pada server");
            $this->throwResponse(['catchException' => true, 'message' => 'Anda tidak punya hak untuk melakukan perubahan data pada server']);
        }
        $sessionResult = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'");
        if ($sessionResult->getNumRows() < 1)
        {
            die;
        }
        $dataTransaksi = $this->sqlQuery("SELECT * FROM transaksi WHERE id = {$id}");
        if ($dataTransaksi->getNumRows() < 1)
        {
            $this->tambahkanRiwayatAktifitasAdmin($session, "Penghapusan data transaksi ditolak karena data tidak ditemukan");
            $this->throwResponse(['catchException' => true, 'message' => 'Data transaksi tidak ditemukan']);
        }
        else
        {
            $transaksi = $dataTransaksi->getRowArray();
            $query = $this->sqlQuery("DELETE FROM transaksi WHERE id = {$id}");
            if ($query)
            {
                $nama = $transaksi['nama'];
                $barang = $transaksi['barang'];
                $nominal = $this->formatRupiah($transaksi['nominal']);
                $this->tambahkanRiwayatAktifitasAdmin($session, "Berhasil menghapus data transaksi dari {$nama} dengan pembelian {$barang} sebesar {$nominal}");
                $this->throwResponse(['catchException' => false, 'message' => "Data transaksi dari {$nama} dengan pembelian {$barang} sebesar {$nominal} telah berhasil dihapus"]);
            }
            else
            {
                $this->tambahkanRiwayatAktifitasAdmin($session, "Terjadi kesalahan yang tidak teridentifikasi saat melakukan penghapusan data transaksi dengan id {$id}");
                $this->throwResponse(['catchException' => true, 'message' => "Terjadi kesalahan saat menghapus data transaksi dengan id {$id}"]);
            }
        }
    }

    private function randomstring(int $length)
    {
        $bytes = random_bytes($length);
        $str = base64_encode($bytes);
        $str = str_replace(['=', '-', '+', '/'], '', $str);
        return $str;
    }

    public function validate_session($session)
    {
        $result = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'");
        if ($result->getNumRows() < 1)
        {
            $this->throwResponse(['catchException' => true, 'message' => 'Session is invalid']);
        }
        else
        {
            $this->throwResponse(['catchException' => false, 'message' => 'Session is valid']);
        }
    }

    private function getUserAgent()
    {
        $agent = $this
        ->request
        ->getUserAgent();
        if ($agent->isBrowser())
        {
            $currentAgent = $agent->getBrowser() . ' ' . $agent->getVersion();
        }
        elseif ($agent->isRobot())
        {
            $currentAgent = $agent->getRobot();
        }
        elseif ($agent->isMobile())
        {
            $currentAgent = $agent->getMobile();
        }
        else
        {
            $currentAgent = 'User agent tidak teridentifikasi';
        }
        return $agent->getPlatform() . ' - ' . $currentAgent;
    }

    public function logadmin($session)
    {
        $this->tambahkanRiwayatAktifitasAdmin($session, "Melihat aktifitas admin");
        $resultSession = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$session}'")->getNumRows();
        $separatorLength = 150;
        if ($resultSession > 0)
        {
            $pesanUtama = "RIWAYAT AKTIFITAS SEMUA ADMIN - {$this->appName} {$this->appDeveloper}";
            $dataLog = $this->sqlQuery("SELECT * FROM aktifitas_admin ORDER BY id DESC")
            ->getResultArray();
            echo $pesanUtama . "\r\n";
            for ($i = 0;$i < $separatorLength;$i++)
            {
                echo '-';
            }
            $interval = 1;
            foreach ($dataLog as $dl)
            {
                $iA = $dl['identitas_admin'];
                $l = $dl['log'];
                $p = $dl['pada'];
                echo "\r\n{$interval}. [{$p}][{$iA}] => {$l}";
                $interval++;
            }
            echo "\r\n";
            for ($i = 0;$i < $separatorLength;$i++)
            {
                echo '-';
            }
            echo "\r\n" . $pesanUtama;
        }
        die;
    }

    public function editFakturTransaksi($sesi, $idTransaksi, $nama, $nominal, $barang) {
        $resSesi = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$sesi}'");
        if($resSesi->getNumRows() < 1) {
            die("Akses anda ditolak");
        } else {
            $resSesi = $resSesi->getRowArray();
            $email = $resSesi['email'];
            $admin = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$email}'")->getRowArray();
            if($admin['read_only'] == 1) {
                $this->throwResponse([
                    'catchException' => true,
                    'message' => 'Anda tidak mempunyai akses untuk mengubah data pada server',
                ]);
            } elseif($admin['email_verified'] == 0) {
                die("Akses anda ditolak");
            } else {
                $this->tambahkanRiwayatAktifitasAdmin($sesi, "Melakukan edit faktur id: {$idTransaksi}");
                $resFaktur = $this->sqlQuery("SELECT * FROM transaksi WHERE id = '{$idTransaksi}'");
                if($resFaktur->getNumRows() < 1) {
                    $this->tambahkanRiwayatAktifitasAdmin($sesi, "Edit faktur ditolak karena id tidak ditemukan");
                    $this->throwResponse([
                        'catchException' => true,
                        'message' => "Faktur dengan id: #{$idTransaksi} tidak ditemukan",
                    ]);
                } else {
                    if(is_numeric($nominal) == false){
                        $this->tambahkanRiwayatAktifitasAdmin($sesi, "Edit faktur ditolak karena mengisi nominal bukan hanya angka");
                        $this->throwResponse([
                            'catchException' => true,
                            'message' => 'Silahkan isi nominal hanya angka'
                        ]);
                        die;
                    } else {
                        $faktur = $resFaktur->getRowArray();
                        $sqlBarang = $this->sqlQuery("SELECT * FROM barang WHERE nama_barang = '{$barang}'");
                        if($sqlBarang->getNumRows() < 1) {
                            $this->tambahkanRiwayatAktifitasAdmin($sesi, "Edit faktur ditolak karena barang tidak ditemukan");
                            $this->throwResponse([
                                'catchException' => true,
                                'message' => 'Barang tidak ditemukan'
                            ]);
                        } else {
                            $namaUpdater = $admin['name'];
                            $sql = $this->sqlQuery("UPDATE transaksi SET nama = '{$nama}', nominal = {$nominal}, barang = '{$barang}', ditambahkan_oleh = '{$namaUpdater}' WHERE id = '{$idTransaksi}'");
                            if($sql) {
                                $this->tambahkanRiwayatAktifitasAdmin($sesi, "Berhasil melakukan edit terhadap transaksi dengan id: #{$idTransaksi}");
                                $this->throwResponse([
                                    'catchException' => false,
                                    'message' => 'Transaksi dengan id: #' . $idTransaksi . ' berhasil diubah'
                                ]);
                            } else {
                                $this->tambahkanRiwayatAktifitasAdmin($sesi, "Terjadi kesalahan saat melakukan perubahan terhadap transaksi dengan id: #{$idTransaksi}");
                                $this->throwResponse([
                                    'catchException' => true,
                                    'message' => "Terjadi kesalahan saat melakukan perubahan terhadap transaksi dengan id: #{$idTransaksi}"
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    public function validasiIdFaktur(string $id = "") {
        $res = $this->sqlQuery("SELECT * FROM transaksi WHERE id = '{$id}'");
        if($res->getNumRows() < 1) {
            $this->throwResponse([
                'catchException' => true,
                'message' => 'ID Transaksi tidak ditemukan'
            ]);
        } else {
            $res = $res->getRowArray();
            $this->throwResponse([
                'catchException' => false,
                'message' => 'ID Transaksi ditemukan',
                'nama' => $res['nama'],
                'nominal' => $res['nominal'],
                'barang' => $res['barang']
            ]);
        }
    }

    public function verifikasiAkun(string $token)
    {
        $resultToken = $this->sqlQuery("SELECT * FROM token_verifikasi WHERE token = '{$token}'");
        $headers = $this
        ->request
        ->headers();
        array_walk($headers, function (&$value, $key)
        {
            $value = $value->getValue();
        });
        if ($resultToken->getNumRows() < 1)
        {
            $msg = "Token verifikasi yang anda masukkan tidak valid";
            $c = true;
        }
        else
        {
            $resultToken = $resultToken->getRowArray();
            $email = $resultToken['email'];
            $resultAkun = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$email}'");
            if ($resultAkun->getNumRows() < 1)
            {
                $msg = "Akun dengan email {$email} tidak ditemukan";
                $c = true;
            }
            else
            {
                $akun = $resultAkun->getRowArray();
                $password = $this->randomString(8);
                $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
                $akunID = $akun['id'];
                $this->sqlQuery("UPDATE akun SET password = '{$passwordHashed}', read_only = 0, email_verified = 1 WHERE id = {$akunID}");
                $this->sqlQuery("DELETE FROM token_verifikasi WHERE token = '{$token}'");
                $akun = $this->sqlQuery("SELECT * FROM akun WHERE id = {$akunID}")->getRowArray();
                $c = false;
                $msg = "Verifikasi sukses";
                $account = ['accountDetails' => ['userName' => $akun['name'], 'password' => $password, 'email' => $akun['email'], 'readOnly' => intval($akun['read_only']) , 'emailVerified' => intval($akun['email_verified']) , 'upLink' => $akun['up_link']]];
            }
        }
        $request = service('request');
        $res = ['catchException' => $c, 'message' => $msg, 'IP' => $this
        ->request
        ->getIPAddress() , 'userAgent' => $this->getUserAgent() , 'requestUriPath' => $request->getUri()
        ->getPath() , 'headers' => $headers];
        $res = [$this->appName => $res];
        if (isset($account))
        {
            array_push($res, $account);
        }
        header('Content-Type: text/html');
        echo '<html><head><style>body { background: #2E2946; } pre { color: white; font-size: 17px; }</style></head><body>'; $json = json_encode($res); echo "<pre id='json'>{$json}</pre>"; echo "<script>(function() { var element = document.getElementById('json'); var obj = JSON.parse(element.innerText); element.innerHTML = JSON.stringify(obj, undefined, 2); })();</script>"; echo '</body></html>';
    // $this->throwResponse($res);
    }

    private function sqlQuery(string $sql)
    {
        return \Config\Database::connect()->query($sql);
    }

    private function throwResponse($array)
    {
        header('Content-Type: application/json');
        echo json_encode($array);
        die;
    }

    public function logout(string $sesi)
    {
        $dataSesi = $this->sqlQuery("SELECT * FROM sesi_login WHERE sesi = '{$sesi}'");
        if ($dataSesi->getNumRows() < 1)
        {
            $this->throwResponse(['catchException' => true, 'message' => 'Sesi login tidak ditemukan']);
        }
        else
        {
            $this->tambahkanRiwayatAktifitasAdmin($sesi, "Melakukan logout");
            $this->sqlQuery("DELETE FROM sesi_login WHERE sesi = '{$sesi}'");
            $this->throwResponse(['catchException' => false, 'message' => 'Logout berhasil']);
        }
    }

    public function login(string $email, string $password)
    {
        $email = trim($email);
        $password = trim($password);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->throwResponse(['catchException' => true, 'message' => "Email yang anda masukkan tidak valid"]);
        }
        elseif (strlen($password) < 7)
        {
            $this->throwResponse(['catchException' => true, 'message' => "Masukkan password lebih dari 7 karakter"]);
        }
        elseif (strlen($password) > 100)
        {
            $this->throwResponse(['catchException' => true, 'message' => "Masukkan password kurang dari 100 karakter"]);
        }
        $resultEmail = $this->sqlQuery("SELECT * FROM akun WHERE email = '{$email}' ORDER BY id DESC LIMIT 1");
        $result = $resultEmail->getRowArray();
        if ($resultEmail->getNumRows() < 1)
        {
            $this->throwResponse(['catchException' => true, 'message' => "Email {$email} tidak terdaftar"]);
        }
        elseif ($result['email_verified'] == 0)
        {
            $this->throwResponse(['catchException' => true, 'message' => 'Email yang anda masukkan belum terverifikasi']);
        }
        elseif (!password_verify($password, $result['password']))
        {
            $this->throwResponse(['catchException' => true, 'message' => 'Password yang anda masukkan salah']);
        }
        else
        {
        // Generate session
            $session = $this->randomString(30);
            $resultSession = $this->sqlQuery("SELECT * FROM sesi_login WHERE email = '{$email}'");
            if ($resultSession->getNumRows() > 0)
            {
                $this->sqlQuery("DELETE FROM sesi_login WHERE email = '{$email}'");
            }
            $this->sqlQuery("INSERT INTO sesi_login VALUES(null, '{$email}', '{$session}')");
            $this->tambahkanRiwayatAktifitasAdmin($session, "Berhasil melakukan login");
            $this->throwResponse(['catchException' => false, 'message' => 'login successfully', 'auth_session' => $session, 'id' => $result['id'], 'email' => $result['email'], 'read_only' => $result['read_only'], 'email_verified' => $result['email_verified'], 'name' => $result['name']]);
        }
    }
}

