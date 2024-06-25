<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeqReturnUnit extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "neq_return_unit_id";


    public function neq_unit()
    {
        return $this->belongsTo(NeqUnit::class, "neq_unit_id");
    }

    public function neq_return()
    {
        return $this->belongsTo(NeqReturn::class, "neq_return_id");
    }
}
