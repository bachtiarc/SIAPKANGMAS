<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'category_id',
        'title',
        'subject',
        'description',
        'document_path',
        'status',
        'admin_notes',
        'handled_by',
        'submitted_at',
        'completed_at',
        'tujuan_permohonan',
        'cara_penyampaian',
        'datang_langsung_opsi',
        'diproses_bidang',
        'diproses_kelompok',
        'diproses_oleh',
        'archived_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'datang_langsung_opsi' => 'array',
        'archived_at' => 'datetime',
    ];

    /**
     * Mapping Bidang/Balai to Code (untuk PEGAWAI)
     */
    private static $bidangCodes = [
        'Sekretariat' => '01',
        'Bidang Pembangunan Sumber Daya Industri Dan Perwilayahan Industri' => '02',
        'Bidang Pemberdayaan Industri' => '03',
        'Bidang Pengembangan Sarana Prasarana, Pengawasan Dan Pengendalian Industri' => '04',
        'Bidang Perdagangan Dalam Negeri' => '05',
        'Bidang Perdagangan Luar Negeri' => '06',
        'Balai Industri Logam dan Kayu (BILK) Kelas A' => '07',
        'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Surakarta Kelas A' => '08',
        'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Semarang' => '09',
        'Balai Industri Produk Tekstil dan Alas Kaki (BIPTAK)' => '10',
        'Balai Industri Kreatif Digital dan Kemasan Kelas A (BIKDK)' => '11',
    ];

    /**
     * Mapping Jabatan to Code (untuk PEGAWAI)
     */
    private static $jabatanCodes = [
        // Sekretariat (01)
        'Sekeretaris' => '101',
        'Kepala Sub Bagian Umum dan Kepegawaian' => '102',
        'Kepala Sub Bagian Keuangan' => '103',
        'Kepala Sub Bagian Program' => '104',
        'Sub Bagian Program' => '105',
        'Sub Bagian Keuangan' => '106',
        'Sub Bagian Umum dan Kepegawaian' => '107',

        // Bidang Pembangunan Sumber Daya Industri Dan Perwilayahan Industri (02)
        'Kepala Bidang Pembangunan Sumber Daya Industri Dan Perwilayahan Industri' => '201',
        'Ketua Kelompok Kerja Pengembangan Perwilayahan Industri' => '202',
        'Ketua Kelompok Kerja Pengembangan Teknologi Industri' => '203',
        'Ketua Kelompok Kerja Pengembangan SDM Industri' => '204',
        'Kelompok Kerja Pengembangan Perwilayahan Industri' => '205',
        'Kelompok Kerja pengembangan Teknologi Industri' => '206',
        'Kelompok Kerja Pengembangan SDM Industri' => '207',

        // Bidang Pemberdayaan Industri (03)
        'Kepala Bidang Pemberdayaan Industri' => '301',
        'Ketua Kelompok Kerja Pengembangan Industri' => '302',
        'Ketua Kelompok Kerja Promosi dan Kerja Sama Industri' => '303',
        'Ketua Kelompok Kerja Industri Hijau' => '304',
        'Kelompok Kerja Pengembangan Industri' => '305',
        'Kelompok Kerja Promosi dan Kerja Sama Industri' => '306',
        'Kelompok Kerja Promosi dan Kerja Sama Industri' => '307',

        // Bidang Pengembangan Sarana Prasarana, Pengawasan Dan Pengendalian Industri (04)
        'Kepala Bidang Pengembangan Sarana Prasarana, Pengawasan Dan Pengendalian Industri' => '401',
        'Ketua Kelompok Kerja Pengembangan Sarana Prasarana Industri' => '402',
        'Ketua Kelompok Kerja Pengawasan dan Pengendalian Industri' => '403',
        'Ketua Kelompok Kerja Data dan Informasi Industri' => '404',
        'Kelompok Kerja Pengembangan Sarana Prasarana Industri' => '405',
        'Kelompok Kerja Pengawasan dan Pengendalian Industri' => '406',
        'Kelompok Kerja Data dan Informasi Industri' => '407',

        // Bidang Perdagangan Dalam Negeri (05)
        'Kepala Bidang Perdagangan Dalam Negeri' => '501',
        'Ketua Kelompok Kerja Pengendalian Bapokting, Pengembangan Informasi dan Sarana Perdagangan' => '502',
        'Ketua Kelompok Kerja Promosi dan Kerjasama' => '503',
        'Ketua Kelompok Kerja Perlindungan Konsumen dan Tertib Niaga' => '504',
        'Kelompok Kerja Pengendalian Bapokting, Pengembangan Informasi dan Sarana Perdagangan' => '505',
        'Kelompok Kerja Promosi dan Kerjasama' => '506',
        'Kelompok Kerja Perlindungan Konsumen dan Tertib Niaga' => '507',

        // Bidang Perdagangan Luar Negeri (06)
        'Kepala Bidang Perdagangan Luar Negeri' => '601',
        'Ketua Kelompok Kerja Ekspor dan Impor' => '602',
        'Ketua Kelompok Kerja Promosi dan Kerjasama Perdagangan Luar Negeri' => '603',
        'Ketua Kelompok Kerja Informasi Dan Analisis Pasar' => '604',
        'Kelompok Kerja Ekspor dan Impor' => '605',
        'Kelompok Kerja Promosi dan Kerjasama Perdagangan Luar Negeri' => '606',
        'Kelompok Kerja Informasi Dan Analisis Pasar' => '607',

        // Balai Industri Logam dan Kayu (BILK) Kelas A (07)
        'Kepala Sub Bagian Tata Usaha' => '701',
        'Ketua Kelompok Kerja Pelayanan Jasa Keteknikan' => '702',
        'Ketua Kelompok Kerja Penerapan dan Rekayasa' => '703',
        'Kelompok Kerja Pelayanan Jasa Keteknikan' => '704',
        'Kelompok Kerja Penerapan dan Rekayasa' => '705',
        'Kelompok Jabatan Fungsional' => '706',

        // Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Surakarta Kelas A (08)
        'Kepala Sub Bagian Tata Usaha' => '801',
        'Ketua Kelompok Kerja Pelayanan Teknis Pengujian dan Kalibrasi' => '802',
        'Ketua Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi' => '803',
        'Kelompok Kerja Pelayanan Teknis Pengujian dan Kalibrasi' => '804',
        'Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi' => '805',
        'Kelompok Jabatan Fungsional' => '806',

        // Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Semarang (09)
        'Kepala Sub Bagian Tata Usaha' => '901',
        'Ketua Kelompok Kerja Produk Alas Kaki' => '902',
        'Ketua Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi' => '903',
        'Kelompok Kerja Pengembangan Produk Alas Kaki' => '904',
        'Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi' => '905',
        'Kelompok Jabatan Fungsional' => '906',

        // Balai Industri Produk Tekstil dan Alas Kaki (BIPTAK) (10)
        'Kepala Sub Bagian Tata Usaha' => '1001',
        'Ketua Kelompok Kerja Pengembangan Produk Tekstil' => '1002',
        'Ketua Kelompok Kerja Pengembangan Produk Alas Kaki' => '1003',
        'Kelompok Kerja Pengembangan Produk Tekstil' => '1004',
        'Kelompok Kerja Pengembangan Produk Alas Kaki' => '1005',
        'Kelompok Jabatan Fungsional' => '1006',

        // Balai Industri Kreatif Digital dan Kemasan Kelas A (BIKDK) (11)
        'Kepala Sub Bagian Tata Usaha' => '1101',
        'Ketua Kelompok Kerja Industri Kreatif Digital' => '1102',
        'Ketua Kelompok Kerja Pengembangan Kemasan' => '1103',
        'Kelompok Kerja Industri Kreatif Digital' => '1105',
        'Kelompok Kerja Pengembangan Kemasan' => '1106',
        'Kelompok Jabatan Fungsional' => '1107',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {
            if (!empty($submission->ticket_id)) {
                return;
            }
            $user = $submission->user;

            if ($user && $user->user_type === 'pegawai') {
                $submission->ticket_id = self::generateTicketIdPegawai($user);
            } else {
                $submission->ticket_id = self::generateTicketIdMasyarakat($user);
            }
        });
    }

    private static function generateTicketIdPegawai($user)
    {
        $prefix = 'PI';

        $bidangCode = self::$bidangCodes[$user->bidang] ?? '00';

        $jabatanCode = self::getJabatanCode($user->bidang, $user->jabatan);

        $date = date('dmY');

        $sequence = self::getMonthlySequence();

        return "{$prefix}.{$bidangCode}.{$jabatanCode}.{$date}_{$sequence}";
    }

    private static function generateTicketIdMasyarakat($user)
    {
        $prefix = 'PI';
        $nik = $user->nik ?? '000000000000000000';
        $nikCode = substr($nik, 10, 6);

        $date = date('dmY');

        $sequence = self::getMonthlySequence();

        return "{$prefix}.{$nikCode}.{$date}_{$sequence}";
    }

    /**
     * Get jabatan code with bidang context (untuk PEGAWAI)
     */
    private static function getJabatanCode($bidang, $jabatan)
    {
        if ($jabatan === 'Kepala Sub Bagian Tata Usaha') {
            $bidangCode = self::$bidangCodes[$bidang] ?? '00';
            return $bidangCode . '01';
        }

        if ($jabatan === 'Sub Bagian Tata Usaha') {
            $bidangCode = self::$bidangCodes[$bidang] ?? '00';
            return $bidangCode . '04';
        }

        return self::$jabatanCodes[$jabatan] ?? '000';
    }

    /**
     * Get monthly sequence number
     * Returns format: 001, 002, 010, 020, etc.
     */
    private static function getMonthlySequence()
    {
        $count = self::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return $sequence;
    }

    /**
     * Get the user who created this submission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the admin handling this submission
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Get status histories
     */
    public function statusHistories()
    {
        return $this->morphMany(StatusHistory::class, 'trackable')->orderBy('created_at', 'desc');
    }

    public function getStatusBadgeAttribute()
    {
        $status = strtolower($this->status);

        if (in_array($status, ['completed', 'selesai'])) {
            return 'bg-green-100 text-green-800';
        }

        if (in_array($status, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])) {
            return 'bg-blue-100 text-blue-800';
        }

        if (in_array($status, ['pending', 'belum diproses'])) {
            return 'bg-yellow-100 text-yellow-800';
        }

        if (in_array($status, ['rejected', 'ditolak'])) {
            return 'bg-red-100 text-red-800';
        }

        return 'bg-gray-100 text-gray-800';
    }

    // Status Label
    public function getStatusLabelAttribute()
    {
        $status = strtolower($this->status);

        if (in_array($status, ['completed', 'selesai'])) {
            return 'Selesai';
        }

        if (in_array($status, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])) {
            return 'Diproses';
        }

        if (in_array($status, ['pending', 'belum diproses'])) {
            return 'Menunggu Diproses';
        }

        if (in_array($status, ['rejected', 'ditolak'])) {
            return 'Ditolak';
        }

        return ucfirst($status);
    }

    /**
     * Get the documents associated with this submission
     */
    public function documents()
    {
        return $this->hasMany(SubmissionDocument::class);
    }

    public function applicant()
    {
        return $this->hasOne(\App\Models\SubmissionApplicant::class);
    }

    public function scopeNotArchived($q)
    {
        return $q->whereNull('archived_at');
    }

    public function scopeArchived($q)
    {
        return $q->whereNotNull('archived_at');
    }
}