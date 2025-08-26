<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Amiri', serif;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            margin: 30px;
            color: #000;
        }

        h1, h3 { text-align: center; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 14px;
        }

        th { font-weight: bold; }

        .signature {
            margin-top: 50px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 14px;
        }

        .signature .line {
            margin-top: 20px;
            border-top: 1px solid #000;
            width: 200px;
        }

        footer {
            font-size: 12px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
            position: fixed;
            bottom: 20px;
            width: 100%;
        }

        .header-logo {
            display: block;
            margin: 0 auto 10px auto;
            max-width: 120px;
        }
    </style>
</head>
<body>
@php
    $path = storage_path('app/public/images/kunAunan.png'); // المسار الكامل الصحيح

    if (file_exists($path)) {
        $imageData = base64_encode(file_get_contents($path));
        $mime = mime_content_type($path);
        $src = "data:$mime;base64,$imageData";
    } else {
        $src = '';
        \Log::error('Image not found: ' . $path);
    }
@endphp

@if($src)
    <img src="{{ $src }}" style="display:block; margin-left:0; margin-right:auto; max-width:120px;">

@endif



<h3>كـن عـونـاً</h3>
<h1>{{ app()->getLocale() === 'ar' ? 'إيصال المعاملة' : 'Transaction Receipt' }}</h1>

<table>
    <tr><th>ID</th><td>{{ $transaction->id }}</td></tr>
    <tr><th>{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th><td>{{ $transaction->type }}</td></tr>
    <tr><th>{{ app()->getLocale() === 'ar' ? 'الاتجاه' : 'Direction' }}</th><td>{{ $transaction->direction }}</td></tr>
    <tr><th>{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th><td>{{ number_format($transaction->amount, 2) }} $</td></tr>
    <tr><th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th><td>{{ $transaction->created_at->translatedFormat('Y-m-d H:i') }}</td></tr>
    @if($transaction->user)
        <tr><th>{{ app()->getLocale() === 'ar' ? 'المستخدم' : 'User' }}</th><td>{{ $transaction->user->name }}</td></tr>
    @endif
</table>

<div class="signature">
    {{ app()->getLocale() === 'ar' ? 'توقيع الجمعية' : 'Organization Signature' }} : كـن عـونـاً
    <div class="line"></div>
</div>

<footer>
    {{ app()->getLocale() === 'ar' ? 'شكراً لاستخدامكم نظام التبرعات' : 'Thank you for using our donation system.' }}
</footer>

</body>
</html>
