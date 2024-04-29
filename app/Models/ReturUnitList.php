<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturUnitList extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "retur_unit_list_id";
}
