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
<?php include __DIR__ . '/public/config.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "$page_title" ?> | One platform All Business</title>
    <link rel="icon" href="public/favicon.ico.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/stylesheets/style.css">
</head>

<body class="bg-gray-50 text-gray-900">
    <?php include 'private/shared/header.php'; ?>

    <main class="min-h-screen flex flex-col items-center p-4 mt-5">
        <h1 class="text-black text-[16px] md:text-[20px] sm:text-[18px] font-bold text-center mb-2 ">
            Welcome to <span class="text-tomato">MeFoodie</span>
        </h1>

        <div class="max-w-6xl w-full">
            <h1 class="text-center text-[24px] sm:text-[32px] md:text-[40px] 
           leading-[1.2] sm:leading-[1.4] md:leading-[1.6] 
           text-black mb-8 home-heading-h1">
                From Kitchen To Clicks <span class="text-tomato">Grow </span> Your Business Online!.
            </h1>



            <!-- Search Bar -->
            <div class="search-container-parent">
                <div class="flex items-center bg-gray-100 rounded-full px-3 py-1 focus-within:ring-2 focus-within:ring-red-400 transition search-container">
                    <input
                        type="text"
                        name="q"
                        id="search-input"
                        placeholder="Search location or tags..."
                        class="bg-gray-100 flex-grow text-sm text-gray-700 focus:outline-none"
                        autocomplete="off"
                        required>
                    <button type="button" id="search-btn">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-gray-500 cursor-pointer"
                            viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M13.293 14.707a8 8 0 111.414-1.414l3.387 3.387a1 1 0 01-1.414 1.414l-3.387-3.387zM8 14a6 6 0 100-12 6 6 0 000 12z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div id="search-results" class="absolute bg-white w-full mt-2 rounded-xl shadow-lg hidden max-h-96 overflow-y-auto z-50"></div>
            </div>

            <!-- business Grid -->

            <div id="business-list" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 max-w-7xl mx-auto">

                <p class="text-gray-500 text-center col-span-full">Loading businesses...</p>
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
                    loadbusinesses(lat, lon, 1);
                }, err => {
                    console.warn("⚠️ Geolocation failed:", err.message);
                    // fallback - Madurai
                    loadbusinesses(9.9195, 78.1193, 1);
                });
            } else {
                console.warn("⚠️ Geolocation not supported. Using fallback.");
                loadbusinesses(9.9195, 78.1193, 1);
            }
        });

        // -------------------------
        // 🧭 Fetch & Render Logic
        // -------------------------
        function loadbusinesses(lat, lon, page) {
            const endpoint = `${window.location.origin}/public/get_nearby_businesses.php?lat=${lat}&lon=${lon}&page=${page}`;
            console.log("🔗 Fetching:", endpoint);

            fetch(endpoint)
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        document.getElementById("business-list").innerHTML =
                            `<p class="text-center text-gray-500">${data.error}</p>`;
                        return;
                    }

                    renderbusinesses(data.businesses);
                    renderPagination(data.total_pages, data.current_page, lat, lon);
                })
                .catch(err => {
                    console.error("❌ Fetch failed:", err);
                    document.getElementById("business-list").innerHTML =
                        `<p class="text-center text-gray-500">Error loading data.</p>`;
                });
        }

        function renderbusinesses(businesses) {
            const container = document.getElementById("business-list");
            container.innerHTML = "";

            if (!businesses || businesses.length === 0) {
                container.innerHTML = `<p class="text-center text-gray-500 col-span-full">No businesses found nearby.</p>`;
                return;
            }

            businesses.forEach(r => {
                container.innerHTML += `
                    <a href="${r.website}" target="_blank"
                       class="url-card block bg-white rounded-2xl shadow-md hover:shadow-lg transition-all p-6 text-center sm:w-full">
                        <h3 class="text-lg font-semibold text-tomato mb-1 truncate">${r.name}</h3>
                        <p class="text-gray-600 text-sm truncate">${r.city}</p>
                        <p class="text-blue-500 text-sm mt-2 truncate hover:underline truncate">${r.website}</p>
                    </a>`;
            });
        }

        function renderPagination(totalPages, currentPage, lat, lon) {
            const container = document.getElementById("pagination");
            container.innerHTML = "";

            if (totalPages <= 1) return;

            // Prev
            container.innerHTML += currentPage > 1 ?
                `<button class="px-3 py-1 border rounded hover:bg-gray-100" onclick="loadbusinesses(${lat},${lon},${currentPage-1})">Prev</button>` :
                `<span class="px-3 py-1 text-gray-400">Prev</span>`;

            // Numbers
            for (let i = 1; i <= totalPages; i++) {
                container.innerHTML += i === currentPage ?
                    `<span class="px-3 py-1 border rounded tomato-bg text-white">${i}</span>` :
                    `<button class="px-3 py-1 border rounded hover:bg-gray-100" onclick="loadbusinesses(${lat},${lon},${i})">${i}</button>`;
            }

            // Next
            container.innerHTML += currentPage < totalPages ?
                `<button class="px-3 py-1 border rounded hover:bg-gray-100" onclick="loadbusinesses(${lat},${lon},${currentPage+1})">Next</button>` :
                `<span class="px-3 py-1 text-gray-400">Next</span>`;
        }
    </script>

    <!-- Popup Overlay -->
    <div id="popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[999]">
        <!-- Popup Content -->
        <div class=" bg-white register-popup rounded-xl shadow-lg w-full max-w-md p-6 relative mt-8 sm:mt-8 md:mt-5">
            <!-- Close Button -->
            <button id="closePopupBtn" class="absolute top-3 right-3 text-black font-bold text-xl">&times;</button>

            <h2 class="text-2xl font-bold text-black mb-4 text-center">Register Your Business</h2>

            <!-- Form -->
            <form action="public/register.php" method="POST" class="w-full max-w-2xl bg-white  rounded-xl p-8 space-y-6 overflow-y-auto max-h-[80vh] mt-5">
                <!-- <h2 class="text-2xl font-bold text-black text-center mb-4">Register Your Business</h2> -->

                <!-- Name -->
                <div>
                    <label class="block text-black font-medium mb-1" for="name">Business Name <span class="text-tomato">*</span></label>
                    <input type="text" id="name" required name="name" placeholder="Enter your business name" pattern="^[A-Za-z][A-Za-z\s]{2,49}$"
                        title="Business name should contain only letters and spaces (minimum 3 characters, no numbers or symbols)."
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <small id="error-name" class="text-red-500 text-sm hidden">business name is required.</small>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-black font-medium mb-1" for="email">Email <span class="text-tomato">*</span></label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" title="Enter a valid email address (e.g., name@example.com)"
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <small id="error-email" class="text-red-500 text-sm hidden">email is required.</small>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-black font-medium mb-1" for="ph">Phone <span class="text-tomato">*</span></label>
                    <input type="tel" id="ph" name="ph" pattern="^(?!.*(\d)\1{9})(?!0123456789)(?!1234567890)(?!9876543210)[6-9]\d{9}$"
                        title="Enter a valid 10-digit Indian mobile number (not sequential or repeating)"
                        maxlength="10"
                        minlength="10" required placeholder="Enter your phone number"
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>

                <!-- FSSAI -->
                <div>
                    <label class="block text-black font-medium mb-1" for="fssai">FSSAI</label>
                    <input type="text" id="fssai" name="fssai" placeholder="Enter your FSSAI number(optional)" pattern="^\d{14}$" title="FSSAI number must contain exactly 14 digits"
                        maxlength="14"
                        minlength="14"
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-black font-medium mb-2">Location</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- State Dropdown -->
                        <select name="state"
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none">
                            <option value="">Select State</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu" selected>Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                            <option value="Chandigarh">Chandigarh</option>
                            <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                            <option value="Ladakh">Ladakh</option>
                            <option value="Lakshadweep">Lakshadweep</option>
                            <option value="Puducherry">Puducherry</option>
                        </select>
                        <input type="text" name="district" placeholder="District*" required pattern="^[A-Za-z\s]{2,50}$"
                            title="Enter a valid city name (letters only)"
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                        <input type="text" name="city" placeholder="Area/Town*" required pattern="^[A-Za-z\s]{2,50}$"
                            title="Enter a valid city name (letters only)"
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                        <input type="text" name="pincode" placeholder="Pincode" pattern="\d{6}"
                            maxlength="6"
                            minlength="6"
                            class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    </div>
                </div>

                <!-- Website URL -->
                <div>
                    <label class="block text-black font-medium mb-1" for=" website">Website URL <span class="text-tomato">*</span> <span class="text-gray" style="color:gray">(eg: www.example.com)</span></label>
                    <input type="text" id="website" name="website" placeholder="Enter website URL" required
                        class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-black font-medium mb-1" for="tags">Tags <span class="text-tomato">*</span> <span class="text-gray" style="color:gray"> Use comma [,] to separate</span></label>
                    <input type="text" id="tags" name="tags" placeholder="Helpful for user to search by tag" required pattern="^([a-zA-Z]+(?:\s[a-zA-Z]+)*)(,\s*[a-zA-Z]+(?:\s[a-zA-Z]+)*){0,2}$" "
                        title=" Enter up to 3 tags separated by commas (letters only). Example: software, website, app"
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
    <div id="successPopup" class=" fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[9999]">
        <div class="popup-content bg-white rounded-xl shadow-lg p-6 max-w-md w-11/12 text-center relative">
            <div class="popup-icon text-4xl mb-4">✅</div>
            <h2 class="text-xl font-semibold mb-2">Success!</h2>
            <p class="text-gray-600 mb-6">
                Your business registration was completed successfully. <br>
                We will review your business and register it within 24 hours.
            </p>
            <button id="okBtn" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition">
                OK
            </button>
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
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector("form");
            const website = document.getElementById("website");
            const successPopup = document.getElementById("successPopup");
            const okBtn = document.getElementById("okBtn");

            // ✅ Updated Regex (accepts google.com, www.google.com, https://google.com, etc.)
            const websiteRegex = /^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/i;

            form.addEventListener("submit", async (e) => {
                e.preventDefault();


                // Elements
                const submitBtn = form.querySelector("button[type='submit']");
                const originalBtnText = submitBtn.innerHTML;

                // 🛑 Disable the button immediately
                submitBtn.disabled = true;
                submitBtn.innerHTML = "Submitting... ⏳";
                submitBtn.classList.add("opacity-70", "cursor-not-allowed");



                // 🧹 Always clear validity first
                website.setCustomValidity("");

                let urlValue = website.value.trim();

                // ✅ Auto-add https:// if missing
                if (urlValue && !/^https?:\/\//.test(urlValue)) {
                    urlValue = "https://" + urlValue;
                }

                // ✅ Update the actual field before validation
                website.value = urlValue;

                console.log("🔍 Validating:", urlValue);

                // ✅ Validate using regex
                if (!websiteRegex.test(urlValue)) {
                    website.setCustomValidity(
                        "Please enter a valid website URL like example.com or https://example.in"
                    );
                    website.reportValidity();
                    return;
                }

                // ✅ Recheck browser validations
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // ✅ If all fine → submit via AJAX
                try {
                    const formData = new FormData(form);

                    // Optional: show a spinner overlay or text while waiting
                    console.log("⏳ Submitting form...");

                    const response = await fetch(form.action, {
                        method: "POST",
                        body: formData
                    });

                    if (response.ok) {
                        showPopup();
                        form.reset();
                    } else {
                        alert("❌ Submission failed. Please try again.");
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("⚠️ Something went wrong. Please try again.");
                } finally {
                    resetButton();
                }

                // 🧩 Helper function to reset button
                function resetButton() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.classList.remove("opacity-70", "cursor-not-allowed");
                }

            });

            // Show popup
            function showPopup() {
                successPopup.style.display = "flex";
            }

            // Hide popup when OK is clicked
            okBtn.addEventListener("click", () => {
                successPopup.style.display = "none";
            });

            // Close popup when clicking outside popup-content
            successPopup.addEventListener("click", (e) => {
                if (e.target === successPopup) {
                    successPopup.style.display = "none";
                }
            });

            // 🩹 Optional: Clear error immediately when typing again
            website.addEventListener("input", () => {
                website.setCustomValidity("");
            });
        });
    </script>


</body>

</html>