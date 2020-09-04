<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Nicolaslopezj\Searchable\SearchableTrait;

class Aduan extends Model
{

    use Notifiable;
    use SearchableTrait;

    protected $searchable = [
        'columns' => [
            'aduan.no_aduan'  => 10,
            'aduan.masalah'   => 10,
            'aduan.idjab'   => 10,
            'aduan.tarikh_aduan'    => 10,
        ]
    ];


    protected $fillable = [
        'id', 'no_aduan', 'tajuk_aduan', 'masalah', 'errormsg',
        'idkategori', 'jenis_kategori', 'noinventori', 'model',
        'tarikh_aduan', 'masa_aduan', 'idstatus', 'lampiran',
        'maklumbalas', 'maklumbalas_jbtn', 'tarikh_tindakan',
        'masa_tindakan', 'nosiri', 'id_pengadu', 'id_pengguna',
        'lokasi', 'idopen', 'masa_respon', 'tarikh_respon',
        'id_onsite', 'id_ganti', 'masa_onsite', 'tarikh_onsite',
        'soalan_1', 'soalan_2', 'komen', 'id_poll',
        'tarikh_aduan_jbtn', 'masa_aduan_jbtn',
        'tarikh_tindakan_jbtn', 'masa_tindakan_jbtn', 'idjab',
        'bantuan_ptmkn_flag', 'bantuan_ptmkn_ctn', 'idbahagian'
    ];

    public function user()
{
    return $this->belongsTo('App\User', 'id_pengguna', 'no_aduan');
}

}
