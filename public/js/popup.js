
        const modal = document.getElementById("popupModal");
        const modalImg = document.getElementById("popupImage");
        const captionText = document.getElementById("caption");

        // Ambil semua gambar dengan class clickable-poster
        document.querySelectorAll(".clickable-poster").forEach(img => {
            img.onclick = () => {
                modal.style.display = "block";
                modalImg.src = img.src;
                captionText.innerHTML = img.alt;
            }
        });

        // Tombol close
        document.querySelector(".close").onclick = () => {
            modal.style.display = "none";
        }
