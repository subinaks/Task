<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    
    protected $fillable = ['subject', 'note', 'attachments', 'task_id'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
