<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobReport extends Model
{
    protected $fillable = ['job_id', 'hasil', 'catatan', 'catatan_admin', 'dibuat_oleh'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}