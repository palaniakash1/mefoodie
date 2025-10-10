<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Filter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-4">

    <!-- Page Header -->
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-black text-center">Search Businesses</h1>
        <p class="text-gray-700 text-center mt-2">Filter by location and tags</p>
    </header>

    <!-- Filter Form -->
    <form id="filterForm" class="bg-white p-6 rounded-xl shadow-md max-w-3xl mx-auto mb-6 space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="state" placeholder="State"
                class="w-full border-2 border-red-500 rounded-md px-4 py-2 focus:outline-none focus:border-red-600">
            <input type="text" id="area" placeholder="Area"
                class="w-full border-2 border-red-500 rounded-md px-4 py-2 focus:outline-none focus:border-red-600">
            <input type="text" id="pincode" placeholder="Pincode"
                class="w-full border-2 border-red-500 rounded-md px-4 py-2 focus:outline-none focus:border-red-600">
        </div>

        <div>
            <input type="text" id="tags" placeholder="Tags (comma separated)"
                class="w-full border-2 border-red-500 rounded-md px-4 py-2 focus:outline-none focus:border-red-600">
        </div>

        <div class="text-center">
            <button type="submit"
                class="bg-red-500 hover:bg-red-600 text-black font-semibold px-6 py-2 rounded-md transition">
                Search
            </button>
        </div>
    </form>

    <!-- Search Results -->
    <div id="results" class="max-w-3xl mx-auto space-y-4">
        <!-- Results will be appended here -->
    </div>

    <!-- JavaScript -->
    <script>
        // Sample data (you can replace this with real data from backend)
        const businesses = [{
                name: "MeFoodie A",
                state: "Tamil Nadu",
                area: "Chennai",
                pincode: "600001",
                tags: ["fast food", "vegan"]
            },
            {
                name: "MeFoodie B",
                state: "Karnataka",
                area: "Bangalore",
                pincode: "560001",
                tags: ["organic", "dessert"]
            },
            {
                name: "MeFoodie C",
                state: "Tamil Nadu",
                area: "Coimbatore",
                pincode: "641001",
                tags: ["restaurant", "fast food"]
            },
        ];

        const form = document.getElementById("filterForm");
        const resultsDiv = document.getElementById("results");

        form.addEventListener("submit", function(e) {
            e.preventDefault();

            // Get filter values
            const state = document.getElementById("state").value.toLowerCase();
            const area = document.getElementById("area").value.toLowerCase();
            const pincode = document.getElementById("pincode").value.toLowerCase();
            const tags = document.getElementById("tags").value.toLowerCase().split(",").map(t => t.trim()).filter(Boolean);

            // Filter businesses
            const filtered = businesses.filter(b => {
                const matchState = state ? b.state.toLowerCase().includes(state) : true;
                const matchArea = area ? b.area.toLowerCase().includes(area) : true;
                const matchPincode = pincode ? b.pincode.includes(pincode) : true;
                const matchTags = tags.length > 0 ? tags.every(tag => b.tags.map(t => t.toLowerCase()).includes(tag)) : true;

                return matchState && matchArea && matchPincode && matchTags;
            });

            // Render results
            resultsDiv.innerHTML = filtered.length ? filtered.map(b => `
        <div class="bg-white rounded-xl shadow-md p-4">
          <h2 class="text-xl font-bold text-black">${b.name}</h2>
          <p class="text-gray-700">Location: ${b.area}, ${b.state} - ${b.pincode}</p>
          <p class="text-gray-700">Tags: ${b.tags.join(", ")}</p>
        </div>
      `).join("") : "<p class='text-center text-gray-500'>No results found.</p>";
        });
    </script>

</body>

</html>