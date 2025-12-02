<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    @php($brandLogo = asset($brandingLogoPath ?? config('branding.defaults.logo_path')))
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة مختبر الرازي</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }

        .print-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .invoice-box {
            width: 800px;
            margin: auto;
            padding: 30px;
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
            padding-bottom: 20px;
            border-bottom: 2px solid #555;
            margin-top: 20px; /* Space for the top-bar */
        }

        header .contact-info {
            text-align: right;
            font-size: 14px;
            line-height: 1.6;
        }

        header .contact-info .name-logo {
            font-family: 'Times New Roman', Times, serif;
            font-style: italic;
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
        }

        header .contact-info .name-logo span {
            display: block;
        }

        header .contact-info .phones {
            margin-top: 10px;
        }

        header .contact-info .phones span {
            display: block;
            font-weight: bold;
        }

        header .title {
            text-align: center;
            color: #111;
        }

        header .title h1 {
            margin: 0;
            font-size: 2.2em;
        }

        header .title h2 {
            margin: 0;
            font-size: 1.4em;
            font-weight: bold;
            letter-spacing: 1px;
        }

        header .title .address {
            margin-top: 10px;
            font-size: 1.1em;
            font-weight: bold;
        }

        header .logo-area {
            text-align: center;
        }

        .logo {
            width: 100px;
            height: 100px;
        }

        .serial-number {
            color: #d90000;
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 10px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-size: 1.1em;
            font-weight: bold;
        }

        .meta-info .doctor-field {
            flex-grow: 1;
            margin-right: 20px;
        }

        .dotted-line {
            border-bottom: 1px dotted #333;
            display: inline-block;
            min-width: 200px;
        }

        .date-field span {
            margin: 0 5px;
        }

        .table-container {
            position: relative;
            width: 100%;
            margin-top: 10px;
        }

        /* Watermark */
        .table-container::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 300px;
            opacity: 0.08;
            z-index: 0;
            background-image: url("{{ $brandLogo }}");
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
            padding: 8px;
            text-align: right;
            font-size: 1.1em;
        }

        thead th {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }

        tbody tr {
            height: 45px; /* Fixed height for empty rows */
        }

        tbody td {
            vertical-align: top;
        }

        th:nth-child(1) { width: 20%; }
        th:nth-child(2) { width: 65%; }
        th:nth-child(3) { width: 15%; }

        tfoot td {
            border-top: 2px solid #333;
            vertical-align: top;
        }

        .payment-details {
            padding: 10px;
            font-weight: bold;
            line-height: 2;
        }

        .payment-details .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #333;
            margin-left: 8px;
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
            padding: 12px 8px;
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
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #555;
            display: flex;
            justify-content: space-around;
            font-weight: bold;
            font-size: 0.9em;
            color: #333;
        }

        /* New styles for editable fields */
        .editable-field {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 50px; /* Adjust as needed */
        }

        .editable-field-inline {
            display: inline-block;
            text-align: center;
            position: relative;
            min-width: 50px; /* Adjust as needed */
        }

        .editable-field .edit-icon, .editable-field-inline .edit-icon {
            cursor: pointer;
            color: #007bff; /* Blue color for edit icon */
            font-size: 0.8em;
        }

        .editable-field:hover .edit-icon, .editable-field-inline:hover .edit-icon {
            /* No change on hover, icon is always visible */
        }

        .editable-input {
            border: 1px solid #ccc;
            padding: 2px 5px;
            font-family: 'Cairo', sans-serif;
            font-size: 1em;
            text-align: inherit; /* Inherit text alignment from parent */
            width: 100%;
        }

        .edit-icon {
            display: inline-block;
            cursor: pointer;
            color: #007bff;
            font-size: 0.7em;
            vertical-align: super;
            margin-left: 3px;
        }

        .edit-icon:hover {
            color: #0056b3;
        }

        .editable-value {
            display: inline-block;
            min-width: 60px;
        }

        @media print {
            .edit-icon {
                display: none !important;
            }
            body, html {
                margin: 0;
                padding: 0;
                background: #fff;
                -webkit-print-color-adjust: exact;
            }
            .print-button {
                display: none;
            }
            .invoice-box {
                box-shadow: none;
                border: 0;
                margin: 0;
                padding: 5px;
                width: 100%;
                height: 100%;
                max-width: 100%;
                position: absolute;
                top: 0;
                left: 0;
                font-size: 9px;
            }
            .invoice-box, .invoice-box table, .invoice-box th, .invoice-box td {
                font-size: 9px;
            }
            .invoice-box th, .invoice-box td {
                padding: 4px;
            }
            .top-bar, header, footer, .meta-info, .table-container {
                page-break-inside: avoid;
            }
            body > *:not(.invoice-box) {
                display: none;
            }
            @page {
                size: A6 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <button class="print-button" onclick="window.print()">طباعة</button>

    <div class="invoice-box">
        <div class="top-bar"></div>

        <header>
            <div class="logo-area">
                <img src="{{ $brandLogo }}" alt="{{ $brandingName ?? 'Brand' }} Logo" class="logo">
                <div class="serial-number">
                    № {{ $case->id }}
                </div>
            </div>

            <div class="title">
                <h1>مختبر الرازي للأسنان</h1>
                <h2>AL - RAZI DENTAL LAB.</h2>
                <div class="address">
                    اربد - شارع الملك عبدلله الثاني📍 <br/>
                    مقابل البوابة الشرقية لمدينة الحسن - جمعة سنتر 9
                </div>
            </div>

            <div class="contact-info">
                <div class="phones" style="text-align: left">
                    <span> 1100  726 02 📞</span>
                    <span> 1100 726 079 📞</span>
                </div>
            </div>
        </header>

        <section class="meta-info">
            <div class="doctor-field">
                الدكتور <span class="dotted-line">{{ $case->client->name }}</span> المحترم
            </div>
            <div class="date-field" style="float: left;">
                التاريخ: <span class="dotted-line" style="min-width: 50px;">{{ now()->format('d / m / Y') }}</span>
            </div>
        </section>

        <section class="meta-info" style="padding-top: 0; margin-top: -10px;">
            <div class="doctor-field">
                المريض: <span class="dotted-line">{{ $case->patient_name ?? 'N/A' }}</span>
            </div>
        </section>

        <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>الإجمالي<br>دينار</th>
                        <th>التفاصيل</th>
                        <th>العدد</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalInvoiceAmount = 0;
                        $rowCount = 0;
                    @endphp

                    @foreach($case->jobs as $job)
                        @php
                            if($job->is_modification) continue;

                            $unitsAmount = count(explode(',', $job->unit_num));

                            if (isset($job->unit_price) && $job->unit_price > 0)
                                $totalJobPrice = $unitsAmount * $job->unit_price;
                            else
                                $totalJobPrice = $unitsAmount * $job->material->price;

                            $totalInvoiceAmount += $totalJobPrice;

                            $details = $job->jobType->name . ' - ' . $job->material->name;
                            if ($job->style) {
                                $details .= ' - ' . $job->style;
                            }
                            if ($job->color && $job->color != 0) {
                                $details .= ' - لون/Color: ' . $job->color;
                            }
                            if ($job->is_rejection) {
                                $details .= ' <span style="color:red; font-weight:bold;">(إعادة/REJECTION)</span>';
                            }

                            $rowCount++;
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ number_format($totalJobPrice, 2) }}</td>

                            <td>{!! $details !!}</td>
                            <td style="text-align: center;">{{ $unitsAmount }}</td>
                        </tr>
                    @endforeach
                    @for($j = $rowCount; $j < 7; $j++)
                        <tr><td>&nbsp;</td><td></td><td></td></tr>
                    @endfor
                </tbody>
                <tfoot>
                    @php
                        $caseCost = $case->invoice->amount ?? $totalInvoiceAmount ?? 0;
                        $clientBalance = $case->client->balance ?? 0;
                        $previousBalance = ($clientBalance ?? 0) - $caseCost;
                        // Balance after payment should start from previous balance, not include current case
                        $balanceAfterCase = $previousBalance;
                    @endphp

                    <!-- Previous Balance Row -->
                    <tr>
                        <td style="text-align: center; padding: 8px;">
                            <span id="previousBalance" class="editable-value">{{ number_format($previousBalance, 2) }}</span><span class="edit-icon">✏️</span>
                        </td>
                        <td colspan="2" style="text-align: right; padding: 8px; border-left: 1px solid #333;">
                            الرصيد المدور
                        </td>
                    </tr>

                    <!-- Payment Row (Cash) -->
                    <tr >
                        <td style="text-align: center; padding: 8px;">
                            <input type="number"
                                   id="paymentAmount"
                                   step="0.01"
                                   placeholder="0.00"
                                   style="width: 80px;
                                          text-align: center;
                                          border: none;
                                          border-bottom: 1px dotted #333;
                                          background: transparent;
                                          font-family: 'Cairo', sans-serif;
                                          font-size: 1.1em;
                                          padding: 4px;">
                        </td>
                        <td colspan="2" style="text-align: right; padding: 8px; border-left: 1px solid #333;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="flex: 1; text-align: right;">
                                    دفعة <span id="paymentInWords" style="display: inline-block;">............................................................ دينار أردني لا غير</span>
                                </span>
                                <span style="margin-left:18px" >نقداً</span>
                            </div>
                            <div style="margin-top: 5px;">
                                بموجب شيك  ..............................................................................................  بتاريخ &nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;  202
                            </div>
                        </td>


                    </tr>

                    <!-- Balance After Payment Row -->
                    <tr>
                        <td style="text-align: center; padding: 8px;">
                            <span id="balanceAfterPayment" class="editable-value">{{ number_format($balanceAfterCase, 2) }}</span><span class="edit-icon">✏️</span>
                        </td>
                        <td colspan="2" style="text-align: right; padding: 8px; border-left: 1px solid #333;">
                            الرصيد بعد الدفعة
                        </td>
                    </tr>

                    <!-- Total Row -->
                    <tr>
                        <td style="text-align: center; padding: 8px; font-weight: bold;">
                            <span id="netTotal" class="editable-value">{{ number_format($case->client->balance, 2) }}</span><span class="edit-icon">✏️</span>
                        </td>
                        <td colspan="2" style="text-align: right; padding: 8px; border-left: 1px solid #333; font-weight: bold;">
                            الإجمالي
                        </td>
                    </tr>
                </tfoot>
            </table>
            </section>

            <footer>
                <span>✉️ support@korvion.com</span>
                <span>👍 Korvion Dental Lab</span>
                <span>📷 Korvion</span>
            </footer>
        </div>

        <script>
            function numberToWords(num) {
                if (num === 0) return "صفر";

                let ones = ["", "واحد", "اثنان", "ثلاثة", "أربعة", "خمسة", "ستة", "سبعة", "ثمانية", "تسعة",
                    "عشرة", "أحد عشر", "اثنا عشر", "ثلاثة عشر", "أربعة عشر", "خمسة عشر",
                    "ستة عشر", "سبعة عشر", "ثمانية عشر", "تسعة عشر"];
                let tens = ["", "", "عشرون", "ثلاثون", "أربعون", "خمسون", "ستون", "سبعون", "ثمانون", "تسعون"];

                function under100(n) {
                    if (n < 20) return ones[n];
                    let t = Math.floor(n / 10);
                    let o = n % 10;
                    if (o === 0) return tens[t];
                    return ones[o] + " و" + tens[t];
                }

                function under1000(n) {
                    if (n < 100) return under100(n);
                    let h = Math.floor(n / 100);
                    let rest = n % 100;

                    let hundredWord = "";
                    if (h === 1) hundredWord = "مئة";
                    else if (h === 2) hundredWord = "مئتان";
                    else hundredWord = ones[h] + " مئة";

                    if (rest === 0) return hundredWord;
                    return hundredWord + " و" + under100(rest);
                }

                // Thousands
                if (num < 1000) return under1000(num);

                let thousands = Math.floor(num / 1000);
                let rest = num % 1000;

                let thousandWord = "";
                if (thousands === 1) thousandWord = "ألف";
                else if (thousands === 2) thousandWord = "ألفان";
                else if (thousands < 11) thousandWord = ones[thousands] + " آلاف";
                else thousandWord = under1000(thousands) + " ألف";

                if (rest === 0) return thousandWord;
                return thousandWord + " و" + under1000(rest);
            }

            document.getElementById("paymentAmount").addEventListener("input", function () {
                const value = parseInt(this.value) || 0;
                const paymentInWordsSpan = document.getElementById("paymentInWords");

                if (value > 0) {
                    paymentInWordsSpan.innerText = numberToWords(value) + " دينار أردني لا غير";
                } else {
                    paymentInWordsSpan.innerText = "............................................................ دينار أردني لا غير";
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Make totals editable when clicking pencil icons
                function makeEditable(spanElement) {
                    const initialValue = spanElement.textContent.trim().replace(/,/g, '');
                    const input = document.createElement('input');
                    input.type = 'number';
                    input.step = '0.01';
                    input.className = 'editable-input';
                    input.value = initialValue;
                    input.style.width = '100px';
                    input.style.textAlign = 'center';
                    input.style.fontFamily = 'Cairo, sans-serif';
                    input.style.fontSize = '1em';

                    spanElement.style.display = 'none';
                    spanElement.parentNode.insertBefore(input, spanElement.nextSibling);
                    input.focus();
                    input.select();

                    const saveChanges = function() {
                        const newValue = parseFloat(input.value) || 0;
                        spanElement.textContent = newValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        spanElement.style.display = 'inline-block';
                        input.remove();
                    };

                    input.addEventListener('blur', saveChanges);
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            saveChanges();
                        }
                    });
                }

                // Attach click handlers to all edit icons
                document.querySelectorAll('.edit-icon').forEach(icon => {
                    icon.addEventListener('click', function() {
                        const span = this.previousElementSibling; // The span right before the icon
                        if (span && span.classList.contains('editable-value')) {
                            makeEditable(span);
                        }
                    });
                });

                // Payment calculation
                const paymentInput = document.getElementById('paymentAmount');
                const initialBalanceAfterPayment = {{ $balanceAfterCase }};
                const initialNetTotal = {{ $case->client->balance }};

                if (paymentInput) {
                    paymentInput.addEventListener('input', function() {
                        const paymentAmount = parseFloat(this.value) || 0;

                        // Update balance after payment
                        const newBalanceAfterPayment = initialBalanceAfterPayment - paymentAmount;
                        document.getElementById('balanceAfterPayment').textContent = newBalanceAfterPayment.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                        // Update net total
                        const newNetTotal = initialNetTotal - paymentAmount;
                        document.getElementById('netTotal').textContent = newNetTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    });
                }
            });
        </script>
    </body>
</html>
