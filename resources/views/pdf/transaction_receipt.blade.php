<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url("{{ asset('storage/fonts/Amiri-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'amiri', serif;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            margin: 30px;
            color: #000;
            background-color: #fff;
        }

        h3 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th, td {
            border: 1px solid #000;
            padding: 10px 12px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 14px;
        }

        th {
            font-weight: bold;
            width: 35%;
            background-color: #fff;
        }

        td {
            background-color: #fff;
        }

        .signature {
            margin-top: 60px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            font-size: 14px;
        }

        .signature .line {
            margin-top: 30px;
            border-top: 1px solid #000;
            width: 200px;
        }

        footer {
            font-size: 12px;
            color: #000;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
            position: fixed;
            bottom: 30px;
            width: 100%;
        }
    </style>
</head>
<body>

<h3>جمعية كن عوناً</h3>
<h1> Transaction Receipt </h1>

<table>
    @php
        $locale = app()->getLocale();
        $types = [
            'donation'=> 'Donation',
            'exchange' =>  'Exchange',
            'recharge' =>  'Recharge',
        ];
        $directions = [
            'in' =>  'In',
            'out' =>  'Out',
        ];
    @endphp

    <tr>
        <th>Transaction ID</th>
        <td>{{ $transaction->id }}</td>
    </tr>
    <tr>
        <th> Transaction Type  </th>
        <td>{{ $types[$transaction->type] ?? $transaction->type }}</td>
    </tr>
    <tr>
        <th> Direction </th>
        <td>{{ $directions[$transaction->direction] ?? $transaction->direction }}</td>
    </tr>
    <tr>
        <th> Amount </th>
        <td>{{ number_format($transaction->amount, 2) }} $ </td>
    </tr>
    <tr>
        <th> Transaction Date </th>
        <td>{{ $transaction->created_at->translatedFormat('Y-m-d H:i') }}</td>
    </tr>
    @if($transaction->user)
        <tr>
            <th>User</th>
            <td>{{ $transaction->user->name}}</td>
        </tr>
    @endif
    @if($transaction->admin)
        <tr>
            <th> Admin </th>
            <td>{{ $transaction->admin->id }}</td>
        </tr>
    @endif
    @if($transaction->campaign)
        <tr>
            <th> Campaign</th>
            <td>{{ $transaction->campaign->title_en}}</td>
        </tr>
    @endif
    @if($transaction->box)
        <tr>
            <th>Box</th>
            <td>{{ $transaction->box->name }}</td>
        </tr>
    @endif
</table>

<div class="signature">
    Organization Signature
    <div class="line"></div>
</div>
<div class="signature" style="margin-top: 60px; text-align: {{ $locale === 'ar' ? 'right' : 'left' }}; font-family: cursive; font-size: 24px; color: #000;">
    {{  'كن عوناً'  }}
    <div style="border-top: 1px solid #000; width: 200px; margin-top: 5px;"></div>
</div>

<footer>
    'Thank you for using our donation system.'
</footer>

</body>
</html>
