<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderEmployee extends Model
{
    use HasFactory;
    protected $fillable = [
        'jobOrderID',
        'employeeID'
    ];
}
