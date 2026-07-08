<?php
// Custom layout for printing without sidebar/navbar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier List - WMS</title>
    <link rel="stylesheet" href="<?= e(APP_URL) ?>/assets/css/bootstrap.min.css">
    <style>
        body { background: #fff; font-size: 12px; }
        .table th { background: #f8f9fa !important; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between mb-4">
            <div>
                <h3 class="mb-0">Supplier List</h3>
                <div class="text-muted">Generated on <?= date('d M Y, h:i A') ?></div>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-primary btn-sm">Print Again</button>
                <button onclick="window.close()" class="btn btn-secondary btn-sm">Close</button>
            </div>
        </div>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Company Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($suppliers)): ?>
                    <tr><td colspan="9" class="text-center">No suppliers found.</td></tr>
                <?php else: ?>
                    <?php foreach($suppliers as $s): ?>
                        <tr>
                            <td><?= e($s['supplier_code']) ?></td>
                            <td><?= e($s['company_name']) ?></td>
                            <td><?= e($s['contact_person']) ?></td>
                            <td><?= e($s['phone']) ?></td>
                            <td><?= e($s['email']) ?></td>
                            <td><?= e($s['city']) ?></td>
                            <td><?= e($s['country']) ?></td>
                            <td class="text-end"><?= number_format($s['opening_balance'], 2) ?> <?= $s['balance_type'][0] ?></td>
                            <td><?= $s['deleted_at'] ? 'Deleted' : ucfirst(e($s['status'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
