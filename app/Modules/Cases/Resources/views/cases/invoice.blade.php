<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة مختبر الرازي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 105mm 148mm; /* A6 portrait */
            margin: 5mm;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .invoice-box {
            width: 100%;
            margin: auto;
            padding: 0;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        .top-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 12px;
            background-color: #9dc9a0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1rem;
            border-bottom: 2px solid #555;
            margin-top: 20px; /* Space for the top-bar */
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .case-id-container {
            display: flex;
            align-items: flex-end;
        }

        .logo {
            width: 120px;
            height: 120px;
        }

        .address {
            margin-top: 15px;
            font-size: 1.3em;
            font-weight: bold;
        }

        .serial-number {
            color: #d90000;
            font-weight: bold;
            font-size: 1.4em;
            margin-top: 15px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            padding: 20px 0;
            font-size: 1.3em;
            font-weight: bold;
        }
        
        .meta-info .doctor-field {
            flex-grow: 1;
            margin-right: 30px;
        }
        
        .dotted-line {
            border-bottom: 1px dotted #333;
            display: inline-block;
            min-width: 300px;
        }
        
        .date-field span {
            margin: 0 8px;
        }

        .table-container {
            position: relative;
            width: 100%;
            margin-top: 15px;
        }

        /* Watermark */
        .table-container::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            opacity: 0.08;
            z-index: 0;
            /* URL-encoded SVG of the tooth logo */
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 60 70' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M52.1 19.3C52.1 9.4 43.6 2.3 32.7 2.3C21.8 2.3 10.6 9.5 10.6 21.6C10.6 28.1 13 32.7 15.1 36.6C10.2 40.5 5 47.3 5 56.4C5 63.9 11.2 70 19.2 70C24.4 70 28.5 67 31.4 63.2C34.2 67 38.3 70 43.5 70C51.5 70 57.7 63.9 57.7 56.4C57.7 47.3 52.5 40.5 47.6 36.6C49.7 32.7 52.1 28.1 52.1 21.6V19.3Z' fill='%238a9bbf'/%3E%3Cpath d='M19.1 41.6C23.6 37.1 30.7 37.1 39.5 41.6C37.6 37.7 35.3 34.3 32.7 31.5C30 28.6 26.6 28.2 23.1 31.5C22.7 34.3 21 37.7 19.1 41.6Z' fill='white'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: contain;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            position: relative; /* To sit on top of the watermark */
            z-index: 1;
        }

        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: right;
            font-size: 1.3em;
        }

        thead th {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }

        tbody tr {
            height: 60px; /* Fixed height for empty rows */
        }
        
        tbody td {
            vertical-align: top;
        }

        th:nth-child(1) { width: 15%; }
        th:nth-child(2) { width: 55%; }
        th:nth-child(3) { width: 30%; }

        tfoot td {
            border-top: 2px solid #333;
            vertical-align: top;
        }

        .payment-details {
            padding: 15px;
            font-weight: bold;
            line-height: 2.5;
        }
        
        .payment-details .checkbox {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 1px solid #333;
            margin-left: 10px;
            vertical-align: middle;
        }

        .totals {
            padding: 0;
            background: #f9f9f9;
        }
        
        .totals-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        
        .totals-grid span {
            padding: 15px 10px;
            display: block;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        
        .totals-grid span:nth-child(odd) { /* Labels */
            border-left: 1px solid #ccc;
        }
        
        .totals-grid span:nth-child(7),
        .totals-grid span:nth-child(8) {
             border-bottom: none; /* Remove border from last row */
        }

        footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #555;
            display: flex;
            justify-content: space-evenly;
            font-weight: bold;
            font-size: 1.1em;
            color: #333;
            align-items: center;
        }

    </style>
</head>
<body>

    <div class="invoice-box">
        <div class="top-bar"></div>
        
        <header>
            <div class="logo-container">
                <svg class="logo" viewBox="0 0 60 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M52.1 19.3C52.1 9.4 43.6 2.3 32.7 2.3C21.8 2.3 10.6 9.5 10.6 21.6C10.6 28.1 13 32.7 15.1 36.6C10.2 40.5 5 47.3 5 56.4C5 63.9 11.2 70 19.2 70C24.4 70 28.5 67 31.4 63.2C34.2 67 38.3 70 43.5 70C51.5 70 57.7 63.9 57.7 56.4C57.7 47.3 52.5 40.5 47.6 36.6C49.7 32.7 52.1 28.1 52.1 21.6V19.3Z" fill="#8a9bbf"/>
                  <path d="M19.1 41.6C23.6 37.1 30.7 37.1 39.5 41.6C37.6 37.7 35.3 34.3 32.7 31.5C30 28.6 26.6 28.2 23.1 31.5C22.7 34.3 21 37.7 19.1 41.6Z" fill="white"/>
                </svg>
                <div class="address">
                    📍 اربد - شارع عبده بقاعور
                </div>
            </div>
            <div class="case-id-container">
                <div class="serial-number">
                    № {{ $case->case_id }}
                </div>
            </div>
        </header>
        
        <section class="meta-info">
            <div class="date-field">
                التاريخ: <span class="dotted-line" style="min-width: 50px;">{{ $case->actual_delivery_date ? date('d', strtotime($case->actual_delivery_date)) : '' }}</span> /
                <span class="dotted-line" style="min-width: 50px;">{{ $case->actual_delivery_date ? date('m', strtotime($case->actual_delivery_date)) : '' }}</span> /
                {{ $case->actual_delivery_date ? date('Y', strtotime($case->actual_delivery_date)) : '۲۰۲' }}
            </div>
            <div class="doctor-field">
                الدكتور <span class="dotted-line">{{ $case->client->name }}</span> المحترم
            </div>
        </section>
        
        <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>العدد</th>
                        <th>التفاصيل</th>
                        <th>الإجمالي<br>دينار</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($case->jobs as $job)
                    <tr>
                        <td>{{ count(explode(',', $job->unit_num)) }}</td>
                        <td>{{ $job->jobType->name }} - {{ str_replace(' لون/Color:', '', $job->material->name) }}</td>
                        <td>{{ $job->unit_price * count(explode(',', $job->unit_num)) }}</td>
                    </tr>
                    @endforeach
                    @for ($i = 0; $i < (7 - $case->jobs->count()); $i++)
                    <tr><td>&nbsp;</td><td></td><td></td></tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="payment-details">
                            <div>
                                <span class="checkbox"></span> نقداً
                            </div>
                            <div>
                                بموجب شيك <span class="dotted-line" style="min-width: 100px;"></span>
                                بتاريخ <span class="dotted-line" style="min-width: 30px;"></span> /
                                <span class="dotted-line" style="min-width: 30px;"></span> /
                                ۲۰۲
                            </div>
                        </td>
                        <td class="totals">
                            <div class="totals-grid">
                                <span>الرصيد المدور</span>   <span class="value">{{ $case->client->balance - $case->invoice->amount }}</span>
                                <span>دفعة</span>           <span class="value">&nbsp;</span>
                                <span>الرصيد بعد الدفعة</span> <span class="value">&nbsp;</span>
                                <span>الإجمالي</span>        <span class="value">{{ $case->invoice->amount }}</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </section>
        
        <footer>
            <span style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;"><img src="https://i.ibb.co/GTP1v2n/facebook.png" alt="facebook" border="0" width="20px"> fb_id</span>
            <span style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;"><img src="https://i.ibb.co/bJpLpns/gmail.png" alt="gmail" border="0" width="20px"> email</span>
            <span style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;"><img src="https://i.ibb.co/c2p2L8t/instagram.png" alt="instagram" border="0" width="20px"> insta_id</span>
        </footer>
    </div>

</body>
</html>