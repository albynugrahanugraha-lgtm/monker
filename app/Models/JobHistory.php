<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobHistory extends Model
{
    protected $table = 'job_history';   // ← TAMBAH INI

    protected $fillable = ['job_id', 'user_id', 'status_lama', 'status_baru', 'keterangan'];

    public function job()  { return $this->belongsTo(Job::class); }
    public function user() { return $this->belongsTo(User::class); }
}