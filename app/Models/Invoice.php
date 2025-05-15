<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    // One To Many 
    public function items(){
        return $this->hasMany(InvoiceItem::class);
    }

    // One To One
    public function payment(){
       return $this->hasMany(InvoicePayment::class);
    }

}
