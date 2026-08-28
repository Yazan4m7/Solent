<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    @php
        $brandLogo = asset($brandingLogoPath ?? config('branding.defaults.logo_path'));
        $brandTitle = $brandingName ?? config('branding.defaults.name');
        $currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
        $currencyUnitAr = (string) ($currencyContext['unit_ar'] ?? 'دينار');
        $currencyNameAr = (string) ($currencyContext['name_ar'] ?? 'دينار أردني');
        $currencyPhraseAr = $currencyNameAr . ' لا غير';
        $totalInvoiceAmount = 0;
        $rowCount = 0;
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandTitle }} | Invoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: {{ config('branding.defaults.primary_color') }};
            --brand-secondary: {{ config('branding.defaults.secondary_color') }};
            --brand-accent: {{ config('branding.defaults.accent_color') }};
            --brand-surface: #ffffff;
            --brand-muted: #6b7280;
        }

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
            background-color: var(--brand-primary);
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
            background-color: var(--brand-primary);
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
            color: var(--brand-accent);
        }

        header .title h2 {
            margin: 0;
            font-size: 1.4em;
            font-weight: bold;
            letter-spacing: 1px;
            color: var(--brand-accent);
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
            color: var(--brand-primary);
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
            background: var(--brand-secondary);
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid var(--brand-accent);
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
            border-top: 2px solid var(--brand-accent);
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
            color: var(--brand-primary);
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
            color: var(--brand-primary);
            font-size: 0.7em;
            vertical-align: super;
            margin-left: 3px;
        }

        .edit-icon:hover {
            color: var(--brand-accent);
        }

        .editable-value {
            display: inline-block;
            min-width: 60px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }

            .edit-icon {
                display: none !important;
            }

            html,
            body {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                background: #fff;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-button {
                display: none !important;
            }

            .invoice-box {
                box-sizing: border-box;
                position: relative;
                width: 100%;
                max-width: none;
                min-height: 0;
                height: auto;
                margin: 0;
                padding: 8mm;
                overflow: visible;
                box-shadow: none;
                border: 0;
                font-size: 9pt;
            }

            .invoice-box .top-bar {
                height: 3mm;
            }

            .invoice-box header {
                margin-top: 3mm;
                padding-bottom: 4mm;
            }

            .invoice-box .logo {
                width: 24mm;
                height: 24mm;
            }

            .invoice-box .serial-number {
                margin-top: 1.5mm;
                font-size: 10pt;
            }

            .invoice-box header .title h1 {
                font-size: 19pt;
            }

            .invoice-box header .title h2 {
                font-size: 12pt;
            }

            .invoice-box header .title .address,
            .invoice-box header .contact-info {
                font-size: 8pt;
            }

            .invoice-box .meta-info {
                padding: 3mm 0;
                font-size: 9pt;
            }

            .invoice-box .table-container {
                margin-top: 2mm;
            }

            .invoice-box table {
                width: 100%;
                table-layout: fixed;
            }

            .invoice-box th,
            .invoice-box td {
                padding: 2.2mm;
                font-size: 8.5pt;
                overflow-wrap: anywhere;
            }

            .invoice-box thead {
                display: table-header-group;
            }

            .invoice-box tbody tr {
                height: 10mm;
            }

            .invoice-box tr,
            .invoice-box header,
            .invoice-box .meta-info,
            .invoice-box footer {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .invoice-box footer {
                margin-top: 4mm;
                padding-top: 3mm;
                font-size: 8pt;
            }
        }
    </style>
</head>
<body>

    <button class="print-button" onclick="window.print()">Print</button>

    <div class="invoice-box">
        <div class="top-bar"></div>

        <header>
            <div class="logo-area">
                <div class="serial-number">
                    № {{ $case->id }}
                </div>
            </div>

            <div class="title">
                <img src="{{ $brandLogo }}" alt="{{ $brandingName ?? 'Brand' }} Logo" class="logo">

                <h2>{{ $brandTitle }}</h2>
                <div class="address">Digital dental laboratory</div>
            </div>

            <div class="contact-info">
                <div class="phones" style="text-align: left">
                    <span>{{ $brandTitle }} Digital Dental Lab</span>
                    <span>Precision - Reliability - Speed</span>
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
                        <th>الإجمالي<br>{{ $currencyUnitAr }}</th>
                        <th>التفاصيل</th>
                        <th>العدد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($case->jobs as $job)
                        @php
                            if($job->is_modification) continue;

                            $unitsAmount = count(explode(',', $job->unit_num));

                            if (isset($job->unit_price) && $job->unit_price > 0)
                                $totalJobPrice = $unitsAmount * $job->unit_price;
                            else
                                $totalJobPrice = $unitsAmount * ($job->material?->price ?? 0);

                            $totalInvoiceAmount += $totalJobPrice;

                            $details = ($job->jobType?->name ?? 'No job type') . ' - ' . ($job->material?->name ?? 'No material');
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
                                    دفعة <span id="paymentInWords" style="display: inline-block;">............................................................ {{ $currencyPhraseAr }}</span>
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

                <span>Precision dental restorations</span>
                <span>Trusted digital workflows</span>
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
                const currencyPhraseAr = @json($currencyPhraseAr);

                if (value > 0) {
                    paymentInWordsSpan.innerText = numberToWords(value) + " " + currencyPhraseAr;
                } else {
                    paymentInWordsSpan.innerText = "............................................................ " + currencyPhraseAr;
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

