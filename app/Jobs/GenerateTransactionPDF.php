<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class GenerateTransactionPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle()
    {
        try {
            // إعداد mPDF مع دعم العربي
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
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
            ]);

            $html = view('pdf.transaction_receipt', ['transaction' => $this->transaction])->render();

            $mpdf->WriteHTML($html);

            $fileName = 'receipts/transaction_' . $this->transaction->id . '.pdf';

            if (!Storage::disk('public')->exists('receipts')) {
                Storage::disk('public')->makeDirectory('receipts');
            }

            Storage::disk('public')->put($fileName, $mpdf->Output('', 'S'));

            // تحديث رابط PDF
            $this->transaction->update(['pdf_url' => $fileName]);

        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
        }
    }
}
