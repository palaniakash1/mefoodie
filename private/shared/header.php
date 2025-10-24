<?php include 'public\config.php' ?>
<link rel="stylesheet" href="../../public/stylesheets/style.css">
<header class="bg-white shadow-md flex flex-col md:flex-row items-center justify-between px-6 py-3 sticky top-0 z-50 home-header">
    <!-- Logo -->
    <div class="flex items-start space-x-2 mb-2 md:mb-0 brand-name">
        <a href="index.php">
            <h1 class="text-2xl font-bold text-red-600 tracking-tight">MeFoodie</h1>
        </a>
    </div>
    <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto search-register-container">

        <!-- Search Bar -->
        <div class="relative w-full md:w-1/2">
            <div class="flex items-center bg-gray-100 rounded-full px-3 py-2 focus-within:ring-2 focus-within:ring-red-400 transition search-container">
                <input
                    type="text"
                    name="q"
                    id="search-input"
                    placeholder="Search location or tags..."
                    class="bg-gray-100 flex-grow text-sm text-gray-700 focus:outline-none"
                    required>
                <button type="submit">
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

        <!-- Register Button -->
        <button id="openPopupBtn" class="mt-2 md:mt-0 text-sm font-medium text-red-600 hover:text-red-800 transition reg-btn">
            Register your business
        </button>

    </div>

</header>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('search-input');
        const results = document.getElementById('search-results');
        const restaurantList = document.getElementById('restaurant-list');
        let timer;

        // 🔍 Typing triggers dropdown suggestions
        input.addEventListener('input', function() {
            const query = input.value.trim();
            clearTimeout(timer);

            timer = setTimeout(() => {
                if (query.length === 0) {
                    results.innerHTML = '';
                    results.classList.add('hidden');
                    return;
                }

                fetch(`<?php echo $base_url; ?>/private/search_restaurants.php?q=${encodeURIComponent(query)}&mode=suggest`)
                    .then(res => res.text())
                    .then(data => {
                        results.innerHTML = data;
                        results.classList.remove('hidden');
                    })
                    .catch(err => {
                        results.innerHTML = `<p class='text-center text-gray-500 py-2'>Error: ${err}</p>`;
                        results.classList.remove('hidden');
                    });
            }, 300);
        });

        // ⌨️ Handle Enter key press for full search
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerFullSearch(input.value.trim());
            }
        });

        // 🖱️ Handle click on suggestion item
        results.addEventListener('click', function(e) {
            const item = e.target.closest('.result-item');
            if (!item) return;

            const query = item.getAttribute('data-query');
            input.value = query;
            results.classList.add('hidden');
            triggerFullSearch(query);
        });

        // 🧩 Function: fetch and render full results in grid
        function triggerFullSearch(query) {
            if (!query) return;

            fetch(`<?php echo $base_url; ?>/private/search_restaurants.php?q=${encodeURIComponent(query)}&mode=full`)
                .then(res => res.json())
                .then(data => renderRestaurants(data))
                .catch(err => {
                    restaurantList.innerHTML = `<p class='text-gray-500 text-center'>Error fetching data: ${err}</p>`;
                });
        }

        // 🧩 Reuse same renderer as index.php
        function renderRestaurants(data) {
            if (!data || data.length === 0) {
                restaurantList.innerHTML = `<p class='text-gray-500 text-center'>No matching restaurants found.</p>`;
                return;
            }

            restaurantList.innerHTML = data.map(r => `
            <div class="bg-white rounded-xl shadow-lg p-4 hover:shadow-xl transition">
                <h3 class="font-bold text-lg text-tomato mb-2">${r.name}</h3>
                <p><strong></strong> ${r.state}</p>
                <p><a href="${r.website}" target="_blank" class="text-blue-500 hover:underline">${r.website}</a></p>
            </div>
        `).join('');
        }

        // 🧭 Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!results.contains(e.target) && !input.contains(e.target)) {
                results.classList.add('hidden');
            }
        });
    });
</script>