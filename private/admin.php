<?php require_once("../private/initialize.php") ?>

<?php $page_title = "Admin Page"; ?>
<link rel="stylesheet" href="../../public/stylesheets/style.css">

<!-- Header link  -->
<?php include '../private/shared/header.php'; ?>

<?php
// Placeholder data
$businesses = [
    [
        'id' => 1,
        'name' => 'MeFoodie A',
        'email' => 'contact@mefoodie.com',
        'phone' => '9876543210',
        'fssai' => '12345678901234',
        'state' => 'Tamil Nadu',
        'area' => 'Chennai',
        'pincode' => '600001',
        'website' => 'https://mefoodie.com',
        'tags' => 'fast food, vegan',
        'status' => 'pending'
    ],
    [
        'id' => 2,
        'name' => 'SpiceVilla Restaurant',
        'email' => 'info@spicevilla.in',
        'phone' => '9876501234',
        'fssai' => '22345678901234',
        'state' => 'Karnataka',
        'area' => 'Bengaluru',
        'pincode' => '560001',
        'website' => 'https://spicevilla.in',
        'tags' => 'south indian, non-veg',
        'status' => 'approved'
    ],
    [
        'id' => 3,
        'name' => 'Urban Bite Café',
        'email' => 'hello@urbanbite.com',
        'phone' => '9812345678',
        'fssai' => '32345678901234',
        'state' => 'Maharashtra',
        'area' => 'Mumbai',
        'pincode' => '400001',
        'website' => 'https://urbanbite.com',
        'tags' => 'café, bakery',
        'status' => 'pending'
    ],
    [
        'id' => 4,
        'name' => 'GreenLeaf Organic Store',
        'email' => 'support@greenleaforganic.in',
        'phone' => '9823456789',
        'fssai' => '42345678901234',
        'state' => 'Kerala',
        'area' => 'Kochi',
        'pincode' => '682001',
        'website' => 'https://greenleaforganic.in',
        'tags' => 'organic, grocery',
        'status' => 'approved'
    ],
    [
        'id' => 5,
        'name' => 'BakeWorld',
        'email' => 'orders@bakeworld.com',
        'phone' => '9945678123',
        'fssai' => '52345678901234',
        'state' => 'Telangana',
        'area' => 'Hyderabad',
        'pincode' => '500001',
        'website' => 'https://bakeworld.com',
        'tags' => 'bakery, desserts',
        'status' => 'disapproved'
    ],
    [
        'id' => 6,
        'name' => 'The Curry House',
        'email' => 'hello@thecurryhouse.in',
        'phone' => '9001234567',
        'fssai' => '62345678901234',
        'state' => 'Delhi',
        'area' => 'Connaught Place',
        'pincode' => '110001',
        'website' => 'https://thecurryhouse.in',
        'tags' => 'north indian, spicy',
        'status' => 'pending'
    ],
    [
        'id' => 7,
        'name' => 'Street Treats',
        'email' => 'contact@streettreats.in',
        'phone' => '9988776655',
        'fssai' => '72345678901234',
        'state' => 'Gujarat',
        'area' => 'Ahmedabad',
        'pincode' => '380001',
        'website' => 'https://streettreats.in',
        'tags' => 'street food, snacks',
        'status' => 'approved'
    ],
    [
        'id' => 8,
        'name' => 'Juice Junction',
        'email' => 'info@juicejunction.in',
        'phone' => '9877001122',
        'fssai' => '82345678901234',
        'state' => 'Madhya Pradesh',
        'area' => 'Indore',
        'pincode' => '452001',
        'website' => 'https://juicejunction.in',
        'tags' => 'juices, smoothies',
        'status' => 'pending'
    ],
    [
        'id' => 9,
        'name' => 'Tandoor Tales',
        'email' => 'admin@tandoortales.in',
        'phone' => '9765432109',
        'fssai' => '92345678901234',
        'state' => 'Punjab',
        'area' => 'Amritsar',
        'pincode' => '143001',
        'website' => 'https://tandoortales.in',
        'tags' => 'tandoori, grill, non-veg',
        'status' => 'approved'
    ],
    [
        'id' => 10,
        'name' => 'Cafe Mocha Town',
        'email' => 'hello@cafemochatown.com',
        'phone' => '9090909090',
        'fssai' => '10345678901234',
        'state' => 'West Bengal',
        'area' => 'Kolkata',
        'pincode' => '700001',
        'website' => 'https://cafemochatown.com',
        'tags' => 'coffee, bakery, snacks',
        'status' => 'pending'
    ]
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="">

    <h1 class="text-3xl font-bold mb-8 text-center text-black mt-10 pt-2">Admin Dashboard</h1>

    <!-- Scrollable Table Container -->
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
                <?php foreach ($businesses as $row) : ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="py-3 px-5"><?php echo $row['id']; ?></td>
                        <td class="py-3 px-5 font-medium text-gray-900"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="py-3 px-5"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="py-3 px-5"><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td class="py-3 px-5"><?php echo htmlspecialchars($row['fssai']); ?></td>
                        <td class="py-3 px-5"><?php echo htmlspecialchars($row['area'] . ', ' . $row['state'] . ' - ' . $row['pincode']); ?></td>
                        <td class="py-3 px-5">
                            <a href="<?php echo htmlspecialchars($row['website']); ?>" class="text-blue-600 hover:underline" target="_blank">
                                <?php echo htmlspecialchars($row['website']); ?>
                            </a>
                        </td>
                        <td class="py-3 px-5"><?php echo htmlspecialchars($row['tags']); ?></td>
                        <td class="py-3 px-5 font-semibold text-yellow-600"><?php echo ucfirst($row['status']); ?></td>
                        <td class="py-3 px-5 space-x-2">
                            <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm shadow">
                                Approve
                            </button>
                            <button class="bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded-md text-sm shadow">
                                Disapprove
                            </button>
                            <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm shadow">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer link  -->
    <?php include '../private/shared/footer.php'; ?>