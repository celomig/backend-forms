<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormFilling extends Model
{
    use HasFactory;

    protected $table = 'form_fillings';
    protected $fillable = ['id', 'form_id', 'data'];

    public $incrementing = false; // Para UUID
    protected $keyType = 'string';

    protected $casts = [
        'data' => 'array', // Decodifica automaticamente o JSON
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
