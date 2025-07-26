<?php

namespace App\Models;

use App\Models\Campaigns\Campaign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'campaign_id',
        'box_id',
        'type',
        'direction',
        'amount',
        'pdf_url',
    ];


    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    //
    protected static function booted()
    {
        static::created(function ($transaction) {
            $pdf = Pdf::loadView('pdf.transaction_receipt', ['transaction' => $transaction]);

            $fileName = 'receipts/transaction_' . $transaction->id . '.pdf';

            Storage::disk('public')->put($fileName, $pdf->output());

            $transaction->update(['pdf_url' => $fileName]);
        });
    }

}
