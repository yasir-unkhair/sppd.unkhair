<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SuratPerjalananDinas extends Model
{
    use HasFactory;

    protected $table = 'app_surat_perjalanan_dinas';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });

        // delete surat tugas
        static::deleting(function ($model) {
            foreach ($model->surat_tugas as $row) {
                $row->delete();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'nomor_spd',
        'tanggal_spd',
        'pegawai_id',
        'departemen_id',
        'kegiatan_spd',
        'angkutan',
        'berangakat',
        'tujuan',
        'lama_pd',
        'tanggal_berangakat',
        'tanggal_kembali',
        'keterangan',
        'kode_mak',
        'detail_alokasi_anggaran',
        'nilai_pencairan', // di input oleh keuangan (ibu ning)

        'sumber_dana',
        'instansi',

        'status_spd',
        'pejabat_ppk',
        'tanggal_review',
        'reviewer_id',
        'alasan'
    ];

    public function scopepencarian($query, $value)
    {
        if ($value) {
            $query->where('app_surat_perjalanan_dinas.nomor_spd', '=', $value);
        }
    }

    public function scopestatus_spd($query, $value)
    {
        if ($value) {
            $query->whereIn('app_surat_perjalanan_dinas.status_spd', $value);
        }
    }

    public function scopebulan($query, $value)
    {
        if ($value) {
            $query->whereMonth('app_surat_perjalanan_dinas.created_at', $value);
        }
    }

    public function scopetahun($query, $value)
    {
        if ($value) {
            $query->whereYear('app_surat_perjalanan_dinas.created_at', $value);
        }
    }

    public function scopeadmin_spd($query, $value)
    {
        if ($value) {
            $query->where('app_surat_perjalanan_dinas.user_id', '=', $value);
        }
    }

    public function surat_tugas()
    {
        return $this->hasOne(SuratTugasDinas::class, 'spd_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'id', 'pegawai_id');
    }

    public function departemen()
    {
        return $this->hasOne(Departemen::class, 'id', 'departemen_id');
    }

    public function reviwer()
    {
        return $this->hasOne(User::class, 'id', 'reviewer_id');
    }
}
