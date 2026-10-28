<?php
require_once("../private/initialize.php");

$page_title = "Admin Page";
$businesses = $db->fetchAllbusinesses();

// Pagination settings
// Pagination settings
if (isset($_GET['limit'])) {
    $per_page = ($_GET['limit'] === 'all') ? 'all' : intval($_GET['limit']);
} else {
    $per_page = 20; // default
}
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * ($per_page !== 'all' ? $per_page : 0);


// Count total (excluding deleted)
$total_stmt = $db->connection->query("SELECT COUNT(*) as total FROM businesses WHERE status != 'deleted'");
$total_businesses = $total_stmt->fetch_assoc()['total'];

// Fetch paginated data (excluding deleted)
if ($per_page === 'all') {
    $stmt = $db->connection->query("SELECT * FROM businesses WHERE status != 'deleted' ORDER BY id DESC");
} else {
    $stmt = $db->connection->prepare("SELECT * FROM businesses WHERE status != 'deleted' ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $per_page, $offset);
    $stmt->execute();
    $stmt = $stmt->get_result();
}


$businesses = ($per_page === 'all') ? $stmt->fetch_all(MYSQLI_ASSOC) : $stmt->fetch_all(MYSQLI_ASSOC);

// Total pages
$total_pages = ($per_page === 'all') ? 1 : ceil($total_businesses / $per_page);

include '../private/shared/admin_header.php';
?>

<div class="full-screen-height">
    <div class="text-center flex justify-center admin-title-heading-container px-5">
        <h1 class="text-5xl font-bold mb-8 text-center text-black mt-4 py-3 admin-title-heading">MeFoodie</h1>
    </div>
    <div class="text-center flex justify-center admin-dashboard-heading-container">
        <h4 class=" font-semibold mb-8 text-center text-black mt-4 py-3 rounded-lg admin-dashboard-heading">Dashboard</h4>
    </div>
    <!-- Table Footer Controls -->
    <div class="flex justify-between items-center px-5 py-4 border-t bg-white mt-2">

        <!-- Left side: Data per page dropdown -->
        <div class="flex items-center space-x-2">
            <label for="limit" class="text-sm text-gray-600 font-medium">Show Data:</label>
            <select id="limit" name="limit" class="border border-gray-300 text-gray-700 rounded-md px-2 py-1 text-sm focus:ring focus:ring-blue-200"
                onchange="changeLimit(this.value)">
                <option value="20" <?= ($per_page == 20 || !isset($_GET['limit'])) ? 'selected' : '' ?>>20</option>
                <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50</option>
                <option value="200" <?= ($per_page == 200) ? 'selected' : '' ?>>200</option>
                <option value="all" <?= ($per_page === 'all') ? 'selected' : '' ?>>All</option>
            </select>
            <span class="ml-2 text-gray-500 text-sm">(<?= number_format($total_businesses) ?> total)</span>

        </div>

        <!-- Right side: Pagination -->
        <div id="pagination" class="flex items-center space-x-2">
            <?php if ($total_pages > 1 && $per_page !== 'all'): ?>
                <div class="flex items-center space-x-1">
                    <!-- Prev -->
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&limit=<?= $per_page ?>" class="px-3 py-1 border rounded hover:bg-gray-100">&lt;</a>
                    <?php endif; ?>

                    <!-- Always show first page -->
                    <a href="?page=1&limit=<?= $per_page ?>"
                        class="px-3 py-1 border rounded <?= ($page == 1) ? 'bg-red-500 text-white' : 'bg-red-100' ?>">01</a>

                    <!-- Left dots -->
                    <?php if ($page > 3): ?>
                        <span class="px-2">...</span>
                    <?php endif; ?>

                    <!-- Pages near current -->
                    <?php
                    $start = max(2, $page - 1);
                    $end   = min($total_pages - 1, $page + 1);
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="?page=<?= $i ?>&limit=<?= $per_page ?>"
                            class="px-3 py-1 border rounded <?= ($i == $page) ? 'bg-red-500 text-white' : 'bg-red-100 hover:bg-red-200' ?>">
                            <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Middle page -->
                    <?php
                    if ($total_pages > 10) {
                        $middle = floor($total_pages / 2);
                        if ($middle > $end + 1 && abs($middle - $page) > 2) {
                            echo '<span class="px-2">...</span>';
                            echo '<a href="?page=' . $middle . '&limit=' . $per_page . '" class="px-3 py-1 border rounded bg-red-100 hover:bg-red-200">'
                                . str_pad($middle, 2, '0', STR_PAD_LEFT) . '</a>';
                        }
                    }
                    ?>

                    <!-- Right dots -->
                    <?php if ($page < $total_pages - 2): ?>
                        <span class="px-2">...</span>
                    <?php endif; ?>

                    <!-- Last page -->
                    <?php if ($total_pages > 1): ?>
                        <a href="?page=<?= $total_pages ?>&limit=<?= $per_page ?>"
                            class="px-3 py-1 border rounded <?= ($page == $total_pages) ? 'bg-red-500 text-white' : 'bg-red-100 hover:bg-red-200' ?>">
                            <?= str_pad($total_pages, 2, '0', STR_PAD_LEFT) ?>
                        </a>
                    <?php endif; ?>

                    <!-- Next -->
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>&limit=<?= $per_page ?>" class="px-3 py-1 border rounded hover:bg-gray-100">&gt;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>


    <div class="overflow-x-auto px-5 mx-5 py-3">
        <table class="min-w-full border-collapse bg-white rounded-sm shadow-md overflow-hidden whitespace-nowrap">
            <thead>
                <tr class="table-top text-black">
                    <th class="py-3 px-5 text-left table-title font-semibold">ID</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Name</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Email</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Phone</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">FSSAI</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Location</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Website</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Tags</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Status</th>
                    <th class="py-3 px-5 text-left table-title font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($businesses)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5 text-gray-500">No records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($businesses as $row): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="py-3 px-5 font-medium text-gray-900"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['ph']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['fssai']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['city'] . ', ' . $row['district'] . ', ' . $row['state'] . ' - ' . $row['pincode']); ?></td>
                            <td class="py-3 px-5">
                                <a href="<?php echo htmlspecialchars($row['website']); ?>" class="text-blue-600 hover:underline truncate" target="_blank">
                                    <?php echo htmlspecialchars($row['website']); ?>
                                </a>
                            </td>
                            <td class="py-3 px-5">
                                <?php
                                $tags = is_array($row['tags']) ? $row['tags'] : json_decode($row['tags'], true);
                                if (!empty($tags)) {
                                    echo '<span class="inline-flex flex-wrap gap-1">';
                                    foreach ($tags as $tag) {
                                        echo '<span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full shadow-sm">' . htmlspecialchars($tag) . '</span>';
                                    }
                                    echo '</span>';
                                } else {
                                    echo '<span class="text-gray-400">No tags</span>';
                                }
                                ?>
                            </td>
                            <td class="py-3 px-5 font-semibold">
                                <?php
                                $status = strtolower($row['status']);
                                $color = match ($status) {
                                    'approved' => 'text-green-600',
                                    'disapproved' => 'text-yellow-600',
                                    'deleted' => 'text-red-600',
                                    default => 'text-gray-500'
                                };
                                echo "<span class='$color'>" . ucfirst($status) . "</span>";
                                ?>
                            </td>
                            <td class="py-3 px-5 space-x-2">
                                <button onclick="updateStatus(<?php echo $row['id']; ?>, 'approved')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm shadow">Approve</button>
                                <button onclick="updateStatus(<?php echo $row['id']; ?>, 'disapproved')" class="bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded-md text-sm shadow">Disapprove</button>
                                <button onclick="updateStatus(<?php echo $row['id']; ?>, 'deleted')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm shadow">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
<footer class="text-center text-gray-500 py-4 border-t mt-10 text-sm">
    &copy; <?php echo date("Y"); ?> MeFoodie Admin Panel — DigiMaraa Technologies
</footer>
</body>

</html>

<script>
    const csrfToken = "<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>";

    function updateStatus(id, status) {
        if (!confirm(`Are you sure you want to set this as ${status}?`)) return;

        fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}&csrf_token=${encodeURIComponent(csrfToken)}`
            })
            .then(res => res.text())
            .then(data => {
                console.log('Server response:', data);
                alert(data);
                location.reload();
            })
            .catch(err => console.error(err));
    }



    function changeLimit(limit) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('limit', limit);
        urlParams.set('page', 1);
        window.location.search = urlParams.toString();
    }
</script>