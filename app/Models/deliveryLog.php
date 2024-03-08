<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deliveryLog extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_log_id";

    public function delivery()
    {
        return $this->belongsTo(delivery::class, "delivery_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
