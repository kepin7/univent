document.addEventListener("DOMContentLoaded", () => {
    const dropdowns = document.querySelectorAll(".custom-dropdown");

    dropdowns.forEach((dropdown) => {
        const selected = dropdown.querySelector(".dropdown-selected");
        const optionsContainer = dropdown.querySelector(".dropdown-options");
        const optionsList = dropdown.querySelectorAll(".dropdown-options li");
        const hiddenInput = dropdown.nextElementSibling; // <input type="hidden">

        // Toggle dropdown saat diklik
        selected.addEventListener("click", () => {
            const isOpen = dropdown.classList.contains("open");
            document
                .querySelectorAll(".custom-dropdown")
                .forEach((d) => d.classList.remove("open"));
            if (!isOpen) dropdown.classList.add("open");
        });

        // Pilih opsi
        optionsList.forEach((option) => {
            option.addEventListener("click", () => {
                const value = option.getAttribute("data-value");
                const text = option.querySelector("span").textContent;

                selected.querySelector("span").textContent = text;
                hiddenInput.value = value;

                dropdown.classList.remove("open");
            });
        });

        // Tutup dropdown jika klik di luar
        document.addEventListener("click", (e) => {
            if (!dropdown.contains(e.target)) dropdown.classList.remove("open");
        });
    });
});
