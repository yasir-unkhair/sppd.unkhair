<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SuratTugasDinas extends Model
{
    use HasFactory;

    protected $table = 'app_surat_tugas_dinas';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'std_dk',
        'spd_id',
        'nomor_std',
        'tanggal_std',
        'departemen_id',
        'kegiatan_std',
        'tanggal_mulai_tugas',
        'tanggal_selesai_tugas',
        'keterangan',
        'kode_mak',
        'detail_alokasi_anggaran',
        'nilai_pencairan', // di input oleh keuangan (ibu ning)
        'kelengkapan_laporan_std',
        'tembusan_std',
        'pimpinan_ttd',
        'pimpinan_id',
        'tanggal_review',
        'reviewer_id',
        'alasan',
        'status_std'
    ];

    public function scopepimpinan($query, $value)
    {
        if ($value) {
            $query->where('app_surat_tugas_dinas.pimpinan_id', $value);
        }
    }

    public function scopedalam_kota($query, $value)
    {
        if ($value) {
            $query->where('app_surat_tugas_dinas.std_dk', $value);
        }
    }

    public function scopepencarian($query, $value)
    {
        if ($value) {
            $query->where('app_surat_tugas_dinas.nomor_std', 'like', '%' . $value . '%');
        }
    }

    public function scopestatus_std($query, $value)
    {
        if ($value) {
            $query->whereIn('app_surat_tugas_dinas.status_std', $value);
        }
    }

    public function scopepimpinan_id($query, $value)
    {
        if ($value) {
            $query->where('app_surat_tugas_dinas.pimpinan_id', $value);
        }
    }

    public function scopebulan($query, $value)
    {
        if ($value) {
            $query->whereMonth('app_surat_tugas_dinas.created_at', $value);
        }
    }

    public function scopetahun($query, $value)
    {
        if ($value) {
            $query->whereYear('app_surat_tugas_dinas.created_at', $value);
        }
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function pegawai()
    {
        return $this->belongsToMany(Pegawai::class, 'app_surat_tugas_dinas_has_pegawai', 'surat_tugas_dinas_id');
    }

    public function departemen()
    {
        return $this->hasOne(Departemen::class, 'id', 'departemen_id');
    }

    public function pimpinan_ttd()
    {
        return $this->hasOne(Pimpinan::class, 'id', 'pimpinan_ttd');
    }

    public function reviwer()
    {
        return $this->hasOne(User::class, 'id', 'reviewer_id');
    }
}
