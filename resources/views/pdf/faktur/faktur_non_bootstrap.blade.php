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

       .table-ttd {
        width: 100%;
        border-collapse: collapse;
        margin-top: -30px;
    }

    .td-ttd {
        padding: 10px;
    }

    .box {
        border: 1px solid #ccc;
        border-radius: 5px;
        height: 200px;
        width: 100%;
        padding: 5px;
        background-color: white;
    }
  </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif;">
  <table style="width: 100%">
    <tbody>
      <tr>
        <td>
          <table>
            <tr>
              <td>
                <?php
               $imagePath = 'logo/alfa-scorpii-logo.png';
               $imageData = base64_encode(file_get_contents($imagePath));
               ?>
             <img src="data:image/png;base64,{{ $imageData }}" class="img-fluid" 
                                   width="150" />
              </td>
              <td>
                 <p style="font-size: 14px; font-weight: 600;">
                  {{ $dealer->dealer->dealer_name }} <br>
                  {{ $dealer->dealer->dealer_address }}  <br>
                </p>
              </td>
            </tr>
          </table>
        </td>
        <td style="text-align: right">
           <span style="font-size: 24px; font-weight: 600;text-align:right">INDENT</span><br>
            <span style="font-size: 12px;text-align: end">Tgl. {{ date('d M Y', strtotime($indent->created_at)) }}</span><br>
            <span style="font-size: 16px; font-weight: 600;text-align: end">{{ $indent->indent_number }}</span>
        </td>
      </tr>
    </tbody>
  </table>



      <div  style="margin-top: 50px;margin-bottom:50px">
        <span style="font-size: 14px; font-weight: 600;">Detail Pembeli&nbsp;:</span><br>
        <span style="font-size: 14px;">{{ $indent->indent_people_name }}</span><br>
        <span style="font-size: 14px;">{{ $indent->indent_number }}</span><br>
        <span style="font-size: 14px;">Tgl Indent&nbsp;: {{ date('d M Y', strtotime($indent->created_at)) }}</span><br>
        <span style="font-size: 14px; text-transform:uppercase; font-weight:bold; color:red">{{ $indent->indent_status }}</span>
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
       <table
        style="width: 100%; border: 1px solid #d4d4d4; background-color: white"
      >
        <thead style="background-color: #f2f2f2">
          <tr>
            <th style="padding: 8px">Payment Method</th>
            <th style="padding: 8px">Payment Bank</th>
            <th style="padding: 8px">Payment Amount</th>
            <th style="padding: 8px">Payment Date</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($indent->indent_payment as $item)
          <tr>
            <td style="padding: 8px; text-transform: uppercase; text-align:center">{{ $item->indent_payment_method }}</td>
            <td style="padding: 8px;text-align:center">{{ isset($item->bank->bank_name) ? $item->bank->bank_name : "-"}}</td>
            <td style="padding: 8px;text-align:center">{{ 'Rp ' . number_format($item->indent_payment_amount, 0, ',', '.') }}</td>
            <td style="padding: 8px;text-align:center">{{ $item->indent_payment_date}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

   <table class="table-ttd">
          <tr>
              <td class="" style="padding-right: 10px;padding-left:-10px">
                  <div class="box">
                      <div>Diserahkan Oleh :</div>
                  </div>
              </td>
              <td class="td-ttd" style="padding-left: 5px; padding-right: 5px;">
                  <div class="box">
                      <div>Diserahkan Oleh :</div>
                  </div>
              </td>
              <td class="td-ttd" style="padding-left: 10px;">
                  <div class="box">
                      <div>Diserahkan Oleh :</div>
                  </div>
              </td>
          </tr>
  </table>
</body>
</html>
