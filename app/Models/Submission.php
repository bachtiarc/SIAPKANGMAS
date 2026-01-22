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
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Mapping Bidang/Balai to Code
     */
    private static $bidangCodes = [
        'Sekretariat' => '01',
        'Bidang Perdagangan Dalam Negeri' => '02',
        'Bidang Perdagangan Luar Negeri' => '03',
        'Bidang Standarisasi Dan Perlindungan Konsumen' => '04',
        'Bidang Industri Agro' => '05',
        'Bidang Industri Non Agro' => '06',
        'Balai Pengujian Dan Sertifikasi Mutu Barang Surakarta' => '07',
        'Balai Pengujian Dan Sertifikasi Mutu Barang Semarang' => '08',
        'Balai Industri Produk Tekstil Dan Alas Kaki' => '09',
        'Balai Industri Kreatif Digital Dan Kemasan' => '10',
        'Balai Industri Logam Dan Kayu' => '11',
    ];

    /**
     * Mapping Jabatan to Code
     */
    private static $jabatanCodes = [
        // Sekretariat (01)
        'Kasubbag Program' => '101',
        'Kasubbag Keuangan' => '102',
        'Kasubbag Umum dan Kepegawaian' => '103',
        'Subbag Program' => '104',
        'Subbag Keuangan' => '105',
        'Subbag Umum dan Kepegawaian' => '106',
        
        // Bidang Perdagangan Dalam Negeri (02)
        'Kepala Seksi Distribusi dan Logistik' => '201',
        'Kepala Seksi Promosi dan Informasi Pasar' => '202',
        'Kepala Seksi Pengembangan Pasar dan Usaha Dagang Kecil Menengah' => '203',
        'Seksi Distribusi dan Logistik' => '204',
        'Seksi Promosi dan Informasi Pasar' => '205',
        'Seksi Pengembangan Pasar dan Usaha Dagang Kecil Menengah' => '206',
        
        // Bidang Perdagangan Luar Negeri (03)
        'Kepala Seksi Ekspor Dan Impor' => '301',
        'Kepala Seksi Promosi Dan Kerjasama Perdagangan Luar Negeri' => '302',
        'Kepala Seksi Informasi Dan Analisis Pasar' => '303',
        'Seksi Ekspor Dan Impor' => '304',
        'Seksi Promosi Dan Kerjasama Perdagangan Luar Negeri' => '305',
        'Seksi Informasi Dan Analisis Pasar' => '306',
        
        // Bidang Standarisasi Dan Perlindungan Konsumen (04)
        'Kepala Seksi Perlindungan Konsumen' => '401',
        'Kepala Seksi Tertib Niaga' => '402',
        'Kepala Seksi Standarisasi Industri' => '403',
        'Seksi Perlindungan Konsumen' => '404',
        'Seksi Tertib Niaga' => '405',
        'Seksi Standarisasi Industri' => '406',
        
        // Bidang Industri Agro (05)
        'Kepala Seksi Pengembangan Sdm Dan Inovasi Industri Agro' => '501',
        'Kepala Seksi Pengembangan Sarana Dan Prasarana Industri Agro' => '502',
        'Kepala Seksi Pengendalian Dan Informasi Industri Agro' => '503',
        'Seksi Pengembangan Sdm Dan Inovasi Industri Agro' => '504',
        'Seksi Pengembangan Sarana Dan Prasarana Industri Agro' => '505',
        'Seksi Pengendalian Dan Informasi Industri Agro' => '506',
        
        // Bidang Industri Non Agro (06)
        'Kepala Seksi Pengembangan SDM, Kreativitas, dan Inovasi Industri Non Agro' => '601',
        'Kepala Seksi Pengembangan Sarana dan Prasarana Industri Non Agro' => '602',
        'Kepala Seksi Pengendalian dan Informasi Industri Non Agro' => '603',
        'Seksi Pengembangan SDM, Kreativitas, dan Inovasi Industri Non Agro' => '604',
        'Seksi Pengembangan Sarana dan Prasarana Industri Non Agro' => '605',
        'Seksi Pengendalian dan Informasi Industri Non Agro' => '606',
        
        // Balai Pengujian Dan Sertifikasi Mutu Barang Surakarta (07)
        'Kepala Sub Bagian Tata Usaha' => '701',
        'Kepala Seksi Pelayanan Teknis Pengujian Dan Kalibrasi' => '702',
        'Kepala Seksi Pengembangan Jasa Pengujian Dan Kalibrasi' => '703',
        'Sub Bagian Tata Usaha' => '704',
        'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi' => '705',
        'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi' => '706',
        
        // Balai Pengujian Dan Sertifikasi Mutu Barang Semarang (08)
        // Kepala Sub Bagian Tata Usaha => '801' (handled by context)
        
        // Balai Industri Produk Tekstil Dan Alas Kaki (09)
        // Kepala Sub Bagian Tata Usaha => '901' (handled by context)
        'Kepala Seksi Pengembangan Produk Tekstil' => '902',
        'Kepala Seksi Pengembangan Produk Alas Kaki' => '903',
        // Sub Bagian Tata Usaha => '904' (handled by context)
        'Seksi Pengembangan Produk Tekstil' => '905',
        'Seksi Pengembangan Produk Alas Kaki' => '906',
        
        // Balai Industri Kreatif Digital Dan Kemasan (10)
        // Kepala Sub Bagian Tata Usaha => '1001' (handled by context)
        'Kepala Seksi Industri Kreatif Digital' => '1002',
        'Kepala Seksi Pengembangan Kemasan' => '1003',
        // Sub Bagian Tata Usaha => '1004' (handled by context)
        'Seksi Industri Kreatif Digital' => '1005',
        'Seksi Pengembangan Kemasan' => '1006',
        
        // Balai Industri Logam Dan Kayu (11)
        // Kepala Sub Bagian Tata Usaha => '1101' (handled by context)
        'Kepala Seksi Pelayanan Jasa Keteknikan' => '1102',
        'Kepala Seksi Penerapan Dan Rekayasa' => '1103',
        // Sub Bagian Tata Usaha => '1104' (handled by context)
        'Seksi Pelayanan Jasa Keteknikan' => '1105',
        'Seksi Penerapan Dan Rekayasa' => '1106',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {
            $user = $submission->user;
            
            // Generate ticket_id dengan format lengkap
            $submission->ticket_id = self::generateTicketId($user);
        });
    }

    /**
     * Generate ticket ID dengan format: PI.01.106.12012026_010
     * Format: PI.XX.YYY.DDMMYYYY_NNN
     * - PI: Permohonan Informasi
     * - XX: Kode Bidang/Balai
     * - YYY: Kode Sub Bagian/Jabatan
     * - DDMMYYYY: Tanggal pengajuan
     * - NNN: Nomor urut bulan ini
     */
    private static function generateTicketId($user)
    {
        $prefix = 'PI'; // Permohonan Informasi
        
        // Get bidang code
        $bidangCode = self::$bidangCodes[$user->bidang] ?? '00';
        
        // Get jabatan code (with context-aware handling)
        $jabatanCode = self::getJabatanCode($user->bidang, $user->jabatan);
        
        // Get date in format: ddmmyyyy
        $date = date('dmY'); 
        
        // Get sequence number for this month
        $sequence = self::getMonthlySequence();
        
        return "{$prefix}.{$bidangCode}.{$jabatanCode}.{$date}_{$sequence}";
    }

    /**
     * Get jabatan code with bidang context
     */
    private static function getJabatanCode($bidang, $jabatan)
    {
        // Special handling for duplicate jabatan names across different balai
        if ($jabatan === 'Kepala Sub Bagian Tata Usaha') {
            $bidangCode = self::$bidangCodes[$bidang] ?? '00';
            return $bidangCode . '01'; // 701, 801, 901, 1001, 1101
        }
        
        if ($jabatan === 'Sub Bagian Tata Usaha') {
            $bidangCode = self::$bidangCodes[$bidang] ?? '00';
            return $bidangCode . '04'; // 704, 804, 904, 1004, 1104
        }
        
        // Normal lookup
        return self::$jabatanCodes[$jabatan] ?? '000';
    }

    /**
     * Get monthly sequence number
     * Returns format: 001, 002, 010, etc.
     */
    private static function getMonthlySequence()
    {
        // Count submissions in current month
        $count = self::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
        
        // Increment by 1
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

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $status = strtolower($this->status);

        // SELESAI = HIJAU
        if (in_array($status, ['completed', 'selesai'])) {
            return 'bg-green-100 text-green-800';
        }

        // DIPROSES = KUNING (pending, in_progress, on_progress, diproses)
        if (in_array($status, ['pending', 'in_progress', 'on_progress', 'diproses'])) {
            return 'bg-yellow-100 text-yellow-800';
        }

        // DITOLAK = MERAH
        if (in_array($status, ['rejected', 'ditolak'])) {
            return 'bg-red-100 text-red-800';
        }

        // Default
        return 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $status = strtolower($this->status);

        // SELESAI
        if (in_array($status, ['completed', 'selesai'])) {
            return 'Selesai';
        }

        // DIPROSES
        if (in_array($status, ['pending', 'in_progress', 'on_progress', 'diproses'])) {
            return 'Diproses';
        }

        // DITOLAK
        if (in_array($status, ['rejected', 'ditolak'])) {
            return 'Ditolak';
        }

        // Default
        return ucfirst($status);
    }

    /**
     * Get the documents associated with this submission
     */
    public function documents()
    {
        return $this->hasMany(SubmissionDocument::class);
    }
}