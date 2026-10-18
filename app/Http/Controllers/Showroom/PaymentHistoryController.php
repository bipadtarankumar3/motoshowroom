<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    /**
     * Display financial transactions and payments ledger for showroom staff.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = $showroom->payments()
            ->with(['user'])
            ->search($search)
            ->filterStatus($status)
            ->latest();

        $payments = $query->paginate(15)->withQueryString();

        $totalVolume = $showroom->payments()->where('status', PaymentStatus::Captured->value)->sum('amount');
        $totalTransactions = $showroom->payments()->count();
        $successfulCount = $showroom->payments()->where('status', PaymentStatus::Captured->value)->count();
        $failedCount = $showroom->payments()->where('status', PaymentStatus::Failed->value)->count();

        $statuses = PaymentStatus::cases();

        return view('showrooms.manage.payments.index', compact(
            'showroom',
            'payments',
            'totalVolume',
            'totalTransactions',
            'successfulCount',
            'failedCount',
            'statuses',
            'search',
            'status'
        ));
    }

    /**
     * Display individual transaction receipt details.
     */
    public function show(Showroom $showroom, Payment $payment): View
    {
        abort_if($payment->tenant_id !== $showroom->id, 404);

        $payment->load(['user', 'payable', 'refunds']);

        return view('showrooms.manage.payments.show', compact('showroom', 'payment'));
    }
}
