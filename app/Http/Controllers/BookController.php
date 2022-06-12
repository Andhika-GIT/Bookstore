<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\TripayController;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::latest()->get();
         return view('book.index', compact('books'));
    }

    public function show(Book $book)
    {
         return view('book.show', compact('book'));
    }

    public function checkout(Book $book)
    {
        $tripay = new TripayController();
        // variabel untuk melihat status active dari object 'data', didapatkan dari variabel $response yang dikirim pada tripaycontroller
        $channels = $tripay->getPaymentChannels();
        return view('book.checkout', compact('book', 'channels'));
    }
}
