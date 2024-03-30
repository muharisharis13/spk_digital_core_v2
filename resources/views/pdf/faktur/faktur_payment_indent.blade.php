<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <style>
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

    #header {
      display: flex;
      justify-content: space-between;
    }
    #header .container-left {
      display: flex;
      gap: 10px;
    }
    .company-name {
      font-size: x-large;
      font-weight: bold;
        top: 50%;
        transform: translate
    }
  </style>
  <body
    style="
      padding: 0;
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      position: relative;
      z-index: 1;
    "
  >
  <?php
// URL gambar yang ingin Anda ubah menjadi format base64
$imageUrl = 'https://cdn.pixabay.com/photo/2020/06/02/11/58/stamp-5250659_1280.png';

// Ambil konten gambar dari URL
$imageData2 = file_get_contents($imageUrl);

// Ubah konten gambar menjadi base64
$base64Image = base64_encode($imageData2);
?>
    <div
      style="
        display: block;
        position: absolute;
        left: 50%;(-50%, -50%);
        width: 90%;
        height: 70%;
        opacity: 0.6;
        background-image: url('data:image/png;base64, {{ $base64Image }}');
        background-repeat: no-repeat;
        background-position: 50% 50%;
        background-size: cover;
        transform: translate(-50%, -50%) rotate(-30deg);
        z-index: -1;
      "
    ></div>
    <div id="header">
      <div class="container-left">
          <?php
            $imagePath = 'logo/alfa-scorpii-logo.png';
            $imageData = base64_encode(file_get_contents($imagePath));
            ?>
            <table>
                <tbody>
                    <tr>
                        <td>
                            <img
                                src="data:image/png;base64,{{ $imageData }}"
                                alt=""
                                width="150"
                                />
                        </td>
                        <td>
                            <div class="company-name">{{ $dealer->dealer->dealer_name }}</div>

                            <div>
                              <div style="max-width: 350px; margin-top: 0px">
                                {{ $dealer->dealer->dealer_address }}
                              </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        
      </div>
      <div
        class="container-right"
        style="
          text-align: right;
          display: flex;
          flex-direction: column;
          gap: 10px;
        "
      >
        <div class="type-faktur" style="font-size: xx-large; font-weight: bold;text-transform: uppercase">
          Faktur Indent Payment
        </div>
        <div class="number-faktur">{{ $indent_payment->indent->indent_number }}</div>
        <div class="date-faktur">Tgl. {{ date('d M Y', strtotime($indent_payment->created_at)) }}</div>
      </div>
    </div>

    <div id="informasi-detail" style="margin-top: 100px">
      <div style="font-size: medium; font-weight: bold">Detail Pembeli</div>
      <table style="margin-top: 5px">
        <tr>
          <td>Nama</td>
          <td>: {{ $indent_payment->indent->indent_people_name }}</td>
        </tr>
        <tr>
          <td>Indent Method</td>
          <td style=" text-transform: capitalize">: {{ $indent_payment->indent->indent_type }}</td>
        </tr>
        <tr>
          <td>Indent Payment Status</td>
          <td style="display: flex; gap: 5px">
            <table>
                <tr>
                    <td>:</td>
                    <td style="font-weight: bold; color: red;text-transform: uppercase">{{ $indent_payment->indent_payment_type }}</td>

                </tr>
            </table>
            
          </td>
        </tr>
      </table>
    </div>

    <div id="data-payment" style="margin-top: 20px">
      <table
        style="width: 100%; border: 1px solid #d4d4d4; background-color: white"
      >
        <thead style="background-color: #f2f2f2">
          <tr>
            <th style="padding: 8px">Payment Method</th>
            <th style="padding: 8px">Payment Bank</th>
            <th style="padding: 8px">Payment Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td
              style="
                padding: 8px;
                text-transform: uppercase;
                text-align: center;
              "
            >
              {{ $indent_payment->indent_payment_method }}
            </td>
            <td style="padding: 8px; text-align: center">
              {{ $indent_payment->bank->bank_name}}
            </td>
            <td style="padding: 8px; text-align: center">
               RP {{ number_format($indent_payment->indent_payment_amount, 0, ',', '.') }}
              
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div>

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
    </div>
     
      
  </body>
</html>
