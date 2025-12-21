document.addEventListener("DOMContentLoaded", function () {
    const categoryDropdown = document.querySelectorAll(".custom-dropdown")[0];
    const organizerDropdown = document.querySelectorAll(".custom-dropdown")[1];
    const clearBtn = document.querySelector(".clear-btn");
    const searchInput = document.querySelector(".search-input input");
    const cards = document.querySelectorAll(".card");

    let selectedCategory = "all";
    let selectedOrganizer = "all";
    let searchQuery = "";

    // ================================
    // BACA QUERY PARAM DARI URL
    // ================================
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get("category");
    const organizerParam = urlParams.get("organizer");

    // Jika ada param kategori → set dropdown otomatis
    if (categoryParam) {
        selectedCategory = categoryParam.toLowerCase();

        const categoryOptions = categoryDropdown.querySelectorAll(".dropdown-options li");
        const selectedText = categoryDropdown.querySelector(".dropdown-selected span");

        categoryOptions.forEach((opt) => {
            opt.classList.remove("active");

            if (opt.dataset.value.toLowerCase() === selectedCategory) {
                opt.classList.add("active");
                selectedText.textContent = opt.querySelector("span").textContent;
            }
        });
    }

    // Jika ada param organizer → set dropdown otomatis
    if (organizerParam) {
        selectedOrganizer = organizerParam.toLowerCase();

        const organizerOptions = organizerDropdown.querySelectorAll(".dropdown-options li");
        const selectedText = organizerDropdown.querySelector(".dropdown-selected span");

        organizerOptions.forEach((opt) => {
            opt.classList.remove("active");

            if (opt.dataset.value.toLowerCase() === selectedOrganizer) {
                opt.classList.add("active");
                selectedText.textContent = opt.querySelector("span").textContent;
            }
        });
    }

    // ================================
    // FUNGSI FILTER
    // ================================
    function filterEvents() {
        const query = searchQuery.toLowerCase();

        cards.forEach((card) => {
            const category = (card.dataset.category || "").toLowerCase();
            const organizer = (card.dataset.organizer || "").toLowerCase();
            const title = card.querySelector(".card-title").textContent.toLowerCase();
            const desc = card.querySelector(".card-desc").textContent.toLowerCase();

            const matchCategory = selectedCategory === "all" || category === selectedCategory;
            const matchOrganizer = selectedOrganizer === "all" || organizer === selectedOrganizer;
            const matchSearch = title.includes(query) || desc.includes(query);

            card.style.display = matchCategory && matchOrganizer && matchSearch ? "flex" : "none";
        });
    }

    // ================================
    // DROPDOWN HANDLER
    // ================================
    function setupDropdown(dropdown, callback) {
        const options = dropdown.querySelectorAll(".dropdown-options li");
        const selectedText = dropdown.querySelector(".dropdown-selected span");

        options.forEach((option) => {
            option.addEventListener("click", () => {
                options.forEach((opt) => opt.classList.remove("active"));
                option.classList.add("active");

                const value = option.dataset.value.toLowerCase();
                const label = option.querySelector("span").textContent;
                selectedText.textContent = label;

                callback(value);
                filterEvents();
            });
        });
    }

    setupDropdown(categoryDropdown, (value) => (selectedCategory = value));
    setupDropdown(organizerDropdown, (value) => (selectedOrganizer = value));

    // ================================
    // SEARCH INPUT
    // ================================
    searchInput.addEventListener("input", (e) => {
        searchQuery = e.target.value;
        filterEvents();
    });

    // ================================
    // CLEAR BUTTON
    // ================================
    clearBtn.addEventListener("click", () => {
        selectedCategory = "all";
        selectedOrganizer = "all";
        searchQuery = "";
        searchInput.value = "";

        document.querySelectorAll(".custom-dropdown").forEach((dropdown) => {
            const selectedText = dropdown.querySelector(".dropdown-selected span");
            const firstOption = dropdown.querySelector("li[data-value='all']");

            dropdown.querySelectorAll("li").forEach((opt) => opt.classList.remove("active"));
            firstOption.classList.add("active");
            selectedText.textContent = firstOption.querySelector("span").textContent;
        });

        filterEvents();
    });

    // Jalankan pertama kali
    filterEvents();
});
