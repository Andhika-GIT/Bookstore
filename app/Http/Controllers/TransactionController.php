<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\TripayController;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Transaction;

class TransactionController extends Controller
{
    
    //  menghandel method requestTransaction dari tripaycontroller
    public function store(Request $request)
    {
        // check token
        // dd(request()->all());

        // dapatkan data books dan code bank, dari id dan channel pada input hidden di checkout.blade.php 
        $book = Book::find($request->book_id);
        $method = $request->method;

        // memanggil tripaycontroller untuk di inisiasi ke variabel
        $tripay = new TripayController();
        // masukkan parameter $method dan $book, untuk data buku dan payment method yang dikirimkan (request) pada controller tripay
        // data json yang sudah di encode, akan di inisasi ke variabel $transaction
        $transaction = $tripay->requestTransaction($method, $book);
        

        // masukkan data transaksi ke tabel transactions (buat model dan migration transactions terlebih dahulu)
        Transaction::create([
            'user_id' => auth()->user()->id,
            'book_id' => $book->id,
            'reference' => $transaction->reference,
            'merchant_ref' => $transaction->merchant_ref,
            'total_amount' => $transaction->amount,
            'status' => $transaction->status
        ]);

        // ambil reference untuk mendapatkan respond detail transaksi (baca web tripay->developer->transaksi->detail transaksi )
        // dd($transaction->reference);
        return redirect()->route('transaction.show', [
            'reference' => $transaction->reference,
        ]);

    }

    // menghandel method detailTransaction dari controller Tripay
    // method show menerima data reference dari method store sebagai parameter 
    public function show($reference)
    {
        // dd($reference);

         // memanggil tripaycontroller untuk di inisiasi ke variabel
         $tripay = new TripayController();
        //  memasukkan reference ke method detailTransaction untuk mendapatkan data detail transaksi yang telah di encode dari json
         // data json yang sudah di encode, akan di inisasi ke variabel $detail
         $detail = $tripay->detailTransaction($reference);

        // lihat data transaction, sebagai patokan object pada view show.blade.php
        // dd($detail);
         
         return view('transaction.show' , compact('detail'));
    }
    


}
