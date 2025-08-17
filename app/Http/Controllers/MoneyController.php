<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\Campaigns\Campaign;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MoneyController extends Controller
{
    public function getBoxStats($box_id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        // التحقق من وجود الصندوق
        $box = Box::find($box_id);
        if (!$box) {
            return response()->json([
                'message' => 'الصندوق غير موجود'
            ], 404);
        }

        $locale = app()->getLocale();
        $boxName = $locale === 'ar' ? $box->name_ar : $box->name_en;

        // مجموع التبرعات الداخلة للصندوق
        $totalDonations = Transaction::where('box_id', $box_id)
            ->where('type', 'donation')
            ->where('direction', 'in')
            ->sum('amount');

        // مجموع المصروفات الخارجة من الصندوق
        $totalExchanges = Transaction::where('box_id', $box_id)
            ->where('type', 'exchange')
            ->where('direction', 'out')
            ->sum('amount');

        return response()->json([
            'box_id'          => $box->id,
            'name'            => $boxName,
            'current_balance' => $box->balance,
            'total_donations' => $totalDonations,
            'total_exchanges' => $totalExchanges,
        ]);
    }

    public function getCampaignStats($campaign_id)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        // التحقق من وجود الحملة
        $campaign = Campaign::find($campaign_id);
        if (!$campaign) {
            return response()->json([
                'message' => 'الحملة غير موجودة'
            ], 404);
        }

        // اللغة المختارة
        $locale = app()->getLocale();
        $campaignName = $locale === 'ar' ? $campaign->title_ar : $campaign->title_en;

        // مجموع التبرعات الداخلة للحملة
        $totalDonations = Transaction::where('campaign_id', $campaign_id)
            ->where('type', 'donation')
            ->where('direction', 'in')
            ->sum('amount');

        // مجموع المصروفات الخارجة من الحملة
        $totalExchanges = Transaction::where('campaign_id', $campaign_id)
            ->where('type', 'exchange')
            ->where('direction', 'out')
            ->sum('amount');

        // الرصيد الحالي = التبرعات - المصروفات
        $currentBalance = $totalDonations - $totalExchanges;

        return response()->json([
            'campaign_id'     => $campaign->id,
            'name'            => $campaignName,
            'total_donations' => $totalDonations,
            'total_exchanges' => $totalExchanges,
            'current_balance' => $currentBalance,
        ]);
    }

}
