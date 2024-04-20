<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasPermission extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'model_has_permissions';
    protected $guarded = [];

    // protected $primaryKey = "micro_finance_id";
}
