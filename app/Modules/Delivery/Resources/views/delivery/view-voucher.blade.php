

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

    /* Print Styles - A6 Portrait */
    @media  print {
        body {
            background: white !important;
            margin: 0;
            padding: 0;
        }

        /* Hide all layout elements */
        .sidebar,
        .main-panel > .navbar,
        .main-panel > footer,
        .print-button-container,
        .btn-print,
        nav,
        .navbar,
        .footer,
        .main-panel > .content > .container-fluid > .row > .col-md-12 > *:not(.receipt-container) {
            display: none !important;
        }

        /* Hide everything except receipt-container */
        body > *:not(.wrapper) {
            display: none !important;
        }

        .wrapper,
        .main-panel,
        .content,
        .container-fluid,
        .row,
        .col-md-12 {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
        }

        .receipt-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 5mm;
            border: none;
            box-shadow: none;
            transform: none;
            font-size: 8px;
        }

        .header {
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .header img {
            max-width: 50px;
        }

        .header h1 {
            font-size: 12px;
            margin: 2px 0;
        }

        .header p {
            font-size: 7px;
            margin: 1px 0;
        }

        .voucher-title {
            font-size: 10px;
            margin-bottom: 5px;
            padding: 3px;
        }

        .info-row {
            font-size: 7px;
            margin-bottom: 3px;
        }

        .table-container {
            margin-top: 5px;
            margin-bottom: 10px;
        }

        table {
            font-size: 7px;
        }

        th, td {
            padding: 2px 3px;
        }

        .signature-section {
            margin-top: 15px;
            padding-top: 5px;
            page-break-inside: avoid;
        }

        .signature-row {
            font-size: 7px;
            margin-bottom: 5px;
        }

        .signature-line {
            min-width: 50px;
        }

        .footer-note {
            font-size: 6px;
            margin-top: 5px;
        }

        @page {
            size: A6 portrait;
            margin: 5mm;
        }

        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="row">
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
                <img src="<?php echo e(asset('assets/images/hikaro-logo.png')); ?>" alt="Hikaro Tech Logo">
                <h1>Albasma Dental Laboratory</h1>

                <p style="font-size: 11px; margin-top: 5px;">
                    📞 1100 726 02 | 1100 726 079 | 📧 Albasmadentallab@gmail.com
                </p>
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
                    Thank you for choosing Albasma Dental Lab
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['pageSlug' => 'View' . ' '. $voucher], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Yazan\Desktop\cCL\Albasma\Albasma_web_app\resources\views/delivery/view-voucher.blade.php ENDPATH**/ ?>
