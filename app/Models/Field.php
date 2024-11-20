<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $table = 'fields';
    protected $fillable = ['id', 'form_id', 'label', 'type', 'required', 'choices'];

    public $incrementing = false; // Para UUID
    protected $keyType = 'string';

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
