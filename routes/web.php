<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PengaduanController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
    use App\Http\Controllers\TransaksiController;
    use App\Http\Controllers\Teknisi\AbsenController;
        use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\KeuanganController;




Route::get('/', function () {
    return view('welcome');
});

// 🔐 Grup route khusus admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // // CRUD Paket
    // Route::resource('paket', PackageController::class);

    // CRUD Pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/tambah', [PelangganController::class, 'create'])->name('pelanggan.create');
    Route::post('/pelanggan/simpan', [PelangganController::class, 'store'])->name('pelanggan.store');
Route::put('/pelanggan/update/{id}', [PelangganController::class, 'update'])
    ->name('admin.pelanggan.update');
Route::get('/admin/pelanggan/berhenti', 
    [App\Http\Controllers\Admin\PelangganController::class, 'berhenti']
)->name('admin.pelanggan_berhenti');
Route::put('/admin/pelanggan/aktifkan/{id}', 
    [PelangganController::class, 'aktifkanUlang']
)->name('admin.pelanggan_aktifkan');

Route::get('/admin/pelanggan_baru', 
    [PelangganController::class, 'pelangganBaru']
)->name('admin.pelanggan_baru');
    // Modem
    Route::get('/modem', [PelangganController::class, 'modem'])->name('admin.modem');
    Route::post('/modem', [PelangganController::class, 'storeModem'])->name('admin.modem');

    // Pelanggan khusus filter/status
    Route::get('/pelanggan/belum-bayar', [PelangganController::class, 'belumBayar'])->name('admin.belum_bayar');
    Route::get('/pelanggan/lunas', [PelangganController::class, 'pelangganLunas'])->name('admin.pelanggan_lunas');
    Route::get('/pelanggan/semua', [PelangganController::class, 'pelangganSemua'])->name('admin.pelanggan_semua');

    // Detail spesifik (spesifik dulu)
Route::get('/pelanggan/belum-bayar/{id}', [PelangganController::class, 'detailBelumBayar'])
    ->whereNumber('id')
    ->name('admin.detail_belum_bayar');


Route::get('/pelanggan/lunas/{id}', [PelangganController::class, 'detailLunas'])
    ->whereNumber('id')
    ->name('admin.detail_lunas');

// Detail general (paling bawah)
// Pelanggan
Route::get('/pelanggan/{id}', [PelangganController::class, 'detail'])
    ->whereNumber('id')
    ->name('admin.detail_pelanggan');

Route::get('/admin/pelanggan/{id}/belum-bayar', [PelangganController::class, 'detail_belum_bayar'])
    ->name('pelanggan.detail_belum_bayar');

// Tagihan





Route::get('/generate-tagihan', [TagihanController::class, 'generateBulanan'])
    ->name('tagihan.generate_bulanan');

Route::get('tagihan/detail-belum-bayar/{id}', [TagihanController::class, 'detail_belum_bayar'])
    ->name('tagihan.detail_belum_bayar_show');



    // Koordinat dan simpan custom
    Route::get('/koordinat', [PelangganController::class, 'koordinat'])->name('admin.koordinat');
    Route::post('/simpan', [PelangganController::class, 'simpan'])->name('admin.simpan');

    // CRUD Sales & Teknisi
    Route::resource('user', UserController::class);

    // Tagihan
    Route::get('/tagihan/buat', [TagihanController::class, 'buatTagihan'])->name('tagihan.buat');
    Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');
// Ubah status tagihan menjadi lunas
Route::post('/tagihan/{id}/lunas', [TagihanController::class, 'setLunas'])
    ->name('tagihan.setLunas');
    Route::get('/tagihan/{id}/detail', [TagihanController::class, 'detail'])
    ->name('admin.detail');


    // Pengaduan
    Route::resource('pengaduan', PengaduanController::class);
    Route::post('pengaduan/{id}/kirim', [PengaduanController::class, 'kirimKeSales'])->name('pengaduan.kirim');
Route::get('/history/lunas', [App\Http\Controllers\Admin\TagihanController::class, 'historyLunas'])->name('history.lunas');
Route::delete('/history/lunas/{id}', [App\Http\Controllers\Admin\TagihanController::class, 'deleteLunas'])->name('history.lunas.delete');
Route::delete('/history/lunas', [App\Http\Controllers\Admin\TagihanController::class, 'deleteAllLunas'])->name('history.lunas.deleteAll');

Route::prefix('admin')->group(function () {
    Route::get('/transaksi-cash', [TransaksiController::class, 'cash'])->name('admin.transaksi_cash');
        Route::get('/transaksi-online', [TransaksiController::class, 'online'])->name('admin.transaksi_online');
 Route::get('/pelanggan-telat', [PelangganController::class, 'telat'])->name('admin.pelanggan_telat');
    Route::get('/pelanggan-nunggak', [PelangganController::class, 'nunggak'])->name('admin.pelanggan_nunggak');
    // CRUD Pelanggan - Tambahkan UPDATE
Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])
    ->whereNumber('id')
    ->name('admin.pelanggan.update');

});
Route::prefix('admin')->middleware('auth')->group(function () {
Route::post('pengaduan/{id}/kirim', [PengaduanController::class, 'kirimKeSales'])->name('pengaduan.kirim');
});
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // History absensi
    Route::get('/absen/history', [\App\Http\Controllers\Admin\AbsenController::class, 'history'])
        ->name('admin.absen.history');

    // Update status izin
    Route::post('/izin/update-status/{id}', [\App\Http\Controllers\Admin\AbsenController::class, 'updateIzinStatus'])
        ->name('admin.izin.updateStatus');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Halaman daftar izin
    Route::get('/izin', [\App\Http\Controllers\Admin\IzinController::class, 'index'])
        ->name('admin.izin.index');

    // Setujui izin
    Route::post('/izin/{id}/setujui', [\App\Http\Controllers\Admin\IzinController::class, 'setujui'])
        ->name('admin.izin.setujui');

    // Tolak izin
    Route::post('/izin/{id}/tolak', [\App\Http\Controllers\Admin\IzinController::class, 'tolak'])
        ->name('admin.izin.tolak');

    // (Optional) Kembalikan status menjadi pending
    Route::post('/izin/{id}/pending', [\App\Http\Controllers\Admin\IzinController::class, 'pending'])
        ->name('admin.izin.pending');
            Route::get('/izin/{user_id}/{bulan}/{tahun}', [\App\Http\Controllers\Admin\IzinController::class, 'bulan'])
        ->name('admin.izin.bulan');
Route::post('/transaksi/batal', [\App\Http\Controllers\TransaksiController::class, 'batalkan'])
    ->name('transaksi.batal');



});

});

Route::prefix('admin')->middleware('auth')->group(function() {
    Route::get('paket', [\App\Http\Controllers\Admin\PackageController::class, 'index'])->name('admin.paket.index');
    Route::get('paket/create', [\App\Http\Controllers\Admin\PackageController::class, 'create'])->name('paket.create');
    Route::post('paket', [\App\Http\Controllers\Admin\PackageController::class, 'store'])->name('paket.store');
    Route::get('paket/{id}/edit', [\App\Http\Controllers\Admin\PackageController::class, 'edit'])->name('paket.edit');
    Route::put('paket/{id}', [\App\Http\Controllers\Admin\PackageController::class, 'update'])->name('paket.update');
    Route::delete('paket/{id}', [\App\Http\Controllers\Admin\PackageController::class, 'destroy'])->name('paket.destroy');
    Route::get('/setting', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('setting');
        Route::get('/notifikasi', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])
        ->name('admin.notif.index');

    Route::get('/notifikasi/read/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'read'])
        ->name('admin.notif.read');

    Route::get('/notifikasi/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'readAll'])
        ->name('admin.notif.readall');

});
// routes/web.php
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('tagihan', [App\Http\Controllers\Admin\TagihanController::class, 'index'])->name('tagihan.index');
    Route::post('tagihan/generate', [App\Http\Controllers\Admin\TagihanController::class, 'generate'])->name('tagihan.generate');
    Route::get('/auto-generate', [TagihanController::class, 'autoGenerateTagihan']);

});

use App\Http\Controllers\TeknisiController;

Route::prefix('admin')->group(function() {
    Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi.index');
    Route::get('/teknisi/create', [TeknisiController::class, 'create'])->name('teknisi.create');
    Route::post('/teknisi/store', [TeknisiController::class, 'store'])->name('teknisi.store');
    Route::get('/teknisi/{id}/edit', [TeknisiController::class, 'edit'])->name('teknisi.edit');
    Route::put('/teknisi/{id}', [TeknisiController::class, 'update'])->name('teknisi.update');
    Route::delete('/teknisi/{id}', [TeknisiController::class, 'destroy'])->name('teknisi.destroy');
});


// 🔐 Route profil untuk user login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🚪 Force logout (paksa logout user)
Route::get('/force-logout', function () {
    Auth::logout();
    return redirect('/login');
});
// Contoh route untuk dashboard sales
Route::get('/sales/dashboard', [App\Http\Controllers\SalesController::class, 'dashboard'])
    ->name('sales.dashboard');
    
Route::middleware(['auth'])->prefix('kangteknisi')->name('kangteknisi.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'dashboard'])->name('dashboard');
// routes/web.php


    Route::get('/pengaduan', [App\Http\Controllers\Teknisi\PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{id}', [App\Http\Controllers\Teknisi\PengaduanController::class, 'show'])->name('pengaduan.show');

    Route::get('/status', [App\Http\Controllers\Teknisi\StatusController::class, 'index'])->name('status.index');
    Route::post('/status/update/{id}', [App\Http\Controllers\Teknisi\StatusController::class, 'update'])->name('status.update');
Route::prefix('kangteknisi')->group(function () {

    Route::patch('pengaduan/{id}/selesaikan',
        [PengaduanController::class, 'selesaikan']
    )->name('kangteknisi.pengaduan.selesaikan');

             Route::get('/pelanggan', [App\Http\Controllers\Teknisi\PelangganController::class, 'index'])
            ->name('pelanggan.index');

        Route::get('/pelanggan/{id}', [App\Http\Controllers\Teknisi\PelangganController::class, 'detail'])
            ->name('pelanggan.detail');
Route::middleware(['auth'])->group(function () {
});


});

    // 🆕 Tambahkan ini untuk halaman setting
    Route::get('/setting', [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'setting'])->name('setting');
});



Route::middleware(['auth'])->group(function () {
    Route::put('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

use App\Http\Controllers\Admin\ODCController;

Route::prefix('odc')->name('odc.')->group(function() {
    Route::get('/', [App\Http\Controllers\Admin\ODCController::class, 'index'])->name('index');       // halaman list ODC
    Route::post('/', [App\Http\Controllers\Admin\ODCController::class, 'store'])->name('store');      // simpan ODC baru
    Route::get('/{odc}/edit', [App\Http\Controllers\Admin\ODCController::class, 'edit'])->name('edit'); 
    Route::put('/{odc}', [App\Http\Controllers\Admin\ODCController::class, 'update'])->name('update'); 
    Route::delete('/{odc}', [App\Http\Controllers\Admin\ODCController::class, 'destroy'])->name('destroy'); 
});
// ODP
Route::prefix('odp')->name('odp.')->group(function() {
    Route::get('/', [App\Http\Controllers\Admin\OdpController::class, 'index'])->name('index');       // halaman list ODP
    Route::post('/', [App\Http\Controllers\Admin\OdpController::class, 'store'])->name('store');      // simpan ODP baru
    Route::get('/{odp}/edit', [App\Http\Controllers\Admin\OdpController::class, 'edit'])->name('edit'); 
    Route::put('/{odp}', [App\Http\Controllers\Admin\OdpController::class, 'update'])->name('update'); 
    Route::delete('/{odp}', [App\Http\Controllers\Admin\OdpController::class, 'destroy'])->name('destroy'); 
});

Route::prefix('admin')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('admin.user.index');
    Route::post('/user', [UserController::class, 'store'])->name('admin.user.store');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('admin.user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
});
Route::get('/kangteknisi/history/lunas', [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'historyLunas'])
    ->name('kangteknisi.history.lunas');

// =======================
// ROUTE ABSENSI TEKNISI
// ROUTE ABSENSI TEKNISI
Route::middleware(['auth'])->prefix('teknisi/absen')->name('absen.')->group(function () {

    // Halaman index → GET
    Route::get('/', [AbsenController::class, 'index'])->name('index');

    // Halaman detail → GET
    Route::get('/detail', [AbsenController::class, 'detail'])->name('detail');

    // Tombol Absen Masuk → POST
    Route::post('/masuk', [AbsenController::class, 'absenMasuk'])->name('masuk');

    // Tombol Absen Pulang → POST
    Route::post('/{id}/pulang', [AbsenController::class, 'absenPulang'])->name('pulang');

    // Halaman history → GET
    Route::get('/history', [AbsenController::class, 'history'])->name('history');
    
    // DETAIL HISTORY
    Route::get('/history/detail/{id}', [AbsenController::class, 'detailHistory'])->name('detailhistory');

    // =====================
    // Halaman Izin Absen
    // =====================
    Route::get('/izin', [AbsenController::class, 'izin'])->name('izin');
    Route::post('/izin', [AbsenController::class, 'submitIzin'])->name('izin.submit');

});

    Route::get('/sales/dashboard', [App\Http\Controllers\SalesController::class, 'dashboard'])
        ->name('sales.dashboard');
        
use App\Http\Controllers\Sales\SalesPelangganController;
use App\Http\Controllers\Sales\SalesTagihanController;



Route::middleware(['auth','role:sales'])
    ->prefix('sales')
    ->name('sales.')
    ->group(function () {

        // Daftar pelanggan
        Route::get('/pelanggan', [SalesPelangganController::class, 'index'])
            ->name('pelanggan.index');

        // Detail semua tagihan pelanggan
        Route::get('/pelanggan/{id}/detail', [SalesTagihanController::class, 'pelangganTagihan'])
            ->name('pelanggan.detail');

        // Proses bayar multi-bulan
        Route::post('/pelanggan/{id}/bayar', [SalesTagihanController::class, 'bayar'])
            ->name('pelanggan.bayar');

        // Daftar tagihan bulan berjalan
        Route::get('/tagihan', [SalesTagihanController::class, 'index'])
            ->name('tagihan.index');

        // Detail tagihan spesifik
        Route::get('/tagihan/{id}/detail', [SalesTagihanController::class, 'detail'])
            ->name('tagihan.detail');

        // Update status pembayaran
        Route::post('/tagihan/{id}/update-status', [SalesTagihanController::class, 'updateStatus'])
            ->name('tagihan.updateStatus');

        // Selesaikan pengaduan
        Route::post('/pengaduan/{id}/selesaikan', 
            [App\Http\Controllers\Teknisi\PengaduanController::class, 'selesaikan']
        )->name('pengaduan.selesaikan');
        
Route::get('/pages', [SalesController::class, 'pages'])
    ->name('pages.index');

Route::get('/pelanggan/lunas', [SalesPelangganController::class, 'lunas'])
    ->name('pelanggan.lunas');
Route::get('/pelanggan/{id}/lunas', 
    [SalesPelangganController::class, 'detailLunas']
)->name('pelanggan.detail_lunas');
Route::get('/sales/pelanggan/detail-lunas/{id}', [SalesPelangganController::class, 'detailLunasJson'])
    ->name('sales.pelanggan.detail_lunas_json');


Route::get('/pelanggan/detail-lunas/{id}', [SalesPelangganController::class, 'detailLunasJson'])
    ->name('sales.pelanggan.detail_lunas_json');
    Route::get('/setting', [App\Http\Controllers\Sales\SalesPelangganController::class, 'setting'])
        ->name('setting');

});




Route::prefix('teknisi')->middleware(['auth'])->group(function () {

    // DETAIL JSON
    Route::get('/absen/pelanggan/{id}/detail-json', 
        [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'detailJson']
    )->name('absen.pelanggan.detail-json');

});

Route::get('/admin/pelanggan-baru/{id}', [PelangganController::class, 'detailBaru'])
    ->name('admin.detail_pelanggan_baru');


// Route::patch('/kangteknisi/pengaduan/{id}/selesai', [PengaduanController::class, 'selesai'])
//     ->name('kangteknisi.pengaduan.selesai');
//     Route::patch('/kangteknisi/pengaduan/{id}/selesai', [PengaduanController::class, 'selesai'])->name('kangteknisi.pengaduan.selesai');

Route::post('/tagihan/set-lunas-multi/{id}', [TagihanController::class, 'setLunasMulti'])
    ->name('tagihan.setLunasMulti');
    Route::get('/kangteknisi/keuangan', [\App\Http\Controllers\Teknisi\KeuanganController::class, 'index'])
     ->name('keuangan.index');
     Route::get('/admin/setting', [SettingController::class, 'index'])->name('setting');
// Tampilkan halaman template WA
Route::get('/setting/wa-template', [SettingController::class, 'waTemplate'])->name('setting.wa_template');

// Simpan perubahan template WA
Route::post('/setting/wa-template', [SettingController::class, 'saveWaTemplate'])->name('setting.wa_template.save');
Route::get('/setting/wa-template', [SettingController::class, 'waTemplate'])->name('setting.wa_template');
Route::post('/setting/wa-template/save', [SettingController::class, 'saveWaTemplate'])->name('setting.wa_template.save');
// Route::get('/setting/wa-template/gunakan/{key_name}', [SettingController::class, 'gunakanTemplate'])->name('setting.wa_template.gunakan');
Route::post('setting/wa_template/gunakan/{key}', [SettingController::class, 'gunakanTemplate'])
    ->name('setting.wa_template.gunakan');
Route::get('/pelanggan/belum-bayar/{id}', [TagihanController::class, 'detail_belum_bayar'])
    ->whereNumber('id')
    ->name('tagihan.detail_belum_bayar');

    Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('admin/requests-password', [NotificationController::class, 'requestsPassword'])->name('admin.requestsPassword');

// Admin approve perubahan password
Route::post('admin/approve-password/{notification}', [NotificationController::class, 'approvePassword'])
    ->name('admin.approvePassword');
    
});
Route::prefix('kangteknisi')->name('kangteknisi.')->group(function () {

    Route::post('update-password', 
        [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'updatePassword']
    )->name('updatePassword');


    Route::post('request-delete-history', [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'requestDeleteHistory'])
        ->name('requestDeleteHistory');
        });
        
    Route::prefix('admin')->name('admin.')->group(function () {
Route::prefix('admin')->name('admin.')->group(function () {

    Route::post('notifications/approve-password/{id}',
        [App\Http\Controllers\Admin\NotificationController::class, 'approvePasswordChange']
    )->name('notifications.approvePassword');

});
        
});

Route::middleware(['auth'])
    ->prefix('kangteknisi')
    ->name('kangteknisi.')
    ->group(function () {

        Route::post(
            '/pengaduan/{id}/selesai',
            [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'selesaiTugas']
        )->name('pengaduan.selesai');

});
use App\Http\Controllers\Admin\AreaController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','admin'])
    ->group(function () {
        Route::resource('areas', AreaController::class);
    });

Route::middleware(['auth'])
    ->prefix('kangteknisi')
    ->name('kangteknisi.')
    ->group(function () {

        Route::post(
            'pengaduan/{id}/selesai',
            [App\Http\Controllers\Teknisi\KangTeknisiController::class, 'selesaiTugas']
        )->name('pengaduan.selesai');

});
Route::middleware(['auth','role:sales'])->group(function () {
    Route::get('/sales/setting', [SalesPelangganController::class, 'setting'])->name('sales.setting');
    Route::post('/sales/update-password', [SalesPelangganController::class, 'updatePassword'])->name('sales.updatePassword');
});

require __DIR__ . '/auth.php';

