<?php include '../private/shared/header.php'; ?>
<div class="max-w-lg mx-auto relative mt-10">
    <input
        type="text"
        id="searchBox"
        placeholder="Search businesses, city, or tags..."
        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-300"
        autocomplete="off" />
    <div id="suggestionsBox"
        class="absolute left-0 right-0 bg-white border rounded-lg shadow-lg mt-1 hidden z-50 max-h-64 overflow-y-auto">
    </div>
</div>

<script>
    const searchBox = document.getElementById('searchBox');
    const suggestionsBox = document.getElementById('suggestionsBox');
    let debounceTimer;

    // Detect if URL already has ?q=
    const urlParams = new URLSearchParams(window.location.search);
    const existingQuery = urlParams.get('q');
    if (existingQuery) {
        searchBox.value = existingQuery;
        // Optionally trigger a full search here later
    }

    // Handle typing
    searchBox.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const query = searchBox.value.trim();
        if (!query) {
            suggestionsBox.classList.add('hidden');
            suggestionsBox.innerHTML = '';
            return;
        }
        debounceTimer = setTimeout(() => fetchSuggestions(query), 200);
    });

    // Fetch suggestions (AJAX)
    function fetchSuggestions(query) {
        fetch(`search_businesses.php?q=${encodeURIComponent(query)}&mode=suggest`)
            .then(res => res.text())
            .then(html => {
                suggestionsBox.innerHTML = html;
                suggestionsBox.classList.remove('hidden');
            })
            .catch(() => {
                suggestionsBox.classList.add('hidden');
            });
    }

    // Handle clicking a suggestion
    suggestionsBox.addEventListener('click', (e) => {
        const item = e.target.closest('.result-item');
        if (!item) return;

        const selected = item.getAttribute('data-query');
        if (!selected) return;

        // Redirect to new URL
        window.location.href = `search.php?q=${encodeURIComponent(selected)}`;
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#searchBox') && !e.target.closest('#suggestionsBox')) {
            suggestionsBox.classList.add('hidden');
        }
    });
</script>

<?php include '../private/shared/footer.php'; ?>