

<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<style>
    /* General Body Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
    }

    /* Print Button */
    .print-button-container {
        margin-bottom: 20px;
        text-align: center;
    }

    .btn-print {
        background: linear-gradient(135deg, #1E4157 0%, #043c4d 100%);
        border: none;
        border-radius: 10px;
        padding: 14px 32px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(30, 65, 87, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
        min-width: 200px;
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(30, 65, 87, 0.4);
    }

    .btn-print i {
        margin-right: 8px;
    }

    /* Receipt Container */
    .receipt-container {
        width: 500px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        border: 2px solid #1E4157;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
        position: relative;
        transform: scale(0.9);
        transform-origin: top center;
    }

    /* Header */
    .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #1E4157;
        padding-bottom: 15px;
    }

    .header img {
        max-width: 120px;
        margin-bottom: 10px;
    }

    .header h1 {
        font-size: 24px;
        margin: 5px 0;
        color: #1E4157;
        font-weight: bold;
    }

    .header p {
        font-size: 13px;
        color: #666;
        margin: 3px 0;
    }

    /* Voucher Title */
    .voucher-title {
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 25px;
        color: #1E4157;
        padding: 10px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 5px;
    }

    /* Info Rows */
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .info-row div {
        flex: 1;
    }

    .info-row .label {
        font-weight: bold;
        color: #1E4157;
    }

    .info-row .value {
        text-align: right;
    }

    /* Table Container */
    .table-container {
        margin-top: 25px;
        margin-bottom: 30px;
        border: 2px solid #1E4157;
        border-radius: 5px;
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px 8px;
        text-align: left;
    }

    th {
        background: linear-gradient(135deg, #1E4157 0%, #043c4d 100%);
        color: white;
        font-weight: bold;
        text-align: center;
    }

    tbody tr:nth-child(odd) {
        background-color: #f8f9fa;
    }

    tbody tr:nth-child(even) {
        background-color: #ffffff;
    }

    /* Signature Section */
    .signature-section {
        margin-top: 80px;
        padding-top: 20px;
        border-top: 2px dashed #ccc;
    }

    .signature-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .signature-row .label {
        font-weight: bold;
        color: #1E4157;
    }

    .signature-line {
        display: inline-block;
        border-bottom: 1px solid #000;
        min-width: 150px;
        margin-left: 10px;
    }

    .footer-note {
        margin-top: 20px;
        font-size: 11px;
        color: #999;
        text-align: center;
    }

    /* Print Styles - A4 Portrait */
    @media  print {
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        html,
        body {
            background: white !important;
            width: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* The application shell must never be part of the printed voucher. */
        html body.white-content .wrapper > .sidebar,
        html body .wrapper > .sidebar,
        body.white-content .solent-floating-topbar,
        body.white-content .solent-quick-nav,
        .solent-floating-topbar,
        .solent-quick-nav,
        .main-panel > .navbar,
        .main-panel > footer,
        .sidebar,
        .print-button-container,
        .btn-print,
        nav,
        .navbar,
        .footer,
        .overlay,
        #overlay {
            display: none !important;
        }

        .wrapper {
            display: block !important;
            min-height: 0 !important;
            overflow: visible !important;
        }

        html body.white-content .wrapper > .main-panel,
        html body .wrapper > .main-panel,
        .main-panel,
        .container-fluid {
            float: none !important;
            width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            transform: none !important;
            overflow: visible !important;
        }

        html body.white-content .wrapper > .main-panel > .content,
        html body .wrapper > .main-panel > .content,
        .main-panel > .content,
        .content {
            width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            transform: none !important;
            overflow: visible !important;
        }

        .voucher-page,
        .voucher-page > .col-md-12 {
            display: block !important;
            float: none !important;
            flex: none !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .receipt-container {
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: none !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 8mm !important;
            border: none !important;
            box-shadow: none !important;
            transform: none !important;
            overflow: visible !important;
            font-size: 9pt;
        }

        .header {
            margin-bottom: 4mm;
            padding-bottom: 3mm;
        }

        .header img {
            max-width: 26mm;
            max-height: 24mm;
        }

        .header h1 {
            font-size: 15pt;
            margin: 1mm 0;
        }

        .header p {
            font-size: 8pt !important;
            margin: 1mm 0 !important;
        }

        .voucher-title {
            font-size: 12pt;
            margin-bottom: 4mm;
            padding: 2.5mm;
        }

        .info-row {
            font-size: 9pt;
            margin-bottom: 2mm;
        }

        .table-container {
            margin-top: 4mm;
            margin-bottom: 6mm;
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            table-layout: fixed;
            font-size: 8.5pt;
        }

        th, td {
            padding: 2.2mm;
            overflow-wrap: anywhere;
        }

        thead {
            display: table-header-group;
        }

        tr,
        .header,
        .voucher-title,
        .info-section,
        .signature-section {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .signature-section {
            margin-top: 12mm;
            padding-top: 4mm;
        }

        .signature-row {
            font-size: 9pt;
            margin-bottom: 3mm;
        }

        .signature-line {
            min-width: 35mm;
        }

        .footer-note {
            font-size: 7.5pt;
            margin-top: 3mm;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="row voucher-page">
    <?php
        $voucherBrandName = $brandingName ?? config('branding.defaults.name', 'Solent');
        $voucherBrandLogo = asset($brandingLogoPath ?? config('branding.defaults.logo_path'));
        $voucherContact = data_get($brandingSettings ?? null, 'copy.contact');
    ?>
    <div class="col-md-12">
        <div class="print-button-container">
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i>
                Print Voucher
            </button>
        </div>

        <div class="receipt-container">
            <!-- Header -->
            <div class="header">
                <img src="<?php echo e($voucherBrandLogo); ?>" alt="<?php echo e($voucherBrandName); ?> Dental Laboratory Logo">
                <h1><?php echo e($voucherBrandName); ?> Dental Laboratory</h1>

                <?php if($voucherContact): ?>
                    <p style="font-size: 11px; margin-top: 5px;"><?php echo e($voucherContact); ?></p>
                <?php endif; ?>
            </div>

            <!-- Voucher Title -->
            <div class="voucher-title">Receipt Voucher | سند استلام</div>

            <!-- Case Information -->
            <div class="info-row">
                <div><span class="label">Dentist:</span> <?php echo e($case->client->name); ?></div>
                <div class="value"><span class="label">Date:</span> <?php echo e(now()->format('Y-m-d H:i')); ?></div>
            </div>
            <div class="info-row">
                <div><span class="label">Patient:</span> <?php echo e($case->patient_name); ?></div>
                <div class="value"><span class="label">Case ID:</span> <?php echo e($case->case_id); ?></div>
            </div>

            <!-- Jobs Table -->
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Job Type</th>
                        <th>Material</th>
                        <th>Color</th>
                        <th>Style</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    <?php $__currentLoopData = $case->jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="text-align: center;"><?php echo e($i); ?></td>
                        <td><?php echo e($job->jobType->name ?? '-'); ?></td>
                        <td><?php echo e($job->material->name ?? '-'); ?></td>
                        <td style="text-align: center;"><?php echo e($job->color == 0 ? 'None' : $job->color); ?></td>
                        <td><?php echo e($job->style ?? '-'); ?></td>
                        <td style="text-align: center;"><?php echo e($job->unit_num ? count(explode(',', $job->unit_num)) : 0); ?></td>
                    </tr>
                        <?php $i++; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                    <?php for($j = $i; $j <= 5; $j++): ?>
                    <tr>
                        <td style="text-align: center;"><?php echo e($j); ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-row">
                    <div>
                        <span class="label">Received By:</span>
                        <span class="signature-line"></span>
                    </div>
                    <div style="text-align: right;">
                        <span class="label">Stamp / Signature:</span>
                        <span class="signature-line"></span>
                    </div>
                </div>

                <div class="footer-note">
                    Thank you for choosing <?php echo e($voucherBrandName); ?> Dental Lab
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['pageSlug' => 'View' . ' '. $voucher], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Yazan\Desktop\cCL\alsolent\alsolent_web_app\resources\views/delivery/view-voucher.blade.php ENDPATH**/ ?>
