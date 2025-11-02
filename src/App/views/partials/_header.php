<?php
$oldFormData = $oldFormData ?? [];
$errors = $errors ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <!-- Viewport optimized for iOS and Android -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="theme-color" content="#212529">
  <title><?php echo e($title); ?> - Budget Buddy</title>
  <link rel="stylesheet" href="/assets/main.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/transactions.css" />
  <link rel="stylesheet" href="/assets/responsive.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="/assets/transactions.js" defer></script>
  <script src="/assets/category-limits.js" defer></script>
</head>

<body>
  <section id="navbar" class="px-3 py-2 text-bg-dark border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <!-- Hamburger button for mobile -->
        <button class="navbar-toggler d-lg-none text-white border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <i class="bi bi-list" style="font-size: 2rem;"></i>
        </button>
        
        <!-- Logout button (visible on mobile, positioned right) -->
        <div class="logout-btn-wrapper d-lg-none ms-auto">
          <a href="/logout" class="btn btn-warning logout-btn">Logout</a>
        </div>
        
        <!-- Navigation menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="nav col-12 col-lg-auto my-2 justify-content-start my-md-0 text-small flex-column flex-lg-row">
            <li>
              <a href="/mainPage" class="nav-link text-secondary">
                Home
              </a>
            </li>
            <li>
              <a href="/expenses" class="nav-link text-white">
                Expenses
              </a>
            </li>
            <li>
              <a href="/incomes" class="nav-link text-white">
                Incomes
              </a>
            </li>
            <li>
              <a href="/dashboards" class="nav-link text-white">
                Dashboards
              </a>
            </li>
            <li>
              <a href="/planner" class="nav-link text-white">
                Planner & Analyzer
              </a>
            </li>
            <li>
              <a href="/settings" class="nav-link text-white">
                Settings
              </a>
            </li>
            <li>
              <a href="/about" class="nav-link text-white">
                About
              </a>
            </li>
          </ul>
        </div>
        <!-- Logout button for desktop -->
        <div class="logout-btn-wrapper d-none d-lg-flex">
          <a href="/logout" class="btn btn-warning logout-btn">Logout</a>
        </div>
      </div>
    </div>
  </section>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>