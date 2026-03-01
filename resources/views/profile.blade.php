<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile - MASSIVE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<nav class="navbar navbar-expand-lg bg-white border-bottom py-3">
    <div class="container-fluid px-4 px-lg-5">

        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="bi bi-soundwave text-primary fs-4 me-2"></i>
            <span class="fw-bold" style="color: #0b1c3c; letter-spacing: 1.5px; font-size: 1.1rem;">MASSIVE</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav gap-lg-4 align-items-lg-center mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="#">Welcome</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="#">Dashbord</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="{{ route('kuisioner.index') }}">Kuisioner</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="#">Modul</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-bold text-dark" aria-current="page" href="{{ route('profile.show') }}">Profile</a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<body class="bg-light">
    <div class="container py-5">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="row justify-content-center">

                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://via.placeholder.com/100' }}" id="preview-avatar" class="rounded-circle mb-3 border" alt="avatar" width="100" height="100" style="object-fit: cover;">

                            <h5 class="card-title">{{ $user->name }}</h5>
                            <p class="text-muted small">Pemilik UMKM</p>

                            <label for="avatarInput" class="d-block small text-primary" style="cursor: pointer; text-decoration: underline;">
                                Ganti Foto Profil
                            </label>
                            <input type="file" class="d-none" id="avatarInput" name="avatar" accept="image/*" onchange="previewImage(event)">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Informasi Pribadi</h5>

                            @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Usaha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-shop"></i></span>
                                    <input type="text" class="form-control" name="nama_usaha" value="{{ old('nama_usaha', $user->nama_usaha) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kategori Usaha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tags"></i></span>
                                    <input type="text" class="form-control" name="kategori_usaha" value="{{ old('kategori_usaha', $user->kategori_usaha) }}">
                                </div>
                            </div>

                            <hr>

                            <h6>Ganti Password</h6>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="password" id="new-password">
                                    <span class="input-group-text" id="toggle-new" style="cursor:pointer;"><i class="bi bi-eye" id="new-icon"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="#" class="btn btn-secondary me-2">Batal</a>
                                <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        // Script Toggle Password
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('toggle-new');
            const pwd = document.getElementById('new-password');
            const icon = document.getElementById('new-icon');
            toggle.addEventListener('click', function() {
                if (pwd.type === 'password') {
                    pwd.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    pwd.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });

        // Script Preview Gambar Sebelum Diupload
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview-avatar');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>