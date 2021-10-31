<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit');
        $status = $request->input('status');

        if($id){
            $transaction = Transaction::with(['items.product'])->find($id);

            if($transaction){
                return ResponseFormatter::success(
                    $transaction,
                    'Data berhasil ditemukan'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data tidak ditemukan',
                    404
                );
            }
        }

        $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        if($status){
            $transaction->where('status', $status);
        }

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data berhasil ditemukan'
        ); 
    }

    public function checkout(Request $request){
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exist:Products.id',
            'total_prize' => 'required',
            'shipping_prize' => 'required',
            'status' => 'required|in:PENDING,SUKSES,FAILED,SHIPPING,SHIPPED',
        ]);

        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'address' => $request->address,
            'total_prize' => $request->total_prize,
            'shipping_prize' => $request->shipping_prize,
            'status' => $request->status,
        ]);

        foreach ($request->items as $product) {
            TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity' => $product['quantity']
            ]);
        }

        return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi Berhasil');
    }
}
