<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';
    protected $fillable = ['id', 'name'];

    public $incrementing = false; // Para UUID
    protected $keyType = 'string';

    public function fields()
    {
        return $this->hasMany(Field::class, 'form_id');
    }

    public function fillings()
    {
        return $this->hasMany(FormFilling::class, 'form_id');
    }
}
