<?php

use Illuminate\Support\Facades\Route;
use App\Bahagian;
/* use Illuminate\Support\Facades\Mail;
use App\Mail\EmailAduan; */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/welcome', function () {
    return view('welcome');
}); */

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/contoh', function () {
    return view('auth.login2');
});

Route::get('/daftarPengguna', 'DaftarController@daftarPengguna')->name('daftar.daftarPengguna');
Route::post('daftarPengguna/fetch', 'DaftarController@fetch')->name('daftarPengguna.fetch');
Route::get('/selesaiDaftar', 'DaftarController@afterDaftar')->name('daftar.afterDaftar');
Route::get('/dahDaftar', 'DaftarController@dahDaftar')->name('daftar.dahDaftar');

Route::post('store_daftar', 'DaftarController@storePengguna')->name('daftar.store');

Route::get('/forgotpassword', 'DaftarController@forgotPass')->name('daftar.forgotpass');
Route::post('store_forgot', 'DaftarController@storeForgot')->name('forgot.store');
Route::get('/selesaiForgot', 'DaftarController@afterForgot')->name('daftar.afterForgot');


Route::get('/maklumbalas/{aduan}', 'DaftarController@maklumbalas')->name('daftar.feedback');
Route::post('storeMaklumbalas', 'DaftarController@storeMaklumbalas')->name('daftar.storeFeedback');
Route::get('/maklumbalasBerjaya', 'DaftarController@maklumbalasBerjaya')->name('daftar.successFeedback');
Route::get('/maklumatGagal', 'DaftarController@maklumbalasGagal')->name('daftar.doneFeedback');


Route::get('/resetpassword', function () {
    return view('daftar.resetpass');
});

Route::post('reset_store', 'DaftarController@resetPass')->name('reset.store');

/* Route::get('/email', function () {
    Mail::to('nazrinaim@gmail.com')->send(new EmailAduan);

    return new EmailAduan();
}); */

Auth::routes();

//admin
Route::group([

    'middleware' => ['auth', 'idlevel:1']],
    function () {
    Route::get('/reset_password', 'AdminController@resetPass')->name('admin.reset');//page reset password
    Route::post('/store_password', 'AdminController@storePass')->name('admin.pass');//proses reset password

    Route::get('/home', 'AdminController@adminHome')->name('admin.dashboard');//halaman utama

    Route::get('/tambahaduan', 'AdminController@createAduan')->name('admin.add_aduan');//halaman tambah aduan
    Route::post('storeAduan', 'AdminController@storeAduan')->name('admin.storeAduan');

    /* Route::get('/senaraiaduan', 'AdminController@listAduan')->name('admin.listaduan'); */

    Route::get('/aduanbaru', 'AdminController@aduanBaru')->name('admin.aduanbaru');//halaman aduan baru
    Route::get('/aduanproses', 'AdminController@aduanProses')->name('admin.aduanproses');//halaman aduan dalam tindakan
    Route::get('/aduanpembekal', 'AdminController@aduanPembekal')->name('admin.aduanpembekal');//halaman aduan pembekal

    Route::get('/aduanselesai', 'AdminController@aduanSelesai')->name('admin.aduanselesai');//halaman aduan selesai

    Route::get('cariSelesai', 'AdminController@cariSelesai')->name('adminSelesai.cariSelesai');//
    Route::get('carian', 'AdminController@searchSelesai')->name('admin.searchSelesai');
    Route::get('maklumatSelesai/{id}', 'AdminController@maklumatSelesai')->name('adminSelesai.maklumatSelesai');



    Route::get('/aduantolak', 'AdminController@aduanTolak')->name('admin.aduantolak');

    Route::get('/senaraiPengguna', 'AdminController@listJabatan')->name('admin.list_pengguna');
    Route::post('list/fetch', 'AdminController@list')->name('admin.list');
    Route::get('list2/fetch', 'AdminController@list2')->name('admin.list2');

    Route::get('/editpengguna/{no_kp}', 'AdminController@detailpengguna')->name('admin.edit_pengguna');
    Route::post('listbah/fetch', 'AdminController@listBah')->name('admin.listBah');
    Route::post('store_detail', 'AdminController@storeDetail');

    Route::get('/profil', 'AdminController@profil')->name('admin.profil');

    Route::get('/editaduan/{no_aduan}', 'AdminController@editAduan')->name('admin.edit_aduan');

    /* Route::get('/modal/{no_aduan}', 'AdminController@modal')->name('admin.modal'); */

    Route::post('reject/{no_aduan}', 'AdminController@reject');

    Route::post('agihan/{no_aduan}', 'AdminController@agihan');

    Route::get('/maklumatAduan/{id}', 'AdminController@maklumatAduan')->name('admin.maklumatAduan');

    Route::get('/edit_profil/{idpengguna}', 'AdminController@editProfil')->name('admin.edit_profil');

    Route::post('a_storeProfil/{idpengguna}', 'AdminController@storeProfil')->name('profil.store');

    Route::get('/aktifPengguna', 'AdminController@aktifPengguna')->name('admin.aktifPengguna');
    Route::post('aktif/{idpengguna}', 'AdminController@aktif');

    Route::get('/cariAduan', 'AdminController@cariAduan')->name('admin.cariAduan');
    Route::get('search', 'AdminController@search')->name('admin.autocomplete');

    Route::get('/semakRekod', 'AdminController@semakRekod')->name('admin.semakRekod');
    Route::get('rekod', 'AdminController@rekod')->name('admin.rekod');
    Route::get('rekod2', 'AdminController@rekod2')->name('admin.rekod2');

    Route::get('/add_pengguna','AdminController@addPengguna')->name('admin.add_pengguna');
    Route::post('storeProfil/fetch', 'AdminController@fetch')->name('pengguna.fetch');
    Route::post('store_pengguna', 'AdminController@storePengguna')->name('pengguna.store');

    Route::get('/listTeknikal', 'AdminController@listTeknikal')->name('admin.listTeknikal');

    Route::get('statistikTahunan', 'AdminController@statistikTahunan')->name('admin.statistikTahunan');//statistik tahunan
    Route::get('tahunan', 'AdminController@tahunan')->name('admin.tahunan');
    Route::get('/statistikTahunan/pdf/{tahun_mula}/{tahun_akhir}', 'AdminController@pdf');
    Route::get('/statistikTahunan/csv/{tahun_mula}/{tahun_akhir}', 'AdminController@csv');


    Route::get('statistikKategori', 'AdminController@statistikKategori')->name('admin.statistikKategori');//statistik kategori
    Route::post('kategori/fetch', 'AdminController@sublist')->name('admin.sublist');
    Route::get('kategori', 'AdminController@kategori')->name('admin.kategori');
    Route::get('kategoriDetail/{idkategori}/{tahun}/{bln}', 'AdminController@kategoriDetail')->name('admin.kategoriDetail');
    Route::get('/statistikKategori/pdf/{tahun}/{kategori}', 'AdminController@pdfKategori');
    Route::get('/statistikKategori/csv/{tahun}/{kategori}', 'AdminController@csvKategori');

    Route::get('statistikJabatan', 'AdminController@statistikJabatan')->name('admin.statistikJabatan');//statistik jabatan
    Route::get('jabatan', 'AdminController@jabatan')->name('admin.jabatan');
    Route::get('jabatanDetail/{idjab}/{tahun}/{bln}', 'AdminController@jabatanDetail')->name('admin.jabatanDetail');
    Route::get('/statistikJabatan/pdf/{tahun}/{idjab}', 'AdminController@pdfJabatan');
    Route::get('/statistikJabatan/csv/{tahun}/{idjab}', 'AdminController@csvJabatan');

    Route::get('statistikTechnician', 'AdminController@statistikTechnician')->name('admin.statistikTechnician');//statistik technician
    Route::get('technician', 'AdminController@technician')->name('admin.technician');
    Route::get('techDetail/{id_pengguna}/{tahun}/{bln}', 'AdminController@techDetail')->name('admin.techDetail');
    Route::get('/statistikTech/pdf/{tahun}/{pengguna}', 'AdminController@pdfTech');
    Route::get('/statistikTech/csv/{tahun}/{pengguna}', 'AdminController@csvTech');

    Route::get('statistikMaklumbalas', 'AdminController@statistikMaklumbalas')->name('admin.statistikMaklumbalas');//statistik maklumbalas
    Route::get('maklumbalas', 'AdminController@maklumbalas')->name('admin.maklumbalas');
    Route::get('maklumbalasDetail/{respon_feedback}/{tahun}/{bln}', 'AdminController@maklumbalasDetail')->name('admin.maklumbalasDetail');
    Route::get('/statistikMaklumbalas/pdf/{tahun}/{pengguna}', 'AdminController@pdfFeed');
    Route::get('/statistikMaklumbalas/csv/{tahun}/{pengguna}', 'AdminController@csvFeed');

    Route::get('tambahkategori', 'AdminController@addKategori')->name('admin.addKategori');
    Route::post('storeKategori', 'AdminController@storeKategori')->name('admin.storeKategori');

    Route::get('tambahsubkat', 'AdminController@addSubkat')->name('admin.addSubkat');
    Route::get('listKat/fetch', 'AdminController@listKat')->name('admin.listKat');
    Route::post('storeSubkat', 'AdminController@storeSubkat')->name('admin.storeSubkat');

    Route::get('tambahmodel', 'AdminController@addModel')->name('admin.addModel');
    Route::post('storeModel', 'AdminController@storeModel')->name('admin.storeModel');


});


//technician
Route::group([

    'middleware' => ['auth', 'idlevel:2']],
    function () {
    Route::get('/t_reset_password', 'TechnicianController@resetPass')->name('technician.reset');
    Route::post('/t_store_password', 'TechnicianController@storePass')->name('technician.pass');

    Route::get('/t_home', 'TechnicianController@technicianHome')->name('technician.dashboard');

    Route::get('/t_aduanbaru', 'TechnicianController@aduanBaru')->name('technician.aduanbaru');

    Route::get('/t_aduanproses', 'TechnicianController@aduanProses')->name('technician.aduanproses');

    Route::get('/t_aduanselesai', 'TechnicianController@aduanSelesai')->name('technician.aduanselesai');

    Route::get('t_cariSelesai', 'TechnicianController@cariSelesai')->name('technicianSelesai.cariSelesai');//
    Route::get('t_carian', 'TechnicianController@searchSelesai')->name('technician.searchSelesai');
    Route::get('t_maklumatSelesai/{id}', 'TechnicianController@maklumatSelesai')->name('technicianSelesai.maklumatSelesai');

    Route::get('/t_aduantolak', 'TechnicianController@aduanTolak')->name('technician.aduantolak');

    Route::get('/t_tambahaduan', 'TechnicianController@createAduan')->name('technician.add_aduan');//halaman tambah aduan
    Route::post('t_storeAduan', 'TechnicianController@storeAduan')->name('technician.storeAduan');

    Route::get('/t_senaraipengguna', 'TechnicianController@listJabatan')->name('technician.list_pengguna');
    Route::post('t_list/fetch', 'TechnicianController@list')->name('technician.list');
    Route::get('t_list2/fetch', 'TechnicianController@list2')->name('technician.list2');

    Route::get('/t_editpengguna/{no_kp}', 'TechnicianController@detailpengguna')->name('technician.edit_pengguna');
    Route::post('t_listbah/fetch', 'TechnicianController@listBah')->name('technician.listBah');
    Route::post('t_store_detail', 'TechnicianController@storeDetail')->name('technician.storeDetail');

    Route::get('/t_profil','TechnicianController@profil')->name('technician.profil');

    Route::get('/t_editaduan/{no_aduan}','TechnicianController@editAduan')->name('technician.edit_aduan');
    Route::post('t_subkat/fetch', 'TechnicianController@subKat')->name('technician.subkat');

    Route::get('/t_editpembekal/{no_aduan}','TechnicianController@editPembekal')->name('technician.edit_pembekal');

    Route::post('t_pembekal/{no_aduan}', 'TechnicianController@storePembekal');

    Route::post('t_agihan/{no_aduan}', 'TechnicianController@agihan');

    Route::post('t_reject/{no_aduan}', 'TechnicianController@reject');

    Route::get('/t_maklumatAduan/{id}', 'TechnicianController@maklumatAduan')->name('technician.maklumatAduan');

    Route::get('/t_edit_profil/{idpengguna}', 'TechnicianController@editProfil')->name('technician.edit_profil');

    Route::post('t_storeProfil/{idpengguna}', 'TechnicianController@storeProfil')->name('t_profil.store');

    Route::get('/t_senaraiaduan', function () {
        return view('technician.listaduan');
    });

    Route::get('/t_semakRekod', 'TechnicianController@semakRekod')->name('technician.semakRekod');
    Route::get('t_rekod', 'TechnicianController@rekod')->name('technician.rekod');
    Route::get('t_rekod2', 'TechnicianController@rekod2')->name('technician.rekod2');

    Route::get('/t_add_pengguna','TechnicianController@addPengguna')->name('technician.add_pengguna');
    Route::post('t_storePengguna/fetch', 'TechnicianController@fetch')->name('t_pengguna.fetch');
    Route::post('t_store_pengguna', 'TechnicianController@storePengguna')->name('t_pengguna.store');

    Route::get('/t_cariAduan', 'TechnicianController@cariAduan')->name('technician.cariAduan');
    Route::get('t_search', 'TechnicianController@search')->name('technician.autocomplete');

    Route::get('/t_listTeknikal', 'TechnicianController@listTeknikal')->name('technician.listTeknikal');

    Route::get('t_tambahkategori', 'TechnicianController@addKategori')->name('technician.addKategori');
    Route::post('t_storeKategori', 'TechnicianController@storeKategori')->name('technician.storeKategori');

    Route::get('t_tambahsubkat', 'TechnicianController@addSubkat')->name('technician.addSubkat');
    Route::get('t_listKat/fetch', 'TechnicianController@listKat')->name('technician.listKat');
    Route::post('t_storeSubkat', 'TechnicianController@storeSubkat')->name('technician.storeSubkat');

    Route::get('t_tambahmodel', 'TechnicianController@addModel')->name('technician.addModel');
    Route::post('t_storeModel', 'TechnicianController@storeModel')->name('technician.storeModel');




});


//pengguna
Route::group([

    'middleware' => ['auth', 'idlevel:4']],
    function () {

    Route::get('/p_reset_password', 'PenggunaController@resetPass')->name('pengguna.reset');
    Route::post('/p_store_password', 'PenggunaController@storePass')->name('pengguna.pass');
   /*  Route::get('/p_home', 'PenggunaController@penggunaHome')->name('pengguna.dashboard');

    Route::get('/p_aduanbaru', 'PenggunaController@aduanBaru')->name('pengguna.aduanbaru');

    Route::get('/p_aduanproses', 'PenggunaController@aduanProses')->name('pengguna.aduanproses');

    Route::get('/p_aduanselesai', 'PenggunaController@aduanSelesai')->name('pengguna.aduanselesai');

    Route::get('/p_aduantolak', 'PenggunaController@aduanTolak')->name('pengguna.aduantolak'); */

    Route::get('/p_home', 'PenggunaController@listAduan')->name('pengguna.listaduan');

    Route::get('/p_profil', 'PenggunaController@profil')->name('pengguna.profil');

    Route::get('/p_tambahaduan', 'PenggunaController@create')->name('pengguna.add_aduan');

    Route::post('store', 'PenggunaController@Store')->name('aduan.store');

    Route::get('/p_detailaduan/{no_aduan}', 'PenggunaController@detail')->name('pengguna.detail_aduan');

    Route::get('/p_edit_profil/{idpengguna}', 'PenggunaController@editProfil')->name('pengguna.edit_profil');

    Route::post('p_storeProfil/{idpengguna}', 'PenggunaController@storeProfil')->name('p_profil.store');


});
