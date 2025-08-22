<?php

namespace App\Models;

use App\Jobs\GenerateTransactionPDF;
use App\Models\Campaigns\Campaign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;


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
    /*
    protected static function booted()
    {
        static::created(function ($transaction) {
            $pdf = Pdf::loadView('pdf.transaction_receipt', ['transaction' => $transaction]);
            $fileName = 'receipts/transaction_' . $transaction->id . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());
            $transaction->update(['pdf_url' => $fileName]);
        });
    }
    */
    /*
    protected static function booted()
    {
        static::created(function ($transaction) {
            try {
                // إعداد mPDF مع دعم الخط العربي
                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font' => 'Amiri',
                    'fontDir' => array_merge($fontDirs, [storage_path('fonts')]),
                    'fontdata' => $fontData + [
                            'amiri' => [
                                'R' => 'Amiri-Regular.ttf',
                                'B' => 'Amiri-Bold.ttf',
                            ]
                        ],
                    'autoScriptToLang' => true, // مهم للعربية
                    'autoLangToFont' => true,   // مهم للعربية
                ]);

                // جلب الـ Blade view كـ HTML
                $html = view('pdf.transaction_receipt', ['transaction' => $transaction])->render();

                $mpdf->WriteHTML($html);

                // تحديد مكان التخزين
                $fileName = 'receipts/transaction_' . $transaction->id . '.pdf';

                if (!Storage::disk('public')->exists('receipts')) {
                    Storage::disk('public')->makeDirectory('receipts');
                }

                // حفظ الملف
                Storage::disk('public')->put($fileName, $mpdf->Output('', 'S'));

                // تحديث رابط PDF في DB
                $transaction->update(['pdf_url' => $fileName]);

            } catch (\Exception $e) {
                \Log::error('PDF Generation Error: ' . $e->getMessage());
            }
        });
    }
    */
    protected static function booted()
    {
        static::created(function ($transaction) {
            GenerateTransactionPDF::dispatchSync($transaction);

        });
    }


}
