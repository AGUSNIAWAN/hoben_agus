<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    protected $fillable = [
        'user_id',
        'outlet_id',
        'task_id',
        'date',
        'shift',
        'division',
        'checklist_data',
        'photo_path',
    ];

    protected $casts = [
        'date' => 'date',
        'checklist_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
