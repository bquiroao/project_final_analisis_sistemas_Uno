<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoapNote extends Model
{
    protected $fillable = [
        'patient_name',
        'subjective',
        'objective',
        'assessment',
        'plan'
    ];
}
