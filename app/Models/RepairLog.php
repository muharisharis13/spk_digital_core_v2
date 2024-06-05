<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairLog extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "repair_log_id";


    public function repair()
    {
        return $this->belongsTo(Repair::class, "repair_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
