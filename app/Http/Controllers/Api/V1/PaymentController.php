<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $payments = Payment::whereHas('order.user', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->get();

        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        DB::beginTransaction();
        try {
            // Validasi
            $user = auth()->user();
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $order = $user->orders()->findOrFail($request->order_id);
            if ($order->status !== 'pending') {
                return response()->json(['error' => 'Order is not in pending status'], 400);
            }

            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                return response()->json(['error' => 'Payment already exists for this order'], 400);
            }

            // Simpan pembayaran
            $newPayment = Payment::create([
                'order_id' => $order->id,
                'paid_at' => now()->toDateTimeString(),
                'transaction_status' => 'pending',
            ]);

            // Kirim ke Midtrans (atau proses lain)
            $midtransPayload = [
                'transaction_details' => [
                    'order_id' => $order->id . '-' . uniqid(),
                    'gross_amount' => round($order->amount),
                ],
                'customer_details' => [
                    'email' => $user->email,
                    'first_name' => $user->name,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($midtransPayload);

            // Simpan token (opsional)
            $newPayment->update([
                'snap_token' => $snapToken,
            ]);

            DB::commit(); // ✅ Semua sukses

            return response()->json([
                'message' => 'Payment created',
                'payment' => $newPayment,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack(); // ❌ Ada error, batalkan semua
            return response()->json([
                'error' => 'Transaction failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $payment = Payment::findOrFail($id);
        if ($payment->order->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($payment);
    }

    public function generateInvoice($orderId, $user, $plan)
    {
        $invoiceNumber = 'INV-' . strtoupper(uniqid());
        $data = [
            'invoice_number' => $invoiceNumber,
            'date' => now()->format('d M Y'),
            'transaction_status' => 'success',
            'user_name' => $user->name,
            'user_email' => $user->email,
            'order_id' => $orderId,
            'plan_name' => $plan->name,
            'price' => $plan->price,
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);
        Storage::disk('public')->put("invoices/{$user->email}/{$invoiceNumber}.pdf", $pdf->output());
        $invoice = Invoice::create([
            'order_id' => $orderId,
            'invoice_number' => $invoiceNumber,
            'pdf_url' => asset("storage/invoices/{$user->email}/{$invoiceNumber}.pdf"),
        ]);
        // dd($invoice); // Debugging line to check the invoice creation
        return $invoice;
    }

    public function callback(Request $request)
    {
        $orderId = explode('-', $request->order_id)[0];
        $order = Order::find($orderId);
        // dd($order);
        $user = $order->user;
        $plan = $order->plan;

        if ($request->transaction_status === 'settlement') {
            $payment = Payment::where('order_id', $orderId)->first();
            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $payment->update([
                'transaction_status' => 'success',
                'paid_at' => now(),
            ]);
            $order->update(['status' => 'completed']);

            // Generate invoice
            $this->generateInvoice($orderId, $user, $plan);

            return response()->json(['message' => 'Payment successful', 'payment' => $payment], 200);
        } else {
            return response()->json(['message' => 'Payment status: ' . $request->transaction_status], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
