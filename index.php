<?php
require_once("private/initialize.php");

$page_title = "Home Page";

$restaurants = $db->fetchApprovedRestaurants(); // only approved ones
?>

<!-- Header link  -->
<?php include 'private/shared/header.php'; ?>
<link rel="stylesheet" href="public/stylesheets/style.css">
<script src="https://cdn.tailwindcss.com"></script>

<main class="min-h-screen flex flex-col items-center p-4 mt-5">
    <h1 class="h1 font-bold text-blue-600">Welcome to MeFoodie 🍽️</h1>

    <div class="max-w-6xl w-full">
        <h1 class="text-3xl font-bold text-center mb-8">Explore Nearby</h1>

        <!-- Grid Layout -->
        <div id="restaurant-list" class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 url-card">
            <?php if (!empty($restaurants)) : ?>
                <?php foreach ($restaurants as $r) : ?>
                    <?php if (!empty($r['website']) && !empty($r['name'])) : ?>
                        <a href="<?php echo htmlspecialchars($r['website']); ?>" target="_blank"
                            class="url-card block bg-white rounded-2xl shadow-md hover:shadow-shadow-tomato transition-all p-6 text-center">
                            <h3 class="h2"><?php echo htmlspecialchars($r['name']); ?></h3>
                            <p class="text-sm text-gray-500 mt-2"><?php echo htmlspecialchars($r['website']); ?></p>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-gray-500 text-center">No websites found.</p>
            <?php endif; ?>
        </div>

    </div>
</main>

<script>
    // ===============================
    // 🧭 GEOLOCATION + SMART LOADING
    // ===============================
    document.addEventListener("DOMContentLoaded", () => {
        const listContainer = document.getElementById("restaurant-list");

        // 1️⃣ Try to get user's geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(loadNearby, handleGeoError, {
                enableHighAccuracy: true,
                timeout: 2000
            });
        } else {
            handleGeoError("Geolocation not supported");
        }

        // ===============================
        // ✅ Load restaurants near user
        // ===============================
        function loadNearby(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            console.log("📍 User Location:", lat, lon);

            // Fetch nearby restaurants (sorted by distance)
            fetch(`private/get_nearby_restaurants.php?lat=${lat}&lon=${lon}`)
                .then(res => {
                    if (!res.ok) throw new Error("Network error");
                    return res.json();
                })
                .then(data => renderRestaurants(data))
                .catch(err => {
                    console.error("❌ Fetch failed:", err);
                    listContainer.innerHTML = `<p class='text-gray-500 text-center'>Error fetching nearby restaurants.</p>`;
                });
        }

        // ===============================
        // ⚙️ Handle location errors
        // ===============================
        function handleGeoError(error) {
            console.warn("⚠️ Geolocation unavailable:", error.message || error);

            // Fallback: Load default restaurants (no location)
            fetch("private/get_nearby_restaurants.php")
                .then(res => res.json())
                .then(data => renderRestaurants(data))
                .catch(err => {
                    console.error("❌ Fetch failed:", err);
                    listContainer.innerHTML = `<p class='text-gray-500 text-center'>Unable to load restaurants.</p>`;
                });
        }

        // ===============================
        // 🎨 Render restaurant cards
        // ===============================
        function renderRestaurants(data) {
            if (!data || data.length === 0) {
                listContainer.innerHTML = `<p class='text-gray-500 text-center mt-6'>No restaurants found nearby.</p>`;
                return;
            }

            listContainer.innerHTML = data.map(r => `
            <a href="${r.website}" target="_blank" 
               class="block bg-white rounded-2xl shadow-md hover:shadow-shadow-tomato transition-all p-6 text-center">
                <h3 class="text-lg font-semibold text-tomato mb-1">${r.name}</h3>
                <p class="text-gray-600 text-sm">${r.city}, ${r.state}</p>
                <p class="text-blue-500 text-sm mt-2 truncate hover:underline">${r.website}</p>
<!--                  ${r.distance ? `<p class="text-xs text-gray-400 mt-1">${r.distance.toFixed(2)} km away</p>` : ""}-->
            </a>
        `).join('');
        }
    });
</script>


<!-- Popup Overlay -->
<div id="popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-500">
    <!-- Popup Content -->
    <div class=" bg-white register-popup rounded-xl shadow-lg w-full max-w-md p-6 relative">
        <!-- Close Button -->
        <button id="closePopupBtn" class="absolute top-3 right-3 text-black font-bold text-xl">&times;</button>

        <h2 class="text-2xl font-bold text-black mb-4 text-center">Register Your Business</h2>

        <!-- Form -->
        <form action="public/register.php" method="POST" class="w-full max-w-2xl bg-white  rounded-xl p-8 space-y-6 overflow-y-auto max-h-[80vh]">
            <!-- <h2 class="text-2xl font-bold text-black text-center mb-4">Register Your Business</h2> -->

            <!-- Name -->
            <div>
                <label class="block text-black font-medium mb-1" for="name">Restaurant Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your Restaurant name"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Email -->
            <div>
                <label class="block text-black font-medium mb-1" for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-black font-medium mb-1" for="ph">Phone</label>
                <input type="tel" id="ph" name="ph" placeholder="Enter your phone number"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- FSSAI -->
            <div>
                <label class="block text-black font-medium mb-1" for="fssai">FSSAI</label>
                <input type="text" id="fssai" name="fssai" placeholder="Enter your FSSAI number"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Location -->
            <div>
                <label class="block text-black font-medium mb-2">Location</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="state" placeholder="State" value="Tamil Nadu"
                        class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <input type="text" name="city" placeholder="City"
                        class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <input type="text" name="district" placeholder="District"
                        class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <input type="text" name="pincode" placeholder="Pincode"
                        class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>
            </div>

            <!-- Website URL -->
            <div>
                <label class="block text-black font-medium mb-1" for=" website">Website URL <span class="text-gray" style="color:gray">(eg: https://example.com)</span></label>
                <input type="url" id="website" name="website" value="https://" placeholder="Enter website URL"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-black font-medium mb-1" for="tags">Tags</label>
                <input type="text" id="tags" name="tags" placeholder="Enter tags separated by commas"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-black font-semibold px-6 py-2 rounded-md transition-colors">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Get elements
    const openBtn = document.getElementById('openPopupBtn');
    const closeBtn = document.getElementById('closePopupBtn');
    const popup = document.getElementById('popup');

    // Open popup
    openBtn.addEventListener('click', () => {
        popup.classList.remove('hidden');
    });

    // Close popup
    closeBtn.addEventListener('click', () => {
        popup.classList.add('hidden');
    });

    // Close popup when clicking outside the popup content
    popup.addEventListener('click', (e) => {
        if (e.target === popup) {
            popup.classList.add('hidden');
        }
    });
</script>

<!-- Footer link  -->
<?php include 'private/shared/footer.php'; ?>