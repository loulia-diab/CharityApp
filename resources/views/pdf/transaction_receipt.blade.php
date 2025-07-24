<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <style>
        /* خطوط تدعم العربية والإنجليزية */
        @font-face {
            font-family: 'DejaVuSans';
            src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'DejaVuSans', sans-serif;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            margin: 30px;
            color: #222;
            background-color: #fff;
        }
        h1 {
            text-align: center;
            font-weight: 700;
            color: #222;
            border-bottom: 3px solid #222;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 12px 15px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 14px;
        }
        th {
            background-color: #e9f2fe;
            color: #222;
            font-weight: 600;
            width: 35%;
        }
        td {
            background-color: #f9faff;
        }
        footer {
            font-size: 12px;
            color: #222;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            position: fixed;
            bottom: 30px;
            width: 100%;
        }
    </style>
</head>
<body>
<h1>{{ app()->getLocale() === 'ar' ? 'وصل المعاملة' : 'Transaction Receipt' }}</h1>

<table>
    @php
        $locale = app()->getLocale();
        $types = [
            'donation' => $locale === 'ar' ? 'تبرع' : 'Donation',
            'exchange' => $locale === 'ar' ? 'مصروف' : 'Exchange',
            'recharge' => $locale === 'ar' ? 'شحن' : 'Recharge',
        ];
        $directions = [
            'in' => $locale === 'ar' ? 'وارد' : 'In',
            'out' => $locale === 'ar' ? 'صادر' : 'Out',
        ];
    @endphp

    <tr>
        <th>{{ $locale === 'ar' ? 'رقم المعاملة' : 'Transaction ID' }}</th>
        <td>{{ $transaction->id }}</td>
    </tr>
    <tr>
        <th>{{ $locale === 'ar' ? 'نوع العملية' : 'Transaction Type' }}</th>
        <td>{{ $types[$transaction->type] ?? $transaction->type }}</td>
    </tr>
    <tr>
        <th>{{ $locale === 'ar' ? 'الاتجاه' : 'Direction' }}</th>
        <td>{{ $directions[$transaction->direction] ?? $transaction->direction }}</td>
    </tr>
    <tr>
        <th>{{ $locale === 'ar' ? 'المبلغ' : 'Amount' }}</th>
        <td>{{ number_format($transaction->amount, 2) }} {{ $locale === 'ar' ? 'ل.س' : 'SYP' }}</td>
    </tr>
    <tr>
        <th>{{ $locale === 'ar' ? 'تاريخ المعاملة' : 'Transaction Date' }}</th>
        <td>{{ $transaction->created_at->translatedFormat($locale === 'ar' ? 'd/m/Y H:i' : 'Y-m-d H:i') }}</td>
    </tr>
    @if($transaction->user)
        <tr>
            <th>{{ $locale === 'ar' ? 'المستخدم' : 'User' }}</th>
            <td>{{ $transaction->user->name }}</td>
        </tr>
    @endif
    @if($transaction->admin)
        <tr>
            <th>{{ $locale === 'ar' ? 'المسؤول' : 'Admin' }}</th>
            <td>{{ $transaction->admin->name }}</td>
        </tr>
    @endif
    @if($transaction->campaign)
        <tr>
            <th>{{ $locale === 'ar' ? 'الحملة' : 'Campaign' }}</th>
            <td>{{ $locale === 'ar' ? $transaction->campaign->title_ar ?? $transaction->campaign->title_en : $transaction->campaign->title_en }}</td>
        </tr>
    @endif
    @if($transaction->box)
        <tr>
            <th>{{ $locale === 'ar' ? 'الصندوق' : 'Box' }}</th>
            <td>{{ $transaction->box->name }}</td>
        </tr>
    @endif
</table>

<footer>
    {{ $locale === 'ar'
        ? 'شكرًا لاستخدامكم نظام التبرعات الخاص بنا.'
        : 'Thank you for using our donation system.'
    }}
</footer>
</body>
</html>
