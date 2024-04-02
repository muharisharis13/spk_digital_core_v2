<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Payment</title>
    <style>
        table {
            border-collapse: collapse;
        }
    </style>
</head>

<body style=" padding: 0;
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;">
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
                                <img src="data:image/png;base64,{{ $imageData }}" alt="" width="150">
                            </td>
                            <td style="display: flex;flex-direction:column;align-items:baseline">
                                <h2>PT ALFA SCORPII - AR HAKIM</h2>
                                <div class="alamat" style="max-width: 400px">
                                    Jl. Arief Rahman Hakim No.134 B - C - D - E Sukaramai I, Kec. Medan Area
                                    Kota Medan, Sumatera Utara 20227
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table style="margin-top: 100px">
                        <tbody>
                            <tr>
                                <td>Sudah diterima dari</td>
                                <td>: Muharis</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td>

                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
