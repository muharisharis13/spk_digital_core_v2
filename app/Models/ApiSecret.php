<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSecret extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $with = ["api_secret_id"];
}
