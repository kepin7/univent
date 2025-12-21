/**
 * Fungsi untuk melakukan masking pada alamat email.
 * Contoh: "mekurukito070@gmail.com" -> "me*********@gmail.com"
 * * @param {string} email - Alamat email yang akan di-masking.
 * @param {number} startChars - Jumlah karakter yang ditampilkan di awal local part.
 * @returns {string} Alamat email yang sudah di-masking.
 */
function maskEmail(email, startChars = 2) {
    if (!email || email.indexOf('@') === -1) {
        return email;
    }

    const parts = email.split('@');
    const localPart = parts[0];
    const domainPart = parts[1];

    if (localPart.length <= startChars) {
        // Jika nama lokal terlalu pendek, tampilkan semua karakter lokal
        return localPart + '@' + domainPart;
    }

    // Ambil karakter awal yang akan ditampilkan
    const start = localPart.substring(0, startChars);
    
    // Hitung jumlah karakter yang akan di-masking
    const maskLength = localPart.length - startChars;
    
    // Buat string masking
    const mask = '*'.repeat(maskLength);

    // Gabungkan: awal + mask + @domain
    return start + mask + '@' + domainPart;
}


document.addEventListener('DOMContentLoaded', () => {
    // --- Logika OTP yang Sudah Ada ---
    const inputsContainer = document.getElementById('otp-inputs');
    if (!inputsContainer) return; 

    const inputs = [...inputsContainer.querySelectorAll('input[type=text]')];
    const hiddenOtpInput = document.getElementById('otp-hidden-input');

    if (!hiddenOtpInput) return;

    const updateHiddenInput = () => {
        hiddenOtpInput.value = inputs.map(input => input.value).join('');
    };

    inputsContainer.addEventListener('input', (e) => {
        const target = e.target;
        const nextInput = target.nextElementSibling;
        
        if (target.matches('input') && target.value.length === 1 && nextInput && nextInput.matches('input')) {
            nextInput.focus();
        }
        updateHiddenInput();
    });

    inputsContainer.addEventListener('keydown', (e) => {
        const target = e.target;
        if (!target.matches('input')) return;

        const prevInput = target.previousElementSibling;

        if (e.key === 'Backspace' && target.value.length === 0 && prevInput && prevInput.matches('input')) {
            e.preventDefault();
            prevInput.focus();
            prevInput.value = '';
        }
        updateHiddenInput();
    });
    
    // Handle paste
    if (inputs.length > 0) {
        inputs[0].addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (pasteData.length === 6 && /^\d+$/.test(pasteData)) {
                inputs.forEach((input, index) => {
                    input.value = pasteData[index];
                });
                updateHiddenInput();
                inputs[5].focus();
            }
        });
    }
    updateHiddenInput();


    // --- Logika Masking Email BARU ---
    const emailDisplayElement = document.getElementById('email-display');
    
    if (emailDisplayElement) {
        const originalEmail = emailDisplayElement.getAttribute('data-email');
        
        if (originalEmail) {
            // Lakukan masking dan ganti teks di elemen <strong>
            const maskedEmail = maskEmail(originalEmail, 2); // Tampilkan 2 karakter awal (me*********)
            emailDisplayElement.textContent = maskedEmail;
        }
    }
    // --- Akhir Logika Masking Email BARU ---
});