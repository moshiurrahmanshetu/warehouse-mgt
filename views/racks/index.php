<?php require_once INCLUDE_PATH . '/header.php'; require_once INCLUDE_PATH . '/navbar.php'; ?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Racks</h2>
            <?php if(hasPermission('racks.manage')): ?>
            <a href="racks.php?action=create" class="btn btn-primary">Add New</a>
            <?php endif; ?>
        </div>
        <?php renderFlash(); ?>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th><th>Code</th><th>Name</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= e($item['id']) ?></td>
                            <td><?= e($item['rack_code']) ?></td>
                            <td><?= e($item['rack_name']) ?></td>
                            <td><span class="badge bg-<?= $item['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst(e($item['status'])) ?></span></td>
                            <td>
                                <?php if(hasPermission('racks.manage')): ?>
                                <a href="racks.php?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <form action="racks.php?action=delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this Rack?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let input = document.createElement("input");
        input.type = "text";
        input.className = "form-control w-25 mb-3";
        input.placeholder = "Search...";
        document.querySelector(".card-body").prepend(input);
        input.addEventListener("keyup", function() {
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll("#dataTable tbody tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    });
</script>
<?php require_once INCLUDE_PATH . '/footer.php'; ?>