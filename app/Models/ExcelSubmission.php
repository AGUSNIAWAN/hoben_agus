<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'excel_module_id',
        'user_id',
        'period',
        'submission_date',
        'file_path',
        'original_name',
        'file_size',
        'status',
        'notes',
        'analysis_data',
    ];

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '-';
        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        }
        return number_format($this->file_size / 1024, 1) . ' KB';
    }

    protected $casts = [
        'submission_date' => 'date',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function module()
    {
        return $this->belongsTo(ExcelModule::class, 'excel_module_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
