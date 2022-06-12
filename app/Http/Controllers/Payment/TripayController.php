<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TripayController extends Controller
{
    
    // lihat private key, merchantcode pada simulator->merchant->detail pada web tripay, lalu masukkan ke .ENV dan .ENV.example (untuk github)
    
    // lalu buat file tripay.php pada config, kemudian masukkan kode pada .ENV tadi, di file tripay.php
    
    // method mengambil channel pembayaran
    public function getPaymentChannels()
    {
        // ambil code dari config tripay.php
        $apiKey = config('tripay.api_key');
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/merchant/payment-channel',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
            CURLOPT_FAILONERROR    => false
        ));
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        
        // dd(json_decode($response)) -> ambil object 'data' nya
        $response = json_decode($response)->data;
        // dd($response);
        return $response ? $response : $error;
        
    }
    
    // membuat transaksi baru atau melakukan generate kode pembayaran dengan menggunakan API request transaksi tripay
    // parameter $method, $book akan diberikan pada controller Transaction untuk menerima data buku dan code channel bank
    public function requestTransaction($method, $book)
    {
        // copy code contoh request pada request transaksi di tripay, lalu sesuai kan dengan kebutuhan
        
        // ambil code dari config tripay.php
        $apiKey       =  config('tripay.api_key');
        $privateKey   = config('tripay.private_key');
        $merchantCode = config('tripay.merchant_code'); 
        $merchantRef  = 'PX-' . time(); // merchantref di isi sesuka hati
        
        // mengambil user yang login
        $user = auth()->user();
        
        $data = [
            'method'         => $method,
            'merchant_ref'   => $merchantRef,
            'amount'         => $book->price,
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'order_items'    => [
                [
                    'name'        => $book->title,
                    'price'       => $book->price,
                    'quantity'    => 1,
                    'image_url'   => asset('storage/bank/'. $book->code. '.png'),
                ],
            ],
            'expired_time' => (time() + (24 * 60 * 60)), // 24 jam
            'signature'    => hash_hmac('sha256', $merchantCode.$merchantRef.$book->price, $privateKey)
        ];
        
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data)
        ]);
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        
        // dd(json_decode($response)) -> ambil object 'data' nya
        $response = json_decode($response)->data;
        // dd($response);
        return $response ? $response : $error;
        
        // response akan mengembalikan data, salah satu data yang diperlukan adalah 'reference' sehingga dapat mengembalikan detail transaksi
        
        // check website tripay->simulator->transaksi untuk melihat apakah respond berhasil diterima
    }
    
    
     // parameter $reference akan diberikan pada controller Transaction untuk, lalu method ini akan mengembalikan nilai json detail transaksi
    public function detailTransaction($reference)
    {
        
        $apiKey = config('tripay.api_key');
        
        $payload = [
            'reference'	=> $reference,
        ];
        
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/detail?'.http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
            CURLOPT_FAILONERROR    => false,
        ]);
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        
        curl_close($curl);

         // dd(json_decode($response)) -> ambil object 'data' nya
         $response = json_decode($response)->data;
        //  dd($response);
        
        return $response ? $response : $error;
        
        
    }
    
}    