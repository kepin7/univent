document.addEventListener('DOMContentLoaded', function () {
    const avatarInput = document.getElementById('avatar-upload-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const newAvatarTemp = document.getElementById('new-avatar-temp');
    const removeAvatar = document.getElementById('remove-avatar');
    const removeButton = document.getElementById('btn-remove-photo');

    if (!avatarPreview) {
        console.warn('avatar-preview element not found.');
        return;
    }

    // ambil default dari data-default atau fallback path
    const defaultAvatarSrc = avatarPreview.dataset?.default || '/images/default-avatar.svg';

    // --- helper: reset preview ke default ---
    function showDefaultAvatar() {
        avatarPreview.src = defaultAvatarSrc;
    }

    // --- helper: reset file input (HTMLFileInputElement) ---
    function resetFileInput(inputEl) {
        if (!inputEl) return;
        try {
            inputEl.value = '';
            // untuk beberapa browser mungkin perlu replaceNode trick:
            // const form = inputEl.parentNode;
            // const clone = inputEl.cloneNode(true);
            // inputEl.parentNode.replaceChild(clone, inputEl);
        } catch (e) {
            console.warn('Could not reset file input cleanly', e);
        }
    }

    // Jika user memilih file -> preview + set newAvatarTemp (base64) + pastikan remove-avatar = 0
    if (avatarInput) {
        avatarInput.addEventListener('change', function (e) {
            const file = this.files?.[0];
            if (!file) return;

            removeAvatar.value = 0; // user tidak menghapus
            const reader = new FileReader();
            reader.onload = function (ev) {
                const base64 = ev.target.result;
                avatarPreview.src = base64;
                newAvatarTemp.value = base64;
            };
            reader.readAsDataURL(file);
        });
    }

    // Remove button handler
    if (removeButton) {
        removeButton.addEventListener('click', function (e) {
            e.preventDefault();

            // jika Swal ada, pakai modal yang lebih bagus. Kalau tidak, fallback ke confirm()
            const confirmRemove = function () {
                // tandai untuk dihapus saat submit
                removeAvatar.value = 1;

                // kosongkan temp avatar (tidak akan dikirim)
                if (newAvatarTemp) newAvatarTemp.value = '';

                // reset file input supaya tidak ada file tersisa
                resetFileInput(avatarInput);

                // tampilkan default avatar di preview
                showDefaultAvatar();

                // beri feedback visual (opsional)
                removeButton.classList.add('removed');
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "Hapus foto profil?",
                    text: "Foto akan hilang setelah Anda klik Save Profile.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal"
                }).then(result => {
                    if (result.isConfirmed) confirmRemove();
                });
            } else {
                if (confirm("Hapus foto profil? Foto akan hilang setelah Anda klik Save Profile.")) {
                    confirmRemove();
                }
            }
        });
    } else {
        console.warn('Remove button (#btn-remove-photo) not found.');
    }

   
    const cancelLink = document.querySelector('.btn-cancel-edit');
    if (cancelLink) {
        cancelLink.addEventListener('click', function () {
            // revert preview to DB avatar or default (server will re-render page on navigation anyway)
            const oldAvatar = document.getElementById('old-avatar')?.value;
            if (oldAvatar) {
                avatarPreview.src = 'data:image/*;base64,' + oldAvatar;
            } else {
                showDefaultAvatar();
            }
            newAvatarTemp.value = '';
            removeAvatar.value = 0;
        });
    }
    
});
