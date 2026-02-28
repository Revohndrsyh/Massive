<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .card {
            border: none;
        }

        .logo {
            font-weight: 700;
            letter-spacing: 1px;
        }
    </style>
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1 class="logo">MASSIVE</h1>
                            <p class="mb-1">Create your account</p>
                            <small class="text-muted">Sign up to get started</small>
                        </div>

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ url('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Your full name">
                            </div>
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ old('no_telp') }}" placeholder="Optional">
                            </div>
                            <div class="mb-3">
                                <label for="nama_usaha" class="form-label">Business Name</label>
                                <input type="text" class="form-control" id="nama_usaha" name="nama_usaha" value="{{ old('nama_usaha') }}" placeholder="Optional">
                            </div>
                            <div class="mb-3">
                                <label for="kategori_usaha" class="form-label">Business Category</label>
                                <input type="text" class="form-control" id="kategori_usaha" name="kategori_usaha" value="{{ old('kategori_usaha') }}" placeholder="Optional">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Re-type password">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Sign up</button>
                        </form>

                        <div class="mt-3 text-center small">
                            Already have an account? <a href="{{ route('login') }}">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>

</html>