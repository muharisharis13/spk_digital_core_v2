<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndentInstansi extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "indent_instansi_id";
}
