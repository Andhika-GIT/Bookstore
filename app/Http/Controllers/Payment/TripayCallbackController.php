<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TripayCallbackController extends Controller
{
    // property variabel diluar method, tidak bisa menggunakan config(), oleh karena itu, copy langsung privatekey dari tripay
    protected $privateKey = 'VIu15-xod3b-lhEen-135uZ-qqu2i';
    
    public function handle(Request $request)
    {
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, $this->privateKey);
        
        if ($signature !== (string) $callbackSignature) {
            return 'Invalid signature';
        }
        
        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            return 'Invalid callback event, no action was taken';
        }
        
        $data = json_decode($json);
        // dd($data);
        $reference = $data->reference;
        
        // pembayaran sukses, lanjutkan proses sesuai sistem
        $transaction = Transaction::where('reference', $reference)
        ->where('status', 'UNPAID')
        ->first();
        
        if (! $transaction) {
            return 'Invoice not found or current status is not UNPAID';
        }
        
        if (intval($data->total_amount) !== (int) $transaction->total_amount) {
            return 'Invalid amount';
        }
        
        switch ($data->status) {
            case 'PAID':
                $transaction->update(['status' => 'PAID']);
                return response()->json(['success' => true]);
                
                case 'EXPIRED':
                    $transaction->update(['status' => 'UNPAID']);
                    return response()->json(['success' => true]);
                    
                    case 'FAILED':
                        $transaction->update(['status' => 'UNPAID']);
                        return response()->json(['success' => true]);
                        
                        default:
                        return response()->json(['error' => 'Unrecognized payment status']);
                    }
    }
}
            