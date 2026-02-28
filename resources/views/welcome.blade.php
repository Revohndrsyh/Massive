<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .hero {
            background: url("{{ asset('images/Background-LandingPage.jpg') }}") center/cover no-repeat;
            color: white;
            padding: 8rem 0;
        }

        .feature-card {
            min-height: 200px;
        }
    </style>
</head>

<body>


    <div class="container-fluid p-0">
        <div class="hero text-center position-relative">
            <div class="overlay" style="position:absolute;inset:0;background:rgba(0,0,0,0.4);"></div>
            <div class="container position-relative text-white py-5" style="max-width:900px;">
                <h1 class="display-4 fw-bold">Unlock the Power of Data with MASSIVE</h1>
                <p class="lead">MASSIVE is a comprehensive analytics platform designed for microbusinesses, offering sentiment analysis, insightful visualizations, and interactive learning modules to help you understand and grow your business.</p>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Get Started</a>
            </div>
        </div>
    </div>

    <section id="features" class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4 text-center">Key Features</h2>
            <p class="text-center text-muted mb-5">MASSIVE provides a suite of tools to help you analyze your business data effectively.</p>
            <div class="row gx-4 gy-4 justify-content-center">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-bar-chart-line display-6 mb-3"></i>
                            <h5 class="card-title">Data Visualization</h5>
                            <p class="card-text small">Visualize your data with interactive charts and graphs, making it easier to identify trends and patterns.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-emoji-smile display-6 mb-3"></i>
                            <h5 class="card-title">Sentiment Analysis</h5>
                            <p class="card-text small">Understand customer feedback and sentiment with advanced analysis tools, helping you improve your products and services.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-book display-6 mb-3"></i>
                            <h5 class="card-title">Interactive Learning</h5>
                            <p class="card-text small">Access a library of interactive modules and tutorials to enhance your data analysis skills and get the most out of MASSIVE.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>