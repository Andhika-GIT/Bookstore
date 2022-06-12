@extends('layouts.frontend')

@section('content')
<a href="{{ url()->previous() }}" class="mt-6 text-sm text-primary hover:text-black transition duration-300 ease-in-out inline-block">
    < Kembali
</a>
<h2 class="text-primary font-medium text-lg mt-3">Choose your payment method</h2>
<div class="grid grid-cols-5 gap-3 mt-6">
    <div class="col-span-4">
        <div class="grid grid-cols-4 gap-3">
            @foreach ($channels as $channel)
            {{-- jika status active == true, tampilkan data  --}}
            @if ($channel->active)
            {{-- request tripay membutuhkan method POST --}}
            <form action="{{ route('transaction.store') }}" method="post">
                @csrf
                {{-- input hidden agar mendapatkan id dari table books dan code dari bank dari variabel channel yang berisi info bank --}}
                <input type="hidden" name="book_id" value="{{ $book->id }}">
                <input type="hidden" name="method" value="{{ $channel->code }}">
                <button type="submit" class="bg-white p-5 h-32 w-36 rounded-md shadow-soft flex items-center">
                    <div>
                        <img src="{{ asset('storage/bank/'. $channel->code. '.png') }}" class="w-full" alt="">
                        <p class="mt-3 text-xs text-gray-600">Pay with {{ $channel->name }}</p>
                    </div>
                </button>
            </form>              
            @endif
            @endforeach
        </div>
    </div>
    <div class="col-span-1">
        <img class="object-contain rounded-md" src="{{ asset('storage/' . $book->cover_image) }}" alt="">
        <p class="mt-3 text-primary text-lg">{{ $book->title }}</p>
        <p class="text-sm font-bold text-primary mt-1">Rp.{{ number_format($book->price) }}</p>
    </div>
</div>
@endsection