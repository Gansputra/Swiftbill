<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $guarded = [];

    public function shift()
    {
        return $this->belongsTo(CashShift::class, 'shift_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
