<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'addres',
        'payment',
        'total_price',
        'shipping_price',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function items(){
        return $this->hasMany(TransactionItems::class, 'transactions_id', 'id');
    }
}
