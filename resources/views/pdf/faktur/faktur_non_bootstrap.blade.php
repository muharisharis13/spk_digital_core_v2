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
         <?php
            $imagePath = 'logo/alfa-scorpii-logo.png';
            $imageData = base64_encode(file_get_contents($imagePath));
            ?>
          <img src="data:image/png;base64,{{ $imageData }}" class="img-fluid" style="width: 250px" />
      <div style="flex: 1;">
        <p style="font-size: 14px; font-weight: 600;">
          {{ $dealer->dealer->dealer_name }} <br>
          {{ $dealer->dealer->dealer_address }}  <br>
        </p>
      </div>
      <div style="text-align: right;">
        <span style="font-size: 24px; font-weight: 600;">INDENT</span><br>
        <span style="font-size: 12px;">Tgl. {{ date('d M Y', strtotime($indent->created_at)) }}</span><br>
        <span style="font-size: 16px; font-weight: 600;">{{ $indent->indent_number }}</span>
      </div>
    </div>
    <div style="display: flex; margin-bottom: 20px;">
      <div style="flex: 1;">
        <span style="font-size: 14px; font-weight: 600;">Detail Pembeli&nbsp;:</span><br>
        <span style="font-size: 14px;">{{ $indent->indent_people_name }}</span><br>
        <span style="font-size: 14px;">{{ $indent->indent_number }}</span><br>
        <span style="font-size: 14px;">Tgl Indent&nbsp;: {{ date('d M Y', strtotime($indent->created_at)) }}</span><br>
        <span style="font-size: 14px; text-transform:uppercase; font-weight:bold; color:red">{{ $indent->indent_status }}</span>
      </div>
    </div>
    <div style="text-align: center; margin-bottom: 20px;">
      <table style="width: 100%; border-collapse: collapse; border: 1px solid #d4d4d4;">
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
    <div style="text-align: start;">
      <h4>Payment</h4>
      <table style="width: 100%; border-collapse: collapse; border: 1px solid #d4d4d4;">
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
