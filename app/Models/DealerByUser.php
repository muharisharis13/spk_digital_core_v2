<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerByUser extends Model
{
    use HasFactory,HasUuids;

    protected $guarded = [];
    
    protected $primaryKey = "dealer_by_user_id";

    protected $hidden = [
        "user_id"
    ];

    
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
