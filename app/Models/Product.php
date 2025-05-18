<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $guarded = [];
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
