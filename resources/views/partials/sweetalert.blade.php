<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}'
        });
    @endif

    @if ($errors->any())
        var errorList = '<ul style="text-align: left; list-style-position: inside;">';
        @foreach ($errors->all() as $error)
            errorList += '<li>{{ $error }}</li>';
        @endforeach
        errorList += '</ul>';

        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: errorList,
        });
    @endif
</script>


