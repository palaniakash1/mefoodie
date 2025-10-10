<?php
include 'config.php'; // or require_once if needed
?>

<header class="bg-white shadow-md flex items-center justify-between">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
        <!-- <img src="assets/images/logo.png" alt="Logo" class="h-10 w-10 object-contain"> -->
        <a href="<?php echo "$base_url" ?>">
            <h1 class="text-3xl font-semibold text-gray-800">MeFoodie</h1>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="flex items-center w-1/2 max-w-md bg-gray-100 rounded-full px-3 py-1">
        <input
            type="text"
            placeholder="Search..."
            class="bg-gray-100 flex-grow text-sm text-gray-700 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-gray-500 cursor-pointer"
            viewBox="0 0 20 20"
            fill="currentColor">
            <path fill-rule="evenodd"
                d="M13.293 14.707a8 8 0 111.414-1.414l3.387 3.387a1 1 0 01-1.414 1.414l-3.387-3.387zM8 14a6 6 0 100-12 6 6 0 000 12z"
                clip-rule="evenodd" />
        </svg>
    </div>

    <!-- resgister -->
    <button id="openPopupBtn" class="text-sm font-medium text-red-600 hover:text-red-800 ">
        Register your business
    </button>
    <!-- <a href="<?php echo "$base_url"?>/search_result.php" id="" class="text-sm font-medium text-red-600 hover:text-red-800 ">
        search button
    </a> -->
    <a href="<?php echo "$base_url"?>admin.php" id="" class="text-sm font-medium text-red-600 hover:text-red-800 ">
        admin
    </a>

</header>

<!-- Popup Overlay -->
<div id="popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-500">
    <!-- Popup Content -->
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative">
        <!-- Close Button -->
        <button id="closePopupBtn" class="absolute top-3 right-3 text-black font-bold text-xl">&times;</button>

        <h2 class="text-2xl font-bold text-black mb-4 text-center">Register Your Business</h2>

        <!-- Form -->
        <form class="w-full max-w-2xl bg-white shadow-lg rounded-xl p-8 space-y-6 overflow-y-auto max-h-[80vh]">
            <!-- <h2 class="text-2xl font-bold text-black text-center mb-4">Register Your Business</h2> -->

            <!-- Name -->
            <div>
                <label class="block text-black font-medium mb-1" for="name">Name</label>
                <input type="text" id="name" placeholder="Enter your name"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Email -->
            <div>
                <label class="block text-black font-medium mb-1" for="email">Email</label>
                <input type="email" id="email" placeholder="Enter your email"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-black font-medium mb-1" for="ph">Phone</label>
                <input type="tel" id="ph" placeholder="Enter your phone number"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- FSSAI -->
            <div>
                <label class="block text-black font-medium mb-1" for="fssai">FSSAI</label>
                <input type="text" id="fssai" placeholder="Enter your FSSAI number"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Location -->
            <div>
                <label class="block text-black font-medium mb-2">Location</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" placeholder="State"
                        class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <input type="text" placeholder="Area"
                        class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                    <input type="text" placeholder="Pincode"
                        class="border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
                </div>
            </div>

            <!-- Website URL -->
            <div>
                <label class="block text-black font-medium mb-1" for="website">Website URL</label>
                <input type="url" id="website" placeholder="Enter website URL"
                    class="w-full border-2 border-tomato-500 focus:border-tomato-600 rounded-md px-4 py-2 text-black focus:outline-none" />
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-black font-medium mb-1" for="tags">Tags</label>
                <input type="text" id="tags" placeholder="Enter tags separated by commas"
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