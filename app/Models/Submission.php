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
        'full_ticket_number',
        'ticket_number',
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
        'Kepala Sub Bagian Tata Usaha' => '701', // BPSMB Surakarta
        'Kepala Seksi Pelayanan Teknis Pengujian Dan Kalibrasi' => '702',
        'Kepala Seksi Pengembangan Jasa Pengujian Dan Kalibrasi' => '703',
        'Sub Bagian Tata Usaha' => '704',
        'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi' => '705',
        'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi' => '706',
        
        // Balai Pengujian Dan Sertifikasi Mutu Barang Semarang (08)
        // 'Kepala Sub Bagian Tata Usaha' => '801', // Duplicate with 701
        // Use context from bidang to differentiate
        
        // Add more mappings as needed...
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {
            $user = $submission->user;
            
            $submission->ticket_id = self::generateTicketId($user);
            
            $submission->full_ticket_number = self::generateFullTicketNumber($user);
            
            $submission->ticket_number = $submission->full_ticket_number;
        });
    }

    /**
     * Generate SHORT ticket ID
     * Format: PI02_03_JAN26
     */
    private static function generateTicketId($user)
    {
        $prefix = 'PI'; 
        $day = date('d'); 
        $month = strtoupper(date('M')); 
        $year = date('y'); 
        
        // Get daily sequence number
        $today = date('Y-m-d');
        $count = self::whereDate('created_at', $today)->count();
        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT); 
        
        return "{$prefix}{$day}_{$sequence}_{$month}{$year}"; 
    }

    /**
     * Generate FULL ticket number with complex format
     * Format: PI.01.106.12012026_010
     */
    private static function generateFullTicketNumber($user)
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
            return $bidangCode . '01'; 
        }
        
        if ($jabatan === 'Sub Bagian Tata Usaha') {
            $bidangCode = self::$bidangCodes[$bidang] ?? '00';
            return $bidangCode . '04'; 
        }
        
        // Normal lookup
        return self::$jabatanCodes[$jabatan] ?? '000';
    }

    /**
     * Get monthly sequence number
     * Returns format: _001, _002, _010, etc.
     */
    private static function getMonthlySequence()
    {
        $currentMonth = date('Y-m');
        
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
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'in_progress' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
        ][$this->status] ?? 'Unknown';
    }

    /**
     * Get the documents associated with this submission
     */
    public function documents()
    {
        return $this->hasMany(SubmissionDocument::class);
    }
}