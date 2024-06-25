<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairReturnLog extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "repair_return_log_id";

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
