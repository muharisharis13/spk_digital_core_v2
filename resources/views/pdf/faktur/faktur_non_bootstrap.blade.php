<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Faktur</title>
    <style>
        body {
            font-size: x-small;
            margin: 0px
        }

        .row {
            overflow: auto;
        }

        .left {
            float: left;
            width: 50%;
        }

        .right {
            float: right;
            width: 50%;
        }

        .page-break {
            page-break-after: always;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table,
        .th,
        .td {
            border: 1px solid #d4d4d4;
        }

        .th,
        .td {
            padding: 8px;
            text-align: center;
        }

        .th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body style="font-family: Arial, Helvetica, sans-serif;">
    <div class="row" style="height: 250px;">
        <div class="left">
            <div>
                {{-- <div>{{ $logo }}</div> --}}
                <img src="{{ $logo }}" alt="Company Logo" class="logo" width="100">
                {{-- <img src="logo/alfa-scorpii-logo.png" alt="Company Logo" class="logo" width="100"> --}}
            </div>

            <div>
                <h3>{{ $dealer->dealer->dealer_name ?? '-' }}</h3>
                <div class="alamat">
                    {{ $dealer->dealer->dealer_address ?? '-' }}
                </div>
            </div>
        </div>

        <div class="right" style="text-align: right">
            <h2>Indent</h2>
            <div>No.{{ $indent->indent_number }}</div>
            <div style="text-align: right;margin-top:10px">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate(url("/detail/indent/$indent->indent_id"))) !!} ">

            </div>
            <div style="margin-top: 10px;font-weight:bold">
                {{ date('d M Y', strtotime($indent->created_at)) }}
            </div>
        </div>
    </div>



    <div style="margin-top: 0px;margin-bottom:10px">
        <span style="font-size: 14px; font-weight: 600;">Detail Indent&nbsp;:</span><br>
        <span style="font-size: 14px;">{{ $indent->indent_people_name }}</span><br>
        <span style="font-size: 14px;">{{ $indent->indent_number }}</span><br>
        <span style="font-size: 14px;">Tgl Indent&nbsp;: {{ date('d M Y', strtotime($indent->created_at)) }}</span><br>
        <span
            style="font-size: 14px; text-transform:uppercase; font-weight:bold; color:red">{{ $indent->indent_status }}</span>
    </div>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #d4d4d4;">
        <thead style="background-color: #f2f2f2;">
            <tr>
                <th style="padding: 8px;">Model</th>
                <th style="padding: 8px;">Warna</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px;text-align:center">{{ $indent->motor->motor_name }}</td>
                <td style="padding: 8px;text-align:center">{{ $indent->color->color_name }}</td>
            </tr>
        </tbody>
    </table>

    <h4>Payment</h4>
    <table class="table">
        <thead>
            <tr>
                <th class="th">Payment Method</th>
                <th class="th">Payment Bank</th>
                <th class="th">Payment Amount</th>
                <th class="th">Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($indent->indent_payment as $item)
                <tr>
                    <td class="td" style="padding: 8px; text-transform: uppercase; text-align:center">
                        {{ $item->indent_payment_method }}</td>
                    <td class="td" style="padding: 8px;text-align:center">
                        {{ isset($item->bank->bank_name) ? $item->bank->bank_name : '-' }}</td>
                    <td class="td" style="padding: 8px;text-align:center">
                        {{ 'Rp ' . number_format($item->indent_payment_amount, 0, ',', '.') }}</td>
                    <td class="td" style="padding: 8px;text-align:center">{{ $item->indent_payment_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display: flex;justify-content:space-between">
        <table class=" table">
            <tr>
                <td class="td">
                    <div style="height: 100px">
                        Diserahkan Oleh:
                    </div>
                </td>
                <td class="td">
                    <div style="height: 100px">
                        Diserahkan Oleh:
                    </div>
                </td>
                <td class="td">
                    <div style="height: 100px">
                        Diserahkan Oleh:
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
