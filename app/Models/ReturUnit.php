<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturUnit extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "retur_unit_id";


    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }
    public function retur_unit_list()
    {
        return $this->hasMany(ReturUnitList::class, "retur_unit_id");
    }
    public function retur_unit_log()
    {
        return $this->hasMany(ReturUnitLog::class, "retur_unit_id");
    }
}
