<?php
// Lightweight print layout - no sidebar/navbar chrome
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List - WMS</title>
    <link rel="stylesheet" href="<?= e(APP_URL) ?>/assets/css/bootstrap.min.css">
    <style>
        body { background: #fff; font-size: 11px; }
        .table th { background: #f8f9fa !important; font-size: 11px; }
        .table td { font-size: 11px; }
        h4 { font-size: 16px; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-0 fw-bold">Customer List</h4>
                <div class="text-muted">Generated on <?= date('d M Y, h:i A') ?></div>
            </div>
            <div class="no-print d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm">Print Again</button>
                <button onclick="window.close()" class="btn btn-secondary btn-sm">Close</button>
            </div>
        </div>

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Company</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr><td colspan="11" class="text-center">No customers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($customers as $i => $c): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($c['customer_code']) ?></td>
                            <td><?= e($c['customer_name']) ?></td>
                            <td><?= e($c['customer_type']) ?></td>
                            <td><?= e($c['company_name']) ?></td>
                            <td><?= e($c['mobile']) ?></td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= e($c['city']) ?></td>
                            <td><?= e($c['country']) ?></td>
                            <td class="text-end"><?= number_format($c['current_balance'], 2) ?> <?= $c['balance_type'][0] ?></td>
                            <td><?= $c['deleted_at'] ? 'Deleted' : ucfirst(e($c['status'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-muted mt-2">Total Records: <?= count($customers) ?></div>
    </div>
</body>
</html>
