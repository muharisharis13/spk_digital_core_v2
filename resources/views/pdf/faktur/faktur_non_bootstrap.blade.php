<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print Faktur</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
    }
    /* Custom Styles */
    .table {
      border-collapse: collapse;
      width: 100%;
    }
    .table th,
    .table td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #dee2e6;
    }
    .table tbody tr:nth-child(even) {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <div style="width: 100%; max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
      <img src="{{ asset('logo/alfa-scorpii-logo.png') }}" style="max-width: 100px;">
      <div style="flex: 1;">
        <p style="font-size: 14px; font-weight: 600;">
          PT ALFA SCORPII - AR HAKIM <br>
          Jl. Arief Rahman Hakim No. 134 B - C -D - E <br>
          Sukaramai I, kec. Medan Area <br>
          Kota Medan, Sumatera Utara 20227
        </p>
      </div>
      <div style="text-align: right;">
        <span style="font-size: 24px; font-weight: 600;">SURAT JALAN</span><br>
        <span style="font-size: 12px;">Tgl. 12 Maret 2024</span><br>
        <span style="font-size: 16px; font-weight: 600;">0001/SJ-REPAIR/ARMDN/03/2024</span>
      </div>
    </div>
    <div style="display: flex; margin-bottom: 20px;">
      <div style="flex: 1;">
        <span style="font-size: 14px; font-weight: 600;">Detail Pembeli&nbsp;:</span><br>
        <span style="font-size: 14px;">Indent Unit</span><br>
        <span style="font-size: 14px;">{{ $indent->indent_number }}</span><br>
        <span style="font-size: 14px;">Tgl Indent&nbsp;: {{ date('d M Y', strtotime($indent->created_at)) }}</span>
      </div>
    </div>
    <div style="text-align: center; margin-bottom: 20px;">
      <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
        <thead style="background-color: #f2f2f2;">
          <tr>
            <th style="padding: 8px;">Model</th>
            <th style="padding: 8px;">Warna</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding: 8px;">{{ $indent->motor->motor_name }}</td>
            <td style="padding: 8px;">{{ $indent->color->color_name }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div style="text-align: center;">
      <h4>Payment</h4>
      <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
        <thead style="background-color: #f2f2f2;">
          <tr>
            <th style="padding: 8px;">Payment Method</th>
            <th style="padding: 8px;">Payment Bank</th>
            <th style="padding: 8px;">Payment Amount</th>
            <th style="padding: 8px;">Payment Date</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($indent->indent_payment as $item)
          <tr>
            <td style="padding: 8px; text-transform: uppercase;">{{ $item->indent_payment_method }}</td>
            <td style="padding: 8px;">{{ $item->bank->bank_name}}</td>
            <td style="padding: 8px;">{{ $item->indent_payment_amount}}</td>
            <td style="padding: 8px;">{{ $item->indent_payment_date}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
