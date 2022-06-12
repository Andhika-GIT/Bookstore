<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Book;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

     // buat relasi ke tabel lain
     public function user()
     {
         // satu transaksi hanya bisa memiliki satu user (belongs To)
         return $this->belongsTo(User::class);
     }
 
     public function book()
     {
         // satu transaksi hanya bisa memiliki satu buku (belongs To)
         return $this->belongsTo(Book::class);
     }
}
