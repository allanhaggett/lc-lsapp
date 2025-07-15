<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <title>LC Coursework Page Mockup</title>
    <style>
        .bg-psa {
            background-color: #003366;
        }

        .nav-tabs .nav-link.active {
            background-color: #003366;
            color: white;
            border-color: #003366;
        }

        .nav-tabs .nav-link {
            border-color: #003366;
            color: #003366;
            font-weight: 500;
            border-width: 2px 2px 0px 2px;
        }

        .nav-tabs .nav-link:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .tab-content {
            border: #003366 2px solid;
            border-radius: 0 .25rem .25rem .25rem;
        }
    </style>
</head>
<body class="min-vh-100 flex-column d-flex">
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-xxl">
            <a href="https://learningcentre.gww.gov.bc.ca/" target="_blank" rel="noopener" class="navbar-brand">
                <img alt="Brought to you by the Learning Centre" height="80" src="https://learn.bcpublicservice.gov.bc.ca/common-components/learning-centre-logo-wordmark.svg">
            </a>
            <!-- toggle button for mobile nav -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- navbar links -->
            <div class="collapse navbar-collapse justify-content-end align-center px-lg-0 px-2" id="main-nav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="#" class="nav-link">PSA Learning System</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Learning<strong>HUB</strong></a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Help (AskMyHR)</a>
                    </li>
                    <?php if(canAccess()): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link disabled">LSApp</a>
                    </li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
        </div>
    </nav>
    <!-- header -->
    <header class="bg-psa text-white p-lg-3 p-2">
        <section id="header">
            <div class="container-lg">
                <h1>
                    <div class="display-2"><?= $deets[2] ?></div>
                    <div class="display-5"><?= $deets[3] ?></div>
                </h1>
            </div>
        </section>
    </header>

<!-- END OF HEADER INLCUDE -->


