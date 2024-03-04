<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = ['subject', 'description', 'start_date', 'due_date', 'status', 'priority'];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
