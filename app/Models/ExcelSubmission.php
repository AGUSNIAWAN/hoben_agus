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
        'status',
        'notes',
    ];

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
