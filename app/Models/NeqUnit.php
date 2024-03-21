<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeqUnit extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "neq_unit_id";
}
