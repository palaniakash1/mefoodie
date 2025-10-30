<?php
// ============================
// 🔹 Redirect index.php → /
// ============================
$request_uri = $_SERVER['REQUEST_URI'];
$query_string = $_SERVER['QUERY_STRING'];
if (basename($request_uri) === "index.php") {
    $redirect_url = '/';
    if (!empty($query_string)) {
        $redirect_url .= '?' . $query_string;
    }
    header("Location: $redirect_url", true, 301);
    exit;
}

require_once("private/initialize.php");
$page_title = "MeFoodie - Home Page";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "$page_title" ?> | One platform All Business</title>
    <link rel="icon" href="public/favicon.ico.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="public/stylesheets/style.css">
</head>

<body class="bg-gray-50 text-gray-900">
    <?php include 'private/shared/header.php'; ?>

    <main class="min-h-screen flex flex-col items-center p-4 mt-5">
        <h1 class="text-black mb-2 text-2xl font-bold text-center">
            Welcome to <span class="text-tomato">MeFoodie</span>
        </h1>

        <div class="max-w-6xl w-full">
            <h1 class="text-center text-2xl mb-8 text-black home-heading-h1 sm:leading-snug">
                The Smart Way to Grow <span class="text-tomato">Your Business</span> Presence.
            </h1>

            <!-- Restaurant Grid -->
            <div id="restaurant-list" class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mt-5 md:px-3 sm-w-full">
                <p class="text-gray-500 text-center col-span-full">Loading restaurants...</p>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="flex justify-center items-center space-x-2 mt-8"></div>
        </div>
    </main>

    <?php include 'private/shared/footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const lat = pos.coords.latitude;
                    const lon = pos.coords.longitude;

                    window.userLocation = {
                        lat: pos.coords.latitude,
                        lon: pos.coords.longitude
                    };
                    console.log("📍 User Location:", lat, lon);
                    loadRestaurants(lat, lon, 1);
                }, err => {
                    console.warn("⚠️ Geolocation failed:", err.message);
                    // fallback - Madurai
                    loadRestaurants(9.9195, 78.1193, 1);
                });
            } else {
                console.warn("⚠️ Geolocation not supported. Using fallback.");
                loadRestaurants(9.9195, 78.1193, 1);
            }
        });

        // -------------------------
        // 🧭 Fetch & Render Logic
        // -------------------------
        function loadRestaurants(lat, lon, page) {
            const endpoint = `${window.location.origin}/public/get_nearby_restaurants.php?lat=${lat}&lon=${lon}&page=${page}`;
            console.log("🔗 Fetching:", endpoint);

            fetch(endpoint)
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        document.getElementById("restaurant-list").innerHTML =
                            `<p class="text-center text-gray-500">${data.error}</p>`;
                        return;
                    }

                    renderRestaurants(data.restaurants);
                    renderPagination(data.total_pages, data.current_page, lat, lon);
                })
                .catch(err => {
                    console.error("❌ Fetch failed:", err);
                    document.getElementById("restaurant-list").innerHTML =
                        `<p class="text-center text-gray-500">Error loading data.</p>`;
                });
        }

        function renderRestaurants(restaurants) {
            const container = document.getElementById("restaurant-list");
            container.innerHTML = "";

            if (!restaurants || restaurants.length === 0) {
                container.innerHTML = `<p class="text-center text-gray-500 col-span-full">No restaurants found nearby.</p>`;
                return;
            }

            restaurants.forEach(r => {
                container.innerHTML += `
                    <a href="${r.website}" target="_blank"
                       class="url-card block bg-white rounded-2xl shadow-md hover:shadow-lg transition-all p-6 text-center sm-w-full">
                        <h3 class="text-lg font-semibold text-tomato mb-1">${r.name}</h3>
                        <p class="text-gray-600 text-sm">${r.city}</p>
                        <p class="text-blue-500 text-sm mt-2 truncate hover:underline">${r.website}</p>
                    </a>`;
            });
        }

        function renderPagination(totalPages, currentPage, lat, lon) {
            const container = document.getElementById("pagination");
            container.innerHTML = "";

            if (totalPages <= 1) return;

            // Prev
            container.innerHTML += currentPage > 1 ?
                `<button class="px-3 py-1 border rounded hover:bg-gray-100" onclick="loadRestaurants(${lat},${lon},${currentPage-1})">Prev</button>` :
                `<span class="px-3 py-1 text-gray-400">Prev</span>`;

            // Numbers
            for (let i = 1; i <= totalPages; i++) {
                container.innerHTML += i === currentPage ?
                    `<span class="px-3 py-1 border rounded tomato-bg text-white">${i}</span>` :
                    `<button class="px-3 py-1 border rounded hover:bg-gray-100" onclick="loadRestaurants(${lat},${lon},${i})">${i}</button>`;
            }

            // Next
            container.innerHTML += currentPage < totalPages ?
                `<button class="px-3 py-1 border rounded hover:bg-gray-100" onclick="loadRestaurants(${lat},${lon},${currentPage+1})">Next</button>` :
                `<span class="px-3 py-1 text-gray-400">Next</span>`;
        }
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
                    <input type="text" id="name" required name="name" placeholder="Enter your Restaurant name"
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <small id="error-name" class="text-red-500 text-sm hidden">Restaurant name is required.</small>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-black font-medium mb-1" for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email"
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <small id="error-email" class="text-red-500 text-sm hidden">email is required.</small>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-black font-medium mb-1" for="ph">Phone</label>
                    <input type="tel" id="ph" name="ph" pattern="\d{10}"
                        maxlength="10"
                        minlength="10" required placeholder="Enter your phone number"
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>

                <!-- FSSAI -->
                <div>
                    <label class="block text-black font-medium mb-1" for="fssai">FSSAI</label>
                    <input type="text" id="fssai" name="fssai" placeholder="Enter your FSSAI number" pattern="\d{14}"
                        maxlength="14"
                        minlength="14"
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-black font-medium mb-2">Location</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="state" placeholder="State" value="Tamil Nadu"
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                        <input type="text" name="city" placeholder="City" required
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                        <input type="text" name="district" placeholder="District" required
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                        <input type="text" name="pincode" placeholder="Pincode"
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    </div>
                </div>

                <!-- Website URL -->
                <div>
                    <label class="block text-black font-medium mb-1" for=" website">Website URL <span class="text-gray" style="color:gray">(eg: www.example.com)</span></label>
                    <input type="text" id="website" name="website" placeholder="Enter website URL" required
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-black font-medium mb-1" for="tags">Tags</label>
                    <input type="text" id="tags" name="tags" placeholder="helpful to user to search by tag" required
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-2 rounded-md transition-colors">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- Success Popup -->
    <div id="successPopup" class="popup">
        <div class="popup-content">
            <span class="popup-close" id="closePopupBtn" onclick="closePopup()">&times;</span>
            <div class="popup-icon">✅</div>
            <h2>Success!</h2>
            <p>Your business registration was completed successfully. <br>We will review your restaurant and register it within 24 hours.</p>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form'); // select your form
            const popup = document.getElementById('successPopup');

            form.addEventListener('submit', async (e) => {
                e.preventDefault(); // stop normal submission to check validity

                // ✅ Step 1: Check if all required fields are valid
                if (!form.checkValidity()) {
                    form.reportValidity(); // show browser validation messages
                    return; // stop here — no popup
                }

                // ✅ Step 2: Submit form (AJAX or actual backend)
                try {
                    // Example AJAX submission (you can adjust this part)
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        // ✅ Step 3: Show success popup only if backend returns success
                        showPopup();
                        form.reset(); // optional: clear form after success
                    } else {
                        alert('Submission failed. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });

        function showPopup() {
            const popup = document.getElementById('successPopup');
            popup.style.display = 'flex';
            setTimeout(closePopup, 3000);
        }

        function closePopup() {
            document.getElementById('successPopup').style.display = 'none';
        }
    </script>
</body>

</html>