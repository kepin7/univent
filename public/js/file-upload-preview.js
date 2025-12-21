document.addEventListener("DOMContentLoaded", () => {
    const fileArea = document.getElementById("file-upload-area");
    const fileInput = document.getElementById("event_poster");
    const previewContainer = document.getElementById("preview-container");
    const previewImage = document.getElementById("preview-image");
    const removeBtn = document.getElementById("remove-preview");
    const uploadText = fileArea.querySelector(".upload-text");
    const form = document.querySelector("form"); // Menggunakan querySelector untuk form terdekat

    if (!fileArea || !fileInput) return;

    // --- 1. Event Click (Pemicu Explorer) ---
    fileArea.addEventListener("click", (e) => {
        // Pengecualian: Jika tombol remove yang diklik, hentikan proses di sini
        if (e.target.id === "remove-preview") {
            e.stopImmediatePropagation();
            return;
        }

        fileInput.click();
        
        // PENTING: Hentikan event agar tidak mengalir ke listener di document/dropdown.js
        e.stopImmediatePropagation(); 
    });

    // --- 2. Event Change (Menampilkan Preview) ---
    fileInput.addEventListener("change", (e) => {
        
        // 🔥 PENTING: Hentikan event 'change' agar tidak memicu explorer kedua 
        // setelah file dipilih (mengatasi konflik event bawaan/library)
        e.stopImmediatePropagation(); 

        if (fileInput.files && fileInput.files[0]) {
            showPreview(fileInput.files[0]);
        }
    });

    // --- 3. Event Drag & Drop ---
    fileArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        fileArea.classList.add("dragover");
    });

    fileArea.addEventListener("dragleave", () => {
        fileArea.classList.remove("dragover");
    });

    fileArea.addEventListener("drop", (e) => {
        e.preventDefault();
        fileArea.classList.remove("dragover");
        
        // Pastikan input file diproses
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files; 
            showPreview(e.dataTransfer.files[0]);
        }
    });

    // --- 4. Tombol Remove ---
    if (removeBtn) {
        removeBtn.addEventListener("click", (e) => {
            e.stopPropagation(); // Mencegah bubbling ke fileArea click
            fileInput.value = "";
            previewContainer.style.display = "none";
            uploadText.textContent = "Drag & drop your file here";
        });
    }

    // --- 5. Fungsi showPreview ---
    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = "block";
            uploadText.textContent = file.name;
        };
        reader.readAsDataURL(file);
    }
    
    // --- 6. Event Reset Form (Tambahan) ---
    if (form) {
        form.addEventListener("reset", () => {
            // Memberi sedikit waktu agar proses reset form selesai, 
            // lalu reset tampilan
            setTimeout(() => {
                fileInput.value = "";
                previewContainer.style.display = "none";
                uploadText.textContent = "Drag & drop your file here";
            }, 50); 
        });
    }
});