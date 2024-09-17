<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPagto extends Model
{
    use HasFactory;
    protected $fillable = ["id",'cod',"forma_pagto"];
}
