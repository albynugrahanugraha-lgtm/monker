<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPhoto extends Model
{
    protected $fillable = ['job_id', 'nama_file', 'jenis_foto'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}