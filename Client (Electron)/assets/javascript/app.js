const { ipcRenderer } = require('electron');
const { parse } = require('path');
const ipc = ipcRenderer;
const appName = "Mividy";
const timer = ms => new Promise(res => setTimeout(res, ms));
// Terapkan nama aplikasi
document.querySelectorAll('[app-name]').forEach((el) => {
    el.innerText = appName;
});

// General variable
let dataLogin;

// Variable proses
let resetPasswordStep1Processing = false;
let resetPasswordStep2Processing = false;
let resetPasswordStep3Processing = false;
let loginProcessing = false;
let refreshOtomatis = false;
let tambahTransaksiProcessing = false;
let editTransaksiProcessing = false;
let tambahkanAdminProcessing = false;
let downloadFakturProcessing = false;

let authEmailChecked = false; // secara default tidak dichecklist

// Seamless control
const minimizeBtn = document.querySelector('.head .right [minimize]');
const changeStateBtn = document.querySelector('.head .right [change-state]');
const closeBtn = document.querySelector('.head .right [close]');
minimizeBtn.addEventListener('click', (e) => {
    e.preventDefault();
    ipc.send('minimize');
});
changeStateBtn.addEventListener('click', (e) => {
    e.preventDefault();
    ipc.send('change-state');
});
closeBtn.addEventListener('click', (e) => {
    e.preventDefault();
    ipc.send('close-app');
});

window.onload = () => {
    document.querySelectorAll('img').forEach((el) => {
        el.setAttribute('draggable', 'false');
    });
    loadingTextAnimation();
    loadingProgressAnimation();
}

async function loadingProgressAnimation() {
    const el = document.querySelector('.loading .load-foot .progress .bar');
    for (let i = 0; i < 102; i++) {
        if (i < 50) {
            el.setAttribute('style', `width: ${i.toString()}% !important;`);
            await timer(50);
        } else if (i < 75) {
            el.setAttribute('style', `width: ${i.toString()}% !important;`);
            await timer(100);
        } else if (i < 90) {
            el.setAttribute('style', `width: ${i.toString()}% !important;`);
            await timer(50);
        } else {
            el.setAttribute('style', `width: ${i.toString()}% !important;`);
            await timer(0);
        }
        // Jika progress bar sudah selesai maka
        if (i === 101) {
            loadingComplete();
        }
    }
}

function loadingTextAnimation() {
    let loadingText = 'Please stand by ';
    let t = '';
    const jumlahTitik = 5;
    let titik = 0;
    const speed = 100;
    const el = document.querySelector('.loading .load-foot .text');
    setInterval(() => {
        t = '';
        (titik > jumlahTitik) ? titik = 0 : titik++;
        for (let i = 0; i < titik; i++) {
            t += '.';
        }
        el.innerText = `${loadingText}${t}`;
    }, speed);
}

function loadingComplete() {
    if (dataLogin == undefined) {
        setTimeout(() => {
            const halamanLoading = document.querySelector('.loading[halaman]');
            const halamanLogin = document.querySelector('.login[halaman]');
            halamanLoading.setAttribute('style', 'display: none !important;');
            halamanLogin.setAttribute('style', 'display: block !important');
        }, 1000);
    }
}

const loginTogglePassword = document.querySelector('.login .middle .input-merged #login-show-hide');
const loginPassword = document.querySelector('.login .input-merged:last-child input');

loginTogglePassword.addEventListener('click', (e) => {
    e.preventDefault();
    if (loginPassword.getAttribute('type') == 'password') {
        loginPassword.setAttribute('type', 'text');
        loginTogglePassword.innerHTML = "<i class='bx bxs-hide'></i>";
        loginPassword.setAttribute('title', 'Show password');
    } else {
        loginPassword.setAttribute('type', 'password');
        loginTogglePassword.innerHTML = "<i class='bx bx-show-alt'></i>";
        loginTogglePassword.setAttribute('title', 'Hide password');
    }
});

// Aktifkan tooltip
function activateTooltip() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}
activateTooltip();

// Login save password toggle
const saveEmailToggle = document.querySelector('.login .foot .save-email #ingat-email');
saveEmailToggle.addEventListener('click', () => {
    if (authEmailChecked == true) {
        authEmailChecked = false;
        ipc.send('set-auth-email', {
            save: false,
            email: ''
        });
    } else {
        authEmailChecked = true;
        ipc.send('set-auth-email', {
            save: true,
            email: document.querySelector('#login-email').value
        });
    }
});


// Simpan email ketika save email diaktifkan dan setiap kali tombol email ditekan
document.querySelector('#login-email').addEventListener('keyup', () => {
    ipc.send('set-auth-email', {
        save: authEmailChecked,
        email: document.querySelector('#login-email').value
    });
});

// Ketika aplikasi dimulai cek apakah email disave dan apa emailnya
ipc.send('get-auth-email');
ipc.on('auth-email-data', function (e, data) {
    let d = Object.values(data);
    let isSave = d[0];
    let email = d[1];
    if (isSave != undefined && isSave == true) {
        authEmailChecked = true;
        saveEmailToggle.setAttribute('checked', 'true');
        document.querySelector('#login-email').value = email;
    } else {
        authEmailChecked = false;
    }
});

function hideAllPage() {
    document.querySelectorAll('[halaman]').forEach((element) => {
        element.setAttribute('style', 'display: none !important');
    });
}

document.querySelector('button[id="login-forgot-password"]').addEventListener('click', (e) => {
    hideAllPage();
    document.querySelector('.reset-password-step1[halaman]').setAttribute('style', 'display: block !important;');
});

document.querySelectorAll('button[goto-login]').forEach((element) => {
    element.addEventListener('click', (e) => {
        e.preventDefault();
        hideAllPage();
        document.querySelector('.login[halaman]').setAttribute('style', 'display: block !important;');
    });
});

ipc.on('message', (e, msg) => {
    showNotification(msg);
});

const btnResetPasswordStep1Submit = document.querySelector('button#btn-reset-password-step-1');
btnResetPasswordStep1Submit.addEventListener('click', (e) => {
    e.preventDefault();
    resetPasswordStep1();
});

document.querySelector('#reset-password-email[step1]').addEventListener('keydown', (e) => {
    if (e.keyCode === 13) {
        resetPasswordStep1();
    }
});

function resetPasswordStep1() {
    if (resetPasswordStep1Processing === false && resetPasswordStep1Processing != undefined) {
        resetPasswordStep1Processing = true;
        btnResetPasswordStep1Submit.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        let email = document.querySelector('#reset-password-email[step1]').value.trim();
        ipc.send('reset-password-step1-data', email);
    }
}

ipc.on('reset-password-step1-success', () => {
    resetPasswordStep1Processing = false;
    btnResetPasswordStep1Submit.innerHTML = `<i class='bx bx-right-arrow-alt'></i><span>Next</span>`;
});


ipc.on('continue-to-reset-password-step2', () => {
    hideAllPage();
    document.querySelector('.reset-password-step2[halaman]').setAttribute('style', 'display: block !important;');
});

const codeResetPassword = document.querySelector('#reset-password-code');
const btnResetpasswordstep2 = document.querySelector('#btn-reset-password-step-2');
codeResetPassword.addEventListener('keydown', (e) => {
    if (codeResetPassword.value.length > 5 && e.keyCode != 8 && e.keyCode != 13) {
        e.preventDefault();
    }
});

codeResetPassword.addEventListener('keyup', (e) => {
    if (e.keyCode === 13) {
        submitResetPasswordStep2();
    }
});

btnResetpasswordstep2.addEventListener('click', (e) => {
    e.preventDefault();
    submitResetPasswordStep2();
});

function submitResetPasswordStep2() {
    if (resetPasswordStep2Processing == false && resetPasswordStep2Processing != undefined) {
        resetPasswordStep2Processing = true;
        btnResetpasswordstep2.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        ipc.send('submit-reset-password-step-2', codeResetPassword.value.trim());
    }
}

ipc.on('submit-step2-success', () => {
    resetPasswordStep2Processing = false;
    btnResetpasswordstep2.innerHTML = `<i class='bx bx-right-arrow-alt'></i><span>Next</span>`;
});

async function showNotification(text = '') {
    document.querySelector('.notification').innerText = text.trim();
    document.querySelector('.notification').classList.add('active');
    setTimeout(() => {
        document.querySelector('.notification').classList.remove('active');
        document.querySelector('.notification').innerHTML = '';
    }, 3000);
}

ipc.on('continue-to-reset-password-step3', () => {
    hideAllPage();
    document.querySelector('.reset-password-step3[halaman]').setAttribute('style', 'display: block !important;');
});

const passwordStep3 = document.querySelector('#password[step3]');
const konfirmasiPasswordStep3 = document.querySelector('#confirm-password[step3]');
const btnResetPasswordStep3 = document.querySelector('#btn-reset-password-step-3');

passwordStep3.addEventListener('keydown', (e) => {
    if (e.keyCode === 13 && e != undefined) {
        e.preventDefault();
        resetPasswordNow();
    }
});

konfirmasiPasswordStep3.addEventListener('keydown', (e) => {
    if (e.keyCode === 13 && e != undefined) {
        e.preventDefault();
        resetPasswordNow();
    }
});

btnResetPasswordStep3.addEventListener('click', (e) => {
    e.preventDefault();
    resetPasswordNow();
});

function resetPasswordNow() {
    if (resetPasswordStep3Processing === false && resetPasswordStep3Processing != undefined) {
        btnResetPasswordStep3.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        resetPasswordStep3Processing = true;
        let data = {
            "password": document.querySelector('#password[step3]').value.trim(),
            "confirm-password": document.querySelector('#confirm-password[step3]').value.trim()
        };
        ipc.send('process-reset-password', data);
    }
}

ipc.on('reset-password-step3-complete', () => {
    resetPasswordStep3Processing = false;
    btnResetPasswordStep3.innerHTML = `<i class='bx bx-lock'></i><span>Reset</span>`;
});

ipc.on('reset-password-complete&show-regards', () => {
    hideAllPage();
    document.querySelector('.regards-reset-password-complete[halaman]').setAttribute('style', 'display: block !important;');
});

const loginEmailSubmit = document.querySelector('#login-email');
const loginPasswordSubmit = document.querySelector('#login-password');
const loginButtonSubmit = document.querySelector('#btn-login');

loginEmailSubmit.addEventListener('keypress', (e) => {
    if (e.keyCode === 13 && e.keyCode != undefined) {
        e.preventDefault();
        login();
    }
});

loginPasswordSubmit.addEventListener('keypress', (e) => {
    if (e.keyCode === 13 && e.keyCode != undefined) {
        e.preventDefault();
        login();
    }
});

loginButtonSubmit.addEventListener('click', (e) => {
    e.preventDefault();
    login();
});

function login() {
    if (loginProcessing === false && loginProcessing != undefined) {
        loginProcessing = true;
        loginButtonSubmit.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        let data = {
            "email": loginEmailSubmit.value.trim(),
            "password": loginPasswordSubmit.value.trim()
        };
        ipc.send('login-submit', data);
    }
}

ipc.on('login-complete', () => {
    loginButtonSubmit.innerHTML = `<i class='bx bxs-log-in-circle'></i><span>Login</span>`;
    loginProcessing = false;
});

ipc.on('is-logged-in', (e, data) => {
    dataLogin = data;
    setInterval(() => {
        document.querySelectorAll('[halaman]:not(.dashboard)').forEach((halaman) => {
            halaman.classList.add('d-none');
        });
    }, 2000);
    document.querySelectorAll('[halaman]:not(.dashboard)').forEach((el) => {
        el.setAttribute('style', 'display: none !important;');
    });
    document.querySelector('[halaman].dashboard').setAttribute('style', 'display: block !important;');
    prepareAfterLogin();
});

document.querySelectorAll('.dashboard .sidebar button[to]').forEach((sideEl) => {
    sideEl.addEventListener('click', () => {
        document.querySelectorAll('.dashboard .sidebar button[to]').forEach((el) => {
            el.classList.remove('active');
        });
        sideEl.classList.add('active');
        movePage(sideEl.getAttribute('to'));
    });
});

document.querySelectorAll('[page]:not(:first-child)').forEach((el) => {
    el.classList.add('d-none');
});

function movePage(to = '') {
    document.querySelectorAll('[page]').forEach((el) => {
        el.classList.add('d-none');
    });
    document.querySelector(`[page="${to}"]`).classList.remove('d-none');
}

btnLogout = document.querySelector('[logout]');
btnLogout.addEventListener('click', () => {
    btnLogout.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
    ipc.send('logout');
});

function prepareAfterLogin() {
    let element = document.querySelector('[regards-after-login]');
    let regardsText = element.innerText.trim();
    element.innerText = regardsText.replace('admin', Object.values(dataLogin)[7]);
    getMinimalDashboard();
    getDataTransaksi();
    ipc.send('get-profile');
    ipc.send('get-barang-transaksi');
    timerRefreshTransaksi();
    getRiwayatAdmin();
    setInterval(() => {
        getMinimalDashboard();
    }, 10000);
    ipc.send('get-refresh-otomatis');
}

function getMinimalDashboard() {
    ipc.send('get-minimal-dashboard');
}

ipc.on('data-minimal-dashboard', (e, data) => {
    let pendapatan = data['data']['pendapatan'];
    let totalTransaksi = data['data']['totalTransaksi'];
    document.querySelector('[dashboard-pendapatan]').innerText = pendapatan;
    document.querySelector('[dashboard-transaksi]').innerText = `${totalTransaksi} x`;
});

// Clock
Number.prototype.pad = function (n) {
    for (var r = this.toString(); r.length < n; r = 0 + r);
    return r;
};

function updateClock() {
    var now = new Date();
    var milli = now.getMilliseconds(),
        sec = now.getSeconds(),
        min = now.getMinutes(),
        hou = now.getHours(),
        mo = now.getMonth(),
        dy = now.getDate(),
        yr = now.getFullYear();
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var tags = ["mon", "d", "y", "h", "m", "s"],
        corr = [months[mo], dy, yr, hou.pad(2), min.pad(2), sec.pad(2)];
    for (var i = 0; i < tags.length; i++)
        document.getElementById(tags[i]).firstChild.nodeValue = corr[i];
}

window.setInterval("updateClock()", 1);


ipc.on('data-profile', (e, data) => {
    let nameElement = document.querySelector('[profile-name]');
    let emailElement = document.querySelector('[profile-email]');
    let sessionElement = document.querySelector('[profile-auth-session]');
    let readOnlyElement = document.querySelector('[profile-read-only]');
    let adminId = document.querySelector('[profile-admin-id]');
    let session = '';
    for (let i = 0; i < 5; i++) {
        session += data['auth_session'].toString()[i];
    }
    emailElement.innerText = data['email'].trim();
    nameElement.innerText = data['name'].trim();
    sessionElement.innerText = `${sessionElement.innerText} ${session}****`;
    readOnlyElement.innerText = `${readOnlyElement.innerText} ${(data['read_only'] == "1")}`
    adminId.innerText = `${adminId.innerText} ${data['id']}`;
});

function getDataTransaksi() {
    ipc.send('get-data-transaksi', document.querySelector('[input-cari-transaksi]').value.trim());
}

document.querySelector('[input-cari-transaksi]').addEventListener('keyup', (e) => {
    if (e.keyCode === 13) {
        getDataTransaksi();
    }
});

document.querySelector('[button-cari-transaksi]').addEventListener('click', () => {
    getDataTransaksi();
});


ipc.on('data-transaksi', (e, data) => {
    let container = document.querySelector('table.transaksi tbody');
    container.innerHTML = '';

    data.forEach((data) => {
        let element = document.createElement('tr');
        element.innerHTML = `<th class="faktur-table-link" onClick="openFakturOnline(${data['id']})" scope="row">${data['id']}</th>
        <td>${data['nama']}</td>
        <td>${data['barang']}</td>
        <td>${data['ditambahkan_oleh']}</td>
        <td>${data['tanggal']}</td>
        <td>${data['nominal']}</td>
        <td class="d-flex justify-content-center">
            <button class="action-kelola" data-bs-toggle="tooltip" data-bs-placement="top"
            title="Kelola faktur" onClick="kelolaFaktur(${data['id']})"><i class='bx bxs-bookmark-alt-minus'></i></button>
            <button data-bs-toggle="tooltip" data-bs-placement="top"
            title="Edit transaksi" class="action-edit" onClick="editTransaksi(${data['id']})"><i class='bx bxs-pencil'></i></button>
            <button data-bs-toggle="tooltip" data-bs-placement="top"
            title="Hapus transaksi" class="action-delete" onClick="hapusTransaksi(${data['id']})"><i class='bx bx-trash'></i></button>
        </td>`;
        container.appendChild(element);
    });
    activateTooltip();
});


let timerTransaksi = 10;
function timerRefreshTransaksi() {
    if (timerTransaksi > 0) {
        setTimeout(() => {
            if (refreshOtomatis === true) {
                document.querySelector('.transaksi-head .left .limit').innerHTML = `<i class='bx bx-timer'></i>Refresh otomatis dalam: ${timerTransaksi}`;
                timerTransaksi--;
                if (timerTransaksi === 0) {
                    timerTransaksi = 10;
                    getDataTransaksi();
                }
            } else {
                document.querySelector('.transaksi-head .left .limit').innerHTML = `<i class='bx bx-timer'></i>Refresh otomatis mati`;
            }
            timerRefreshTransaksi();
        }, 1000);
    }
}

let toggleRefresh = document.querySelector('#toggle-refresh');
toggleRefresh.addEventListener('change', () => {
    if (toggleRefresh.checked) {
        ipc.send('change-refresh-otomatis', true);
        refreshOtomatis = true;
    } else {
        ipc.send('change-refresh-otomatis', false);
        refreshOtomatis = false;
    }
});

ipc.on('refresh-otomatis', (e, val) => {
    if (val === true && val != undefined) {
        toggleRefresh.setAttribute('checked', 'true');
    }
    refreshOtomatis = val;
});


function editTransaksi(id) {
    document.querySelector('button[to="edit-transaksi"]').click();
    document.querySelector('input#id[edit]').value = id;
    if (editIdTransaksi.value.length > 0) {
        ipc.send('cek-data-transaksi', editIdTransaksi.value.trim());
    } else {
        editPembeliTransaksi.setAttribute('disabled', '');
        editPembeliTransaksi.value = '';
        editNominalTransaksi.setAttribute('disabled', '');
        editNominalTransaksi.value = '';
        editBarangTransaksi.setAttribute('disabled', '');
    }
}

function hapusTransaksi(id) {
    if (confirm(`Anda yakin ingin menghapus transaksi dengan id: #${id} ?`)) {
        ipc.send('delete-transaksi', id);
    }
}

ipc.on('delete-transaksi-berhasil', () => {
    getDataTransaksi();
});

ipc.on('data-barang', (e, data) => {
    let container = document.querySelector('select#barang[tambah]');
    let container2 = document.querySelector('select#barang[edit]');
    container.innerHTML = '';
    container2.innerHTML = '';
    data.forEach((data) => {
        let el = document.createElement('option');
        el.setAttribute('value', data['barang']);
        el.innerText = data['barang'];
        container.appendChild(el);
    });
    data.forEach((data) => {
        let el = document.createElement('option');
        el.setAttribute('value', data['barang']);
        el.innerText = data['barang'];
        container2.appendChild(el);
    });
});

let btnTambahTransaksi = document.querySelector('button.tambah-t');
btnTambahTransaksi.addEventListener('click', (e) => {
    e.preventDefault();
    if (tambahTransaksiProcessing === false && tambahTransaksiProcessing != undefined) {
        tambahTransaksiProcessing = true;
        btnTambahTransaksi.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        let data = {
            pembeli: document.querySelector('#nama[tambah]').value.trim(),
            nominal: document.querySelector('#nominal[tambah]').value.trim(),
            barang: document.querySelector('#barang[tambah]').value.trim()
        };
        ipc.send('tambah-transaksi', data);
    }
});

ipc.on('tambah-transaksi-complete', () => {
    tambahTransaksiProcessing = false;
    btnTambahTransaksi.innerHTML = `<i class='bx bx-plus'></i><span>Tambah</span>`;
});

const editIdTransaksi = document.querySelector('input#id[edit]');
const editPembeliTransaksi = document.querySelector('input#pembeli[edit]');
const editNominalTransaksi = document.querySelector('input#nominal[edit]');
const editBarangTransaksi = document.querySelector('select#barang[edit]');
const btnEditSubmit = document.querySelector('button.edit-t');

editIdTransaksi.addEventListener('keyup', (e) => {
    if (editIdTransaksi.value.length > 0) {
        ipc.send('cek-data-transaksi', editIdTransaksi.value.trim());
    } else {
        editPembeliTransaksi.setAttribute('disabled', '');
        editPembeliTransaksi.value = '';
        editNominalTransaksi.setAttribute('disabled', '');
        editNominalTransaksi.value = '';
        editBarangTransaksi.setAttribute('disabled', '');
    }
});

ipc.on('data-id-transaksi-edit-tidak-ditemukan', () => {
    editPembeliTransaksi.setAttribute('disabled', '');
    editPembeliTransaksi.value = '';
    editNominalTransaksi.setAttribute('disabled', '');
    editNominalTransaksi.value = '';
    editBarangTransaksi.setAttribute('disabled', '');
});

ipc.on('data-id-transaksi-edit-ditemukan', (e, data) => {
    editPembeliTransaksi.removeAttribute('disabled');
    editNominalTransaksi.removeAttribute('disabled');
    editBarangTransaksi.removeAttribute('disabled');
    editPembeliTransaksi.value = data['nama'];
    editNominalTransaksi.value = data['nominal'];
    editBarangTransaksi.value = data['barang'];
});

btnEditSubmit.addEventListener('click', (e) => {
    e.preventDefault();
    if (editTransaksiProcessing != undefined && editTransaksiProcessing === false) {
        editTransaksiProcessing = true;
        btnEditSubmit.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        let data = {
            id: editIdTransaksi.value.trim(),
            pembeli: editPembeliTransaksi.value.trim(),
            nominal: editNominalTransaksi.value.trim(),
            barang: editBarangTransaksi.value.trim()
        };
        ipc.send('edit-data-transaksi', data);
    }
});

ipc.on('edit-data-transaksi-complete', () => {
    btnEditSubmit.innerHTML = `<i class='bx bxs-pen'></i><span>Edit</span>`;
    editTransaksiProcessing = false;
});

const tambahkanAdminNama = document.querySelector('input#nama[tambah-admin]');
const tambahkanAdminEmail = document.querySelector('input#email[tambah-admin]');
const btnTambahkanAdmin = document.querySelector('button.tambah-admin');

btnTambahkanAdmin.addEventListener('click', (e) => {
    e.preventDefault();
    if (tambahkanAdminProcessing != undefined && tambahkanAdminProcessing === false) {
        tambahkanAdminProcessing = true;
        btnTambahkanAdmin.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        let data = {
            nama: tambahkanAdminNama.value.trim(),
            email: tambahkanAdminEmail.value.trim()
        };
        ipc.send('tambahkan-data-admin', data);
    }
});

ipc.on('tambahkan-admin-complete', () => {
    btnTambahkanAdmin.innerHTML = `<i class='bx bx-user-plus'></i><span>Tambah</span>`;
    tambahkanAdminProcessing = false;
});

ipc.on('alert', (e, msg) => {
    alert(msg);
});

let tarea = document.querySelector('textarea[riwayat-admin]');
function getRiwayatAdmin() {
    tarea.value = null;
    ipc.send('get-riwayat-admin');
}
ipc.on('data-riwayat-admin', (e, data) => {
    tarea.value = data;
});
tarea.addEventListener('keydown', (e) => {
    e.preventDefault();
});

let invoiceIdTransaksi = document.querySelector('.invoice-head [id-invoice]');
let invoicePrint = document.querySelector('.invoice-foot [invoice-print]');
let invoiceDownload = document.querySelector('.invoice-foot [invoice-download]');
let invoiceOpeninBrowser = document.querySelector('.invoice-foot [invoice-open-in-browser]');

invoiceOpeninBrowser.addEventListener('click', (e) => {
    e.preventDefault();
    ipc.send('open-invoice-in-browser', invoiceIdTransaksi.value.trim());
});

invoiceDownload.addEventListener('click', (e) => {
    e.preventDefault();
    if (downloadFakturProcessing === false && downloadFakturProcessing != undefined) {
        downloadFakturProcessing = true;
        invoiceDownload.innerHTML = `<div class="spinner-border text-light" role="status"></div>`;
        ipc.send('download-faktur', invoiceIdTransaksi.value.trim());
    }
});

ipc.on('download-faktur-complete', () => {
    downloadFakturProcessing = false;
    invoiceDownload.innerHTML = `<i class='bx bx-cloud-download'
    invoice-download></i><span>Download</span>`;
});

invoicePrint.addEventListener('click', (e) => {
    e.preventDefault();
    ipc.send('print-invoice', invoiceIdTransaksi.value.trim());
});

function kelolaFaktur(id) {
    document.querySelectorAll('button[to]').forEach((button) => {
        button.classList.remove('active');
    });
    movePage('invoice');
    document.querySelector('button[to="invoice"]').classList.add('active');
    document.querySelector('input[id-invoice]').value = id;
}

function openFakturOnline(id) {
    ipc.send('open-invoice-in-browser', id);
}