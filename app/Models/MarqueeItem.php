<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarqueeItem extends Model
{
    use HasFactory;

    protected $fillable = ['image', 'nom', 'spec', 'sort_order'];
}