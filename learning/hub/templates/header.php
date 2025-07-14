
<!doctype html>
<html lang="en-US" data-bs-theme="auto">
<head>
    <title>Manage | LearningHUB</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        /*!
         * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
         * Copyright 2011-2024 The Bootstrap Authors
         * Licensed under the Creative Commons Attribution 3.0 Unported License.
         */
        (() => {
            'use strict'

            const getStoredTheme = () => localStorage.getItem('theme')
            const setStoredTheme = theme => localStorage.setItem('theme', theme)

            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme()
                if (storedTheme) {
                    return storedTheme
                }

                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            }

            const setTheme = theme => {
                if (theme === 'auto') {
                    document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'))
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme)
                }
            }

            setTheme(getPreferredTheme())

            const showActiveTheme = (theme, focus = false) => {
                const themeSwitcher = document.querySelector('#bd-theme')

                if (!themeSwitcher) {
                    return
                }

                const themeSwitcherText = document.querySelector('#bd-theme-text')
                const activeThemeIcon = document.querySelector('.theme-icon-active')
                const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
                const iconOfActiveBtn = btnToActive.querySelector('i').className

                document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
                    element.classList.remove('active')
                    element.setAttribute('aria-pressed', 'false')
                })

                btnToActive.classList.add('active')
                btnToActive.setAttribute('aria-pressed', 'true')
                activeThemeIcon.querySelector('i').className = iconOfActiveBtn


                const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
                themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

                if (focus) {
                    themeSwitcher.focus()
                }
            }

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const storedTheme = getStoredTheme()
                if (storedTheme !== 'light' && storedTheme !== 'dark') {
                    setTheme(getPreferredTheme())
                }
            })

            window.addEventListener('DOMContentLoaded', () => {
                showActiveTheme(getPreferredTheme())

                document.querySelectorAll('[data-bs-theme-value]')
                    .forEach(toggle => {
                        toggle.addEventListener('click', () => {
                            const theme = toggle.getAttribute('data-bs-theme-value')
                            setStoredTheme(theme)
                            setTheme(theme)
                            showActiveTheme(theme)
                        })
                    })
            })
        })();
    </script>
    
    <link rel='stylesheet' id='wp-learning-hub-style-css' href='/learning/hub/style.css' type='text/css' media='all' />

    <style type='text/css'>
    @font-face {
        font-family: 'BC Sans Regular';
        font-weight: 400;
        font-display: swap;
        font-fallback: Noto Sans;
        src: url('BCSans-Regular.woff2') format('woff2');
    }
    </style>
</head>
<body class="bg-body-light d-flex flex-column min-vh-100">

	<a class="skip-link screen-reader-text" href="#results">Skip to content</a>

    <header id="mainheader" role="banner" class="sticky-top">
        <nav class="navbar navbar-expand-lg px-3 bg-gov-blue" data-bs-theme="dark" aria-label="Primary menu">
            <div class="container-fluid px-0 px-lg-3">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon text-white"></span>
                </button>
                <a href="https://learningcentre.gww.gov.bc.ca/learninghub/" class="wordmark navbar-brand order-lg-first mx-1 me-lg-3">
                                        Learning<span class="gov-yellow fw-bold">HUB</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false" aria-label="Toggle search">
                    <span class="search-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mb-2 mb-lg-0 order-1 order-lg-2">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="https://learningcentre.gww.gov.bc.ca/learninghub/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://learningcentre.gww.gov.bc.ca/learninghub/about/">
                                About</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Corporate Learning</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="https://learningcentre.gww.gov.bc.ca/learninghub/what-is-corp-learning-framework/">Corporate Learning Framework</a></li>
                                <li><a class="dropdown-item" href="https://learningcentre.gww.gov.bc.ca/learninghub/corporate-learning-partners/">Learning Partners</a></li>
                                <li><a class="dropdown-item" href="https://learningcentre.gww.gov.bc.ca/learninghub/intake/">Intake for Corporate Learning</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Courses</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="https://learningcentre.gww.gov.bc.ca/learninghub/filter/?keyword=">All Courses</a></li>
                                <li><a class="dropdown-item" href="https://learningcentre.gww.gov.bc.ca/learninghub/foundational-corporate-learning/">Mandatory and Foundational Learning</a></li>
                                <li><a class="dropdown-item" href="https://learningcentre.gww.gov.bc.ca/learninghub/categories/">Course Categorization</a></li>
                                <li><a class="dropdown-item" href="https://learningcentre.gww.gov.bc.ca/learninghub/learning-systems/">Learning Platforms</a></li>
                            </ul>
                        </li>
                        <?php if(canAccess()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/learning/hub/dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="/learning/hub/partners/">Partner index</a></li>
                            </ul>
                        </li>
                        <?php endif ?>

                        <li class="nav-item dropdown">
                            <button class="btn btn-link nav-link ml-3 py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme (dark)">
                                <span class="theme-icon-active"><i class="me-2"></i></span>
                                <span class="d-none ms-2" id="bd-theme-text">Toggle theme</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                                        <i class="bi bi-sun-fill me-2" data-icon="bi-sun-fill"></i>
                                        Light
                                        <i class="bi bi-check2 d-none" data-icon="check2"></i>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="dark" aria-pressed="true">
                                        <i class="bi bi-moon-stars-fill me-2" data-icon="bi-moon-stars-fill"></i>
                                        Dark
                                        <i class="bi bi-check2 d-none" data-icon="check2"></i>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                                        <i class="bi bi-circle-half me-2" data-icon="bi-circle-half"></i>
                                        Auto
                                        <i class="bi bi-check2 d-none" data-icon="check2"></i>
                                    </button>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- <a href="/learninghub/filter/" class="btn btn-primary">Search the catalogue</a> -->
                <form method="get" action="/learninghub/filter/" data-bs-theme="light" class="collapse navbar-collapse row g-1 flex-nowrap" role="search" id="navbarSearch">
                    <label for="keyword" class="visually-hidden">Search</label>
                    <div class="col-auto flex-grow-1 flex-shrink-1"><input type="search" id="keyword" class="s form-control" name="keyword" placeholder="Search catalogue" required value=""></div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-secondary" aria-label="Submit Search">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </nav>
    </header><!-- #masthead -->
    <div id="content" class="bg-secondary-subtle">