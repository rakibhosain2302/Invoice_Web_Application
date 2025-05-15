<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    protected $casts = [
        'paid_at' => 'datetime:d-M-Y H:i:s',
    ];
    protected $guarded = [];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
}
