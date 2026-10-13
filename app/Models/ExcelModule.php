<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'period',
        'drive_link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function submissions()
    {
        return $this->hasMany(ExcelSubmission::class);
    }
}
