// Import library apapun yang dibutuhkan
'use strict';
const settings = require('electron-settings');
const { remote, app, BrowserWindow, ipcMain, webContents } = require('electron');
const path = require('path');
const ipc = ipcMain;
const Client = require('node-rest-client').Client;
const client = new Client();
const fs = require('fs');
const pdf = require('html-pdf');
const log = require('electron-log');
const { Certificate } = require('crypto');
// Inisialisasi form agar bisa diakses oleh semua method
let mainWindow;

// General variable
const apiHost = 'https://mividy.tobelsoft.my.id/api/';
let emailResetPassword = '';
let codeResetPassword = '';
let sesiLogin;

// Buat form dengan propertinya
function createWindow() {
  try {
    createLog('Memulai aplikasi')
    createLog('Inisialisasi aplikasi');
    mainWindow = new BrowserWindow({
      width: 1100,
      height: 600,
      frame: false,
      minWidth: 1100,
      minHeight: 600,
      webPreferences: {
        preload: path.join(__dirname, 'preload.js'),
        nodeIntegration: true,
        contextIsolation: false,
        devTools: false
      }
    });
    // File yang dimuat pertama kali form diload
    createLog('Load file yang dibutuhkan');
    mainWindow.loadFile('page/app.html');
  } catch (ex) {
    createLog(`Terjadi kesalahan saat memulai aplikasi: ${ex.message}`, true);
  }
}

function showNotification(text = '') {
  mainWindow.webContents.send('message', text);
}

app.whenReady().then(() => {
  // Jika aplikasi sudah siap maka
  createWindow();
  app.on('activate', function () {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
  mainWindow.maximize();
  autoLogin();
});

app.on('window-all-closed', function () {
  // Jika semua form ditutup maka
  createLog('Semua window ditutup');
  if (process.platform !== 'darwin') {
    // Jika os bukan macos / ios maka
    app.quit();
  }
});

// Aktifkan otomatis reload setiap terjadi perubahan
// require('electron-reload')(__dirname, {
//   electron: path.join(__dirname, 'node_modules', '.bin', 'electron')
// });
// Comment kode diatas ketika dibuild

// Seamless control
ipc.on('minimize', () => {
  mainWindow.minimize();
});

ipc.on('change-state', () => {
  if (mainWindow.isMaximized()) {
    mainWindow.unmaximize();
  } else {
    mainWindow.maximize();
  }
});

ipc.on('close-app', () => {
  app.quit();
});

ipc.on('set-auth-email', (e, val) => {
  try {
    settings.set('auth-email', val);
    createLog('Setting email authentikasi');
  } catch (ex) {
    createLog(`Terjadi kesalahan saat setting email authentikasi: ${ex.message}`, true);
  }
});

ipc.on('get-auth-email', function () {
  try {
    let data = '';
    data = settings.getSync('auth-email');
    mainWindow.webContents.send('auth-email-data', data);
    createLog('Membaca email authentikasi');
  } catch (ex) {
    createLog(`Terjadi kesalahan saat mengambil email authentikasi: ${ex.message}`, true);
  }
});

ipc.on('reset-password-step1-data', (e, email) => {
  try {
    createLog('Melakukan reset password [step 1]');
    if (email.trim().length === 0) {
      mainWindow.webContents.send('reset-password-step1-success');
      createLog('Reset password langkah pertama ditolak karena ada form yang tidak diisi');
    } else {
      client.get(`${apiHost}reset-password/${email}`, (data, response) => {
        if (data['catchException'] === true) {
          showNotification(data['message']);
          createLog(`Server mengembalikan nilai error: ${data['message']}`, true);
        } else {
          createLog(`Reset password langkah pertama berhasil, melanjutkan ke langkah kedua`);
          emailResetPassword = email;
          mainWindow.webContents.send('continue-to-reset-password-step2');
        }
        mainWindow.webContents.send('reset-password-step1-success');
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan reset password langkah pertama: ${ex.message}`, true);
  }
});

ipc.on('submit-reset-password-step-2', (e, kode) => {
  try {
    createLog('Melakukan reset password langkah kedua');
    if (kode.length < 1) {
      createLog('Reset password langkah kedua ditolak karena ada form yang tidak diisi');
      mainWindow.webContents.send('submit-step2-success', null);
    } else {
      client.get(`${apiHost}reset-password/${emailResetPassword}?kode=${kode.trim()}`, (data, response) => {
        console.log(data);
        if (data['catchException'] === true) {
          showNotification(data['message']);
          createLog(`Reset password langkah kedua ditolak karena server tidak mengembalikan nilai error: ${data['message']}`, true);
        } else {
          mainWindow.webContents.send('reset-password-step2-successfully', null);
          mainWindow.webContents.send('continue-to-reset-password-step3');
          createLog(`Reset password langkah kedua berhasil`);
          codeResetPassword = kode;
        }
        mainWindow.webContents.send('submit-step2-success', null);
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan reset password langkah kedua: ${ex.message}`, true);
  }
});

ipc.on('process-reset-password', (e, data) => {
  try {
    let password = Object.values(data)[0];
    let konfirmasiPassword = Object.values(data)[1];
    if (password != konfirmasiPassword) {
      showNotification('Password yang anda masukkan tidak cocok');
      mainWindow.webContents.send('reset-password-step3-complete');
      createLog('Reset password langkah ketiga ditolak karena password baru dengan konfirmasi password baru tidak cocok');
    } else {
      client.get(`${apiHost}/reset-password/${emailResetPassword}?kode=${codeResetPassword}&newPassword=${password.trim()}`, (data, response) => {
        if (data['catchException'] === true) {
          showNotification(data['message']);
          createLog(`Reset password langkah ketiga ditolak karena server mengembalikan nilai error: ${data['message']}`, true);
        } else {
          mainWindow.webContents.send('reset-password-complete&show-regards');
          createLog('Reset password berhasil');
        }
        mainWindow.webContents.send('reset-password-step3-complete');
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan reset password langkah ketiga: ${ex.message}`, true);
  }
});

ipc.on('login-submit', (e, data) => {
  try {
    createLog(`Melakukan login`);
    let email = Object.values(data)[0];
    let password = Object.values(data)[1];

    if (email.length < 1 && email != undefined) {
      mainWindow.webContents.send('login-complete', null);
      createLog(`Proses login ditolak karena email tidak diisi atau tidak terdefinisi`, true);
      showNotification('Silahkan masukkan email setidaknya 1 karakter');
    } else if (password.length < 1 && password != undefined) {
      createLog(`Proses login ditolak karena password tidak diisi atau tidak terdefinisi`, true);
      showNotification('Silahkan masukkan password setidaknya 1 karakter');
      mainWindow.webContents.send('login-complete', null);
    } else {
      client.get(`${apiHost}login/${email}/${password}`, (data, raw) => {
        if (data['catchException'] === true && data != undefined) {
          showNotification(data['message']);
          createLog(`Proses login ditolak karena server mengembalikan nilai error: ${data['message']}`, true);
        } else {
          createLog('Berhasil melakukan login');
          settings.setSync('admin-login', data);
          mainWindow.webContents.send('is-logged-in', settings.getSync('admin-login'));
          sesiLogin = Object.values(settings.getSync('admin-login'))[2];
        }
        mainWindow.webContents.send('login-complete', null);
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan login: ${ex.message}`, true);
  }
});

function autoLogin() {
  try {
    createLog(`Menjalankan fitur auto login`);
    let data = settings.getSync('admin-login');
    if (data != undefined) {
      let sesi = Object.values(data)[2];
      client.get(`${apiHost}validate_session/${sesi}`, (data, raw) => {
        createLog('Terdapat sesi pada komputer, dan melakukan validasi sesi pada sisi server');
        if (data['catchException'] === false && data['message'] == 'Session is valid') {
          createLog('Sesi valid dan belum expired, auto login berhasil dilakukan');
          mainWindow.webContents.on('did-finish-load', () => {
            mainWindow.webContents.send('is-logged-in', settings.getSync('admin-login'));
            sesiLogin = Object.values(settings.getSync('admin-login'))[2];
          });
        } else {
          createLog('Sesi login tidak valid atau expired', true);
          settings.unsetSync('admin-login');
        }
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat menjalankan fitur auto login: ${ex.message}`, true);
    mainWindow.webContents.on('did-finish-load', () => {
      showNotification('Gagal menjalankan fitur auto login');
    });
  }
}

ipc.on('logout', () => {
  try {
    createLog(`Melakukan logout`);
    client.get(`${apiHost}logout/${sesiLogin}`, () => {
      settings.unsetSync('admin-login');
      app.relaunch();
      createLog('Logout berhasil');
      app.exit();
    });
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan logout: ${ex.message}`, true);
  }
});

ipc.on('get-minimal-dashboard', () => {
  try {
    createLog('Membaca data dashboard');
    client.get(`${apiHost}minimal-dashboard/${sesiLogin}`, (data, raw) => {
      mainWindow.webContents.send('data-minimal-dashboard', data);
      createLog(`Berhasil membaca data dashboard`);
    });
  } catch (ex) {
    createLog(`Terjadi kesalahan saat mengambil data pada dashboard: ${ex.message}`, true);
  }
});

ipc.on('get-profile', () => {
  createLog(`Membaca info profil`);
  try {
    mainWindow.webContents.send('data-profile', settings.getSync('admin-login'));
    createLog(`Berhasil membaca info profil`);
  } catch (ex) {
    createLog(`Terjadi kesalahan saat mambaca info profil: ${ex.message}`, true);
  }
});

ipc.on('get-data-transaksi', (e, q) => {
  try {
    createLog(`Membaca data transaksi`);
    client.get(`${apiHost}ambil_semua_transaksi/${sesiLogin}/${q}`, (data, raw) => {
      mainWindow.webContents.send('data-transaksi', data);
      createLog(`Berhasil membaca data transaksi`);
    });
  } catch (ex) {
    createLog(`Terjadi kesalahan saat membaca data transaksi: ${ex.message}`, true);
  }
});

ipc.on('change-refresh-otomatis', (e, val) => {
  try {
    createLog(`Mengganti pengaturan refresh transaksi otomatis menjadi ${val.toString()}`);
    settings.setSync('refresh-otomatis', val);
  } catch (ex) {
    createLog(`Terjadi kesalahan saat mengganti pengaturan refresh transaksi otomatis menjadi: ${val.toString()} karena: ${ex.message}`, true);
  }
});

ipc.on('get-refresh-otomatis', () => {
  mainWindow.webContents.send('refresh-otomatis', settings.getSync('refresh-otomatis'));
});

ipc.on('delete-transaksi', (e, id) => {
  try {
    createLog(`Menghapus transaksi id #${id.toString()}`);
    client.get(`${apiHost}hapus_transaksi/${sesiLogin}/${id}`, (data, raw) => {
      if (data['catchException'] === true) {
        createLog(`Terjadi kesalahan saat menghapus data transaksi karena server mengembalikan nilai error: ${data['message']}`, true);
      } else {
        createLog(`Berhasil menghapus data transaksi`);
      }
      showNotification(data['message']);
      mainWindow.webContents.send('delete-transaksi-berhasil');
    });
  } catch (ex) {
    createLog(`Terjadi kesalahan saat menghapus data transaksi: ${ex.message}`, true);
  }
});

ipc.on('get-barang-transaksi', () => {
  try {
    createLog(`Membaca data transaksi`);
    client.get(`${apiHost}/ambil_semua_barang/${sesiLogin}`, (data, raw) => {
      mainWindow.webContents.send('data-barang', data);
    });
  } catch (ex) {
    createLog(`Terjadi kesalahan saat membaca data transaksi: ${ex.message}`, true);
  }
});

ipc.on('tambah-transaksi', (e, data) => {
  try {
    createLog('Menambahkan data transaksi');
    let pembeli = data['pembeli'];
    let nominal = data['nominal'];
    let barang = data['barang'];
    if (pembeli.length < 1 && pembeli != undefined) {
      createLog(`Penambahan data transaksi ditolak karena form pembeli belum diisi atau tidak terdefinisi`, true);
      showNotification('Silahkan isi pembeli setidaknya 1 karakter');
      mainWindow.webContents.send('tambah-transaksi-complete');
    } else if (nominal.length < 1) {
      createLog(`Penambahan data transaksi ditolak karena form nominal belum diisi atau tidak terdefinisi`, true);
      showNotification('Silahkan isi nominal setidaknya 1 karakter');
      mainWindow.webContents.send('tambah-transaksi-complete');
    } else if (barang.length < 1) {
      createLog(`Penambahan data transaksi ditolak karena form barang belum diisi atau tidak terdefinisi`, true);
      showNotification('Silakan pilih barang terlebih dahulu');
      mainWindow.webContents.send('tambah-transaksi-complete');
    } else {
      client.get(`${apiHost}tambahkan_data_transaksi/${sesiLogin}/${pembeli}/${nominal}/${barang}`, (data, raw) => {
        if (data['catchException'] === true) {
          showNotification(data['message']);
          createLog(`Terjadi kesalahan saat menambahkan data transaksi karena server mengembalikan nilai error: ${data['message']}`, true);
        } else {
          createLog(`Berhasil menambahkan data transaksi`);
          showNotification(data['message']);
          require('electron').shell.openExternal(data['urlFaktur']);
        }
        mainWindow.webContents.send('tambah-transaksi-complete');
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat menambah data transaksi: ${ex.message}`, true);
  }
});

ipc.on('cek-data-transaksi', (e, id) => {
  try {
    createLog(`Melakukan cek data transaksi`);
    client.get(`${apiHost}validasi-id-faktur/${id.trim()}`, (data, raw) => {
      if (data != undefined) {
        if (data['catchException'] === true) {
          mainWindow.webContents.send('data-id-transaksi-edit-tidak-ditemukan');
          createLog(`Terjadi kesalahan saat melakukan pengecekan data transaksi karena server mengembalikan nilai error: ${data['message']}`, true);
        } else {
          createLog(`Data transaksi ditemukan`);
          mainWindow.webContents.send('data-id-transaksi-edit-ditemukan', data);
        }
      }
    });
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan cek data transaksi: ${ex.message}`, true);
  }
});

ipc.on('edit-data-transaksi', (e, dataTransaksi) => {
  try {
    createLog(`Melakukan edit data transaksi`);
    let id = dataTransaksi['id'];
    let pembeli = dataTransaksi['pembeli'];
    let nominal = dataTransaksi['nominal'];
    let barang = dataTransaksi['barang'];

    if (id.length < 1 || pembeli.length < 1 || nominal.length < 1 || barang.length < 1) {
      showNotification('Data yang anda masukkan tidak valid');
      mainWindow.webContents.send('edit-data-transaksi-complete');
      createLog(`Edit data transaksi ditolak karena ada data yang belum diisi atau ada data yang tidak terdefinisi`, true);
    } else {
      client.get(`${apiHost}validasi-id-faktur/${id.trim()}`, (data, raw) => {
        if (data['catchException'] === true) {
          showNotification(data['message']);
          mainWindow.webContents.send('edit-data-transaksi-complete');
          createLog(`Terjadi kesalahan saat melakukan edit data transaksi karena server mengembalikan nilai tidak valid: ${data['message']}`, true);
        } else {
          client.get(`${apiHost}edit-faktur/${sesiLogin}/${id}/${pembeli}/${nominal}/${barang}`, (data, raw) => {
            showNotification(data['message']);
            if (data['catchException'] === true) {
              createLog(`Transaksi #${id} berhasil diedit`);
            } else {
              createLog(`Terjadi kesalahan saat melakukan edit data transaksi karena server mengembalikan nilai error: ${data['message']}`, true);
            }
            mainWindow.webContents.send('edit-data-transaksi-complete');
          });
        }
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan edit data transaksi: ${ex.message}`, true);
  }
});

ipc.on('tambahkan-data-admin', (e, data) => {
  try {
    createLog(`Menambahkan admin`);
    let nama = data['nama'].trim();
    let email = data['email'].trim();
    if (nama.length < 1) {
      showNotification('Silahkan isi nama setidaknya 1 karakter');
      mainWindow.webContents.send('tambahkan-admin-complete');
      createLog(`Penambahan admin ditolak karena data nama tidak diisi atau tidak terdefinisi`, true);
    } else if (email.length < 1) {
      showNotification('Silahkan isi email setidaknya 1 karakter');
      mainWindow.webContents.send('tambahkan-admin-complete');
      createLog(`Penambahan admin ditolak karena data email tidak diisi atau tidak terdefinisi`, true);
    } else {
      client.get(`${apiHost}tambahkan_admin/${sesiLogin}/${nama}/${email}`, (data, raw) => {
        if (data['catchException'] === true && data != undefined) {
          showNotification(data['message']);
          mainWindow.webContents.send('tambahkan-admin-complete');
          createLog(`Berhasil menambahkan admin baru`);
        } else {
          mainWindow.webContents.send('tambahkan-admin-complete');
          mainWindow.webContents.send('alert', data['message']);
          createLog(`Terjadi kesalahan saat menambahkan admin karena server mengembalikan nilai error: ${data['message']}`, true);
        }
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat menambahkan admin: ${ex.message}`, true);
  }
});

ipc.on('get-riwayat-admin', () => {
  try {
    createLog(`Membaca riwayat admin pada sisi server`);
    client.get(`${apiHost}/logadmin/${sesiLogin}`, (data, raw) => {
      mainWindow.webContents.send('data-riwayat-admin', data.toString());
      createLog(`Berhasil membaca riwayat admin pada sisi server`);
    });
  } catch (ex) {
    createLog(`Terjadi kesalahan saat membaca riwayat admin pada sisi server: ${ex.message}`, true);
  }
});

ipc.on('open-invoice-in-browser', (e, id) => {
  try {
    createLog(`Membuka faktur secara online melalui browser default`);
    if (id.length < 1) {
      showNotification('Silahkan isi id transaksi setidaknya 1 karakter');
    } else {
      client.get(`${apiHost}validasi-id-faktur/${id}`, (data, raw) => {
        if (data['catchException'] === true) {
          showNotification(data['message']);
          createLog(`Terjadi kesalahan saat melakukan validasi id faktur karena server mengembalikan nilai error: ${data['message']}`, true);
        } else {
          let url = `${apiHost.replace('/api', '')}faktur/${id}.html`;
          require('electron').shell.openExternal(`${url}`);
          createLog(`Berhasil membuka faktur secara online melalui browser default`);
        }
        console.log(data);
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat membuka faktur secara online melalui browser default: ${ex.message}`, true);
  }
});

ipc.on('download-faktur', (e, id) => {
  try {
    createLog(`Melakukan download faktur sebagai dokumen pdf`);
    if (id.length < 1) {
      showNotification('Silahkan isi id transaksi setidaknya 1 karakter');
      mainWindow.webContents.send('download-faktur-complete');
      createLog(`Download faktur ditolak karena id belum diisi atau id tidak terdefinisi`, true);
    } else {
      client.get(`${apiHost}validasi-id-faktur/${id}`, (data, raw) => {
        if (data['catchException'] === true) {
          showNotification(data['message']);
          mainWindow.webContents.send('download-faktur-complete');
          createLog(`Terjadi kesalahan saat melakukan download faktur sebagai dokumen pdf karena server mengembalikan nilai error: ${data['message']}`, true);
        } else {
          let url = `${apiHost.replace('/api', '')}faktur/${id}.html`;
          client.get(url, (data, raw) => {
            let html = data.toString();
            var options = { format: 'Letter' };

            let pathCompany = 'C:/SMKM 6 Donomulyo/';
            let pathAppName = pathCompany + 'Mividy/';
            let pathInvoice = pathAppName + 'Invoice/';
            if (!fs.existsSync(pathCompany)) {
              fs.mkdirSync(pathCompany);
            }
            if (!fs.existsSync(pathAppName)) {
              fs.mkdirSync(pathAppName);
            }
            if (!fs.existsSync(pathInvoice)) {
              fs.mkdirSync(pathInvoice);
            }

            pdf.create(html, options).toFile(`${pathInvoice}${id}.pdf`, function (err, res) {
              if (err) {
                showNotification(err);
                mainWindow.webContents.send('download-faktur-complete');
                createLog(`Terjadi kesalahan saat melakukan download faktur: ${err}`, true);
              } else {
                createLog(`Faktur berhasil didownload`);
                showNotification('Faktur / invoice berhasil didownload');
                mainWindow.webContents.send('download-faktur-complete');
              }
            });
          });
        }
      });
    }
  } catch (ex) {
    createLog(`Terjadi kesalahan saat melakukan download faktur: ${ex.message}`, true);
  }
});

ipc.on('print-invoice', () => {
  printPdf();
});

function createLog(message = '', error = false) {
  let pathCompany = 'C:/SMKM 6 Donomulyo/';
  let pathAppName = pathCompany + 'Mividy/';
  let fileName = pathAppName + 'Log.ini';
  if (!fs.existsSync(pathCompany)) {
    fs.mkdirSync(pathCompany);
  }
  if (!fs.existsSync(pathAppName)) {
    fs.mkdirSync(pathAppName);
  }
  if (!fs.existsSync(fileName)) {
    fs.writeFileSync(fileName, '', 'utf-8');
  }

  // log.transports.file.level = (error === false) ? 'Info' : 'Error';
  log.transports.file.file = fileName;
  log.info(message);
}

function printPdf(id = 0) {
  console.log(mainWindow.webContents.getPrinters());
  const options = {
    preview: false,               // Preview in window or print
    width: '170px',               //  width of content body
    margin: '0 0 0 0',            // margin of content body
    copies: 1,                    // Number of copies to print
    printerName: 'XP-80C',        // printerName: string, check with webContent.getPrinters()
    timeOutPerLine: 400,
    pageSize: { height: 301000, width: 71000 }  // page size
  }

  const data = [
    {
      type: 'text',                                       // 'text' | 'barCode' | 'qrCode' | 'image' | 'table
      value: 'SAMPLE HEADING',
    }
  ];
  // const data = [
  //   {
  //     type: 'text',                                       // 'text' | 'barCode' | 'qrCode' | 'image' | 'table
  //     value: 'SAMPLE HEADING',
  //     style: `text-align:center;`,
  //     css: { "font-weight": "700", "font-size": "18px" }
  //   }
  // ];

  PosPrinter.print(data, options)
    .then(() => { })
    .catch((error) => {
      showNotification(error.message);
      console.error(error);
    });
}