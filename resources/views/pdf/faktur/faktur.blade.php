<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Print Faktur</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <style>
      * {
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
    <div class="container-fluid">
      <div class="row">
        <div class="col-1">
          <?php
$imagePath = 'logo/alfa-scorpii-logo.png';
$imageData = base64_encode(file_get_contents($imagePath));
?>
          <img src="data:image/png;base64,{{ $imageData }}" class="img-fluid" />
        </div>
        <div class="col-4">
          <p class="fw-bold" style="font-size: medium">
            {{ $dealer->dealer->dealer_name }} <br />Jl. Arief Rahman Hakim No. 134 B -
            C -D - E <br />
            Sukaramai I, kec. Medan Area <br />
            Kota Medan, Sumatera Utara 20227
          </p>
        </div>
        <div
          class="col-7 justify-content-end d-flex"
          style="flex-direction: column"
        >
          <span
            class="fw-bold"
            style="font-size: xx-large; align-self: flex-end"
            >SURAT JALAN</span
          >
          <span class="fw-bold" style="font-size: small; align-self: flex-end"
            >Tgl. 12 Maret 2024</span
          >
          <span style="align-self: flex-end; font-size: large; font-weight: 600"
            >0001/SJ-REPAIR/ARMDN/03/2024</span
          >
        </div>
      </div>
      <div class="row mt-5">
        {{-- <div class="col-5 d-flex flex-column">
          <span class="fw-bold" style="font-size: medium"
            >Detail Pengiriman&nbsp;:</span
          >
          <span class="fw-normal" style="font-size: medium"
            >Driver&nbsp;: Doni Chrisdianto K</span
          >
          <span class="fw-normal" style="font-size: medium"
            >Mobil&nbsp;: Avanza Veloz</span
          >
          <span class="fw-bold mt-4" style="font-size: medium"
            >Detail Penerima&nbsp;:</span
          >
          <span class="fw-bold" style="font-size: medium">PT ALFA SCORPII</span>
          <p class="fw-normal" style="font-size: medium">
            Jl. H. Adam Malik No.34 C <br />Silalas - Medan Barat<br />
            Medan, 20214, Sumatera Utara <br />
            (061) 453 0935
          </p>
        </div> --}}

        <div class="col-3 d-flex flex-column align-items-start">
          <span class="fw-bold" style="font-size: medium"
            >Detail Pembeli&nbsp;:</span
          >
          <span class="fw-normal" style="font-size: medium"
            >Indent Unit</span
          >
          <span class="fw-normal" style="font-size: medium"
            >{{ $indent->indent_number }}</span
          >
          <span class="fw-normal" style="font-size: medium"
            >Tgl Indent&nbsp;: {{ date('d M Y', strtotime($indent->created_at))  }}</span
          >
        </div>
      </div>

      <div class="d-flex justify-content-center mt-5">
        <table class="table border table-bordered">
          <thead class="bg-body-secondary text-dark fw-bold">
            <tr>
              <th class="text-center bg-body-secondary">Model</th>
              <th class="text-center bg-body-secondary">Warna</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-center">{{ $indent->motor->motor_name }}</td>
              <td class="text-center">{{ $indent->color->color_name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center mt-5">
        <h4>Payment</h4>
        <table class="table border table-bordered">
          <thead class="bg-body-secondary text-dark fw-bold">
            <tr>
              <th class="text-center bg-body-secondary">Payment Method</th>
              <th class="text-center bg-body-secondary">Payment Bank</th>
              <th class="text-center bg-body-secondary">Payment Amount</th>
              <th class="text-center bg-body-secondary">Payment Date</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($indent->indent_payment as $item)
              <tr>
              <td class="text-center text-uppercase">{{ $item->indent_payment_method }}</td>
              <td class="text-center">{{ $item->bank->bank_name}}</td>
              <td class="text-center">{{ $item->indent_payment_amount}}</td>
              <td class="text-center">{{ $item->indent_payment_date}}</td>
            </tr>
            @endforeach
            
          </tbody>
        </table>
      </div>
    </div>
  </body>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
  ></script>
</html>
