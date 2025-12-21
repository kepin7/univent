document.addEventListener('DOMContentLoaded', () => {
    // ===============================================
    // 1. Fungsionalitas Dropdown Kustom
    // ===============================================
    const dropdowns = document.querySelectorAll('.custom-dropdown');

    dropdowns.forEach(dropdown => {
        const selected = dropdown.querySelector('.dropdown-selected');
        const optionsList = dropdown.querySelector('.dropdown-options');
        const options = optionsList.querySelectorAll('li');
        const selectedText = selected.querySelector('span');

        // Toggle dropdown saat elemen terpilih diklik
        selected.addEventListener('click', () => {
            dropdown.classList.toggle('open');
        });

        // Menangani pemilihan opsi
        options.forEach(option => {
            option.addEventListener('click', () => {
                // Hapus kelas 'active' dari semua opsi
                options.forEach(o => o.classList.remove('active'));
                
                // Set kelas 'active' pada opsi yang baru dipilih
                option.classList.add('active');
                
                // Update teks yang ditampilkan
                selectedText.textContent = option.querySelector('span').textContent.trim();
                
                // Tutup dropdown
                dropdown.classList.remove('open');
            });
        });
    });

    // Menutup dropdown saat mengklik di luar
    window.addEventListener('click', e => {
        dropdowns.forEach(dropdown => {
            // Periksa jika klik tidak terjadi di dalam elemen dropdown
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    });

    // ===============================================
    // 2. Fungsionalitas Navigasi Tab Admin
    // ===============================================
    
    // Tampilkan tab 'pending' saat halaman dimuat (default startup)
    // Hapus semua status aktif awal (jika ada)
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.admin-nav-tabs button').forEach(btn => btn.classList.remove('active'));

    // Set 'pending' sebagai default
    const defaultTab = document.getElementById('pending');
    const defaultButton = document.getElementById('tab-pending');

    if (defaultTab && defaultButton) {
        defaultTab.classList.add('active');
        defaultButton.classList.add('active');
    }
});


/**
 * Fungsi global untuk mengelola tampilan tab.
 * Dibuat global agar bisa dipanggil dari atribut onclick di HTML.
 * * @param {HTMLElement} buttonElement - Tombol navigasi yang diklik.
 * @param {string} tabName - ID dari konten tab yang akan ditampilkan.
 */
window.showTab = function(buttonElement, tabName) {
    // Hapus kelas 'active' dari semua konten
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Hapus kelas 'active' dari semua tombol navigasi
    document.querySelectorAll('.admin-nav-tabs button').forEach(btn => {
        btn.classList.remove('active');
    });

    // Tambahkan kelas 'active' ke konten tab yang sesuai
    const targetTab = document.getElementById(tabName);
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Tambahkan kelas 'active' ke tombol yang diklik
    buttonElement.classList.add('active');
};
// ===============================================
// SWEETALERT CUSTOM DELETE EVENT
// ===============================================

window.addEventListener('load', () => {

    const deleteButtons = document.querySelectorAll('.btn-delete-sw');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {

            const form = this.closest('form'); // <-- diperbaiki

            Swal.fire({
                title: 'Hapus event ini?',
                text: 'Event akan dihapus secara permanen.',
                icon: 'warning',
                iconColor: '#f4a261',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: false,
                customClass: {
                    confirmButton: 'sw-confirm-btn',
                    cancelButton: 'sw-cancel-btn'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });
    });
});


