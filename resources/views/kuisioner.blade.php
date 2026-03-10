<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuisioner Opini - {{ config('app.name', 'MASSIVE') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 antialiased">
    <nav class="navbar navbar-expand-lg bg-white border-bottom py-3 sticky-top shadow-sm">
        <div class="container-fluid px-4 px-lg-5">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-soundwave text-primary fs-4 me-2"></i>
                <span class="fw-bold" style="color: #0b1c3c; letter-spacing: 1.5px; font-size: 1.1rem;">MASSIVE</span>
            </a>

            <div class="navbar-collapse d-flex justify-content-end" id="navbarNav">
                <ul class="navbar-nav flex-row gap-4 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="{{ url('/') }}">Welcome</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="#">Dashbord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active fw-bold text-dark" aria-current="page" href="{{ route('kuisioner.index') }}">Kuisioner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="#">Modul</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="{{ route('profile.show') }}">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:py-10">
        <h1 class="text-3xl font-extrabold leading-tight text-slate-800 sm:text-4xl">Isi Kuesioner Opini</h1>
        <p class="mt-2 text-sm text-slate-500">Silakan isi kuesioner berikut berdasarkan pengalaman Anda.</p>

        <div class="mt-8 rounded-xl border border-slate-200 bg-white p-5 sm:p-7">
            <h2 class="text-2xl font-bold text-slate-800 sm:text-3xl">Tingkat Kepuasan & Kondisi Bisnis (Skala 1–5)</h2>
            <div class="mt-4 space-y-1 text-sm leading-7 text-slate-600">
                <p><strong class="font-semibold">Petunjuk Pengisian:</strong> Berikan penilaian pada skala 1 sampai 5 untuk setiap pernyataan.</p>
                1 = Sangat Tidak Puas / Sangat Tidak Setuju<br>
                2 = Tidak Puas / Tidak Setuju<br>
                3 = Cukup<br>
                4 = Puas / Setuju<br>
                5 = Sangat Puas / Sangat Setuju
            </div>

            <form method="POST" action="#">
                @csrf

                <section class="mt-8 border-t border-slate-200 pt-7 first:mt-0 first:border-0 first:pt-0">
                    <h3 class="mb-4 text-2xl font-extrabold text-slate-800">Bagian A : Operasional & Produk</h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-slate-50 text-slate-700">
                                <tr>
                                    <th class="border-b border-slate-200 px-3 py-3 text-left font-bold">Opini Bisnis</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">1</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">2</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">3</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">4</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600 sm:min-w-[380px]">Kualitas produk atau layanan yang dihasilkan</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="a_1" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600">Efisiensi proses operasional usaha</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="a_2" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600">Kemampuan memenuhi permintaan pelanggan</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="a_3" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 text-slate-600">Kualitas SDM/karyawan dalam menjalankan operasional</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="px-3 py-3 text-center"><input type="radio" name="a_4" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-8 border-t border-slate-200 pt-7">
                    <h3 class="mb-4 text-2xl font-extrabold text-slate-800">Bagian B : Pemasaran & Hubungan pelanggan</h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-slate-50 text-slate-700">
                                <tr>
                                    <th class="border-b border-slate-200 px-3 py-3 text-left font-bold">Opini Bisnis</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">1</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">2</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">3</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">4</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600 sm:min-w-[380px]">Efektivitas strategi pemasaran yang dilakukan</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="b_1" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600">Kemampuan melakukan pemasaran digital</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="b_2" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600">Kepuasan terhadap interaksi dan layanan pelanggan</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="b_3" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 text-slate-600">Jangkauan pasar dan visibilitas usaha</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="px-3 py-3 text-center"><input type="radio" name="b_4" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-8 border-t border-slate-200 pt-7">
                    <h3 class="mb-4 text-2xl font-extrabold text-slate-800">Bagian C : Keuangan & Akses Modal</h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-slate-50 text-slate-700">
                                <tr>
                                    <th class="border-b border-slate-200 px-3 py-3 text-left font-bold">Opini Bisnis</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">1</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">2</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">3</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">4</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600 sm:min-w-[380px]">Kemampuan mengelola arus kas usaha</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="c_1" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600">Kemudahan mendapatkan akses permodalan</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="c_2" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 text-slate-600">Kemampuan menentukan harga dan keuntungan yang sesuai</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="px-3 py-3 text-center"><input type="radio" name="c_3" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-8 border-t border-slate-200 pt-7">
                    <h3 class="mb-4 text-2xl font-extrabold text-slate-800">Bagian D : Teknologi dan Digitalisasi</h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-slate-50 text-slate-700">
                                <tr>
                                    <th class="border-b border-slate-200 px-3 py-3 text-left font-bold">Opini Bisnis</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">1</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">2</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">3</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">4</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600 sm:min-w-[380px]">Pemanfaatan teknologi dalam operasional bisnis</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="d_1" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600">Kemampuan menggunakan aplikasi bisnis (pencatatan, inventori)</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="d_2" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 text-slate-600">Tingkat kesiapan usaha dalam beradaptasi dengan teknologi digital</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="px-3 py-3 text-center"><input type="radio" name="d_3" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mt-8 border-t border-slate-200 pt-7">
                    <h3 class="mb-4 text-2xl font-extrabold text-slate-800">Bagian E : Tantangan & kebutuhan UMKM</h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-slate-50 text-slate-700">
                                <tr>
                                    <th class="border-b border-slate-200 px-3 py-3 text-left font-bold">Opini Bisnis</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">1</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">2</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">3</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">4</th>
                                    <th class="w-14 border-b border-slate-200 px-3 py-3 text-center font-bold">5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600 sm:min-w-[380px]">Tingkat kesulitan yang dirasakan dalam menjalankan usaha</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="e_1" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="border-b border-slate-100 px-3 py-3 text-slate-600">Kebutuhan terhadap pelatihan atau modul pembelajaran</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="border-b border-slate-100 px-3 py-3 text-center"><input type="radio" name="e_2" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                                <tr>
                                    <td class="px-3 py-3 text-slate-600">Kejelasan arah strategi bisnis jangka panjang</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="px-3 py-3 text-center"><input type="radio" name="e_3" value="{{ $i }}" class="h-4 w-4 cursor-pointer accent-indigo-600"></td>
                                        @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="mt-8 border-t border-slate-200 pt-5 text-right">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700" style="border-radius: 50px;">Kirim</button>
                </div>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>