<?php
require_once("../private/initialize.php");

$page_title = "Admin Page";
$restaurants = $db->fetchAllRestaurants();

include '../private/shared/admin_header.php';
?>

<div class="full-screen-height">
    <h1 class="text-3xl font-bold mb-8 text-center text-black mt-10 pt-2">Admin Dashboard</h1>

    <div class="overflow-x-auto px-5 mx-5 py-3">
        <table class="min-w-full border-collapse bg-white rounded-xl shadow-md overflow-hidden whitespace-nowrap">
            <thead>
                <tr class="tomato-bg text-white">
                    <th class="py-3 px-5 text-left text-sm font-semibold">ID</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Name</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Email</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Phone</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">FSSAI</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Location</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Website</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Tags</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Status</th>
                    <th class="py-3 px-5 text-left text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($restaurants)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5 text-gray-500">No records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($restaurants as $row): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="py-3 px-5 font-medium text-gray-900"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['ph']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['fssai']); ?></td>
                            <td class="py-3 px-5"><?php echo htmlspecialchars($row['city'] . ', ' . $row['district'] . ', ' . $row['state'] . ' - ' . $row['pincode']); ?></td>
                            <td class="py-3 px-5">
                                <a href="<?php echo htmlspecialchars($row['website']); ?>" class="text-blue-600 hover:underline" target="_blank">
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
    function updateStatus(id, status) {
        if (!confirm(`Are you sure you want to set this as ${status}?`)) return;

        fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}&status=${status}`
            })
            .then(res => res.text())
            .then(data => {
                console.log('Server response:', data);
                alert(data);
                location.reload();
            })
            .catch(err => console.error(err));
    }
</script>