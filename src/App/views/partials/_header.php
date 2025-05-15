<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php echo e($title); ?> - Budget Buddy</title>

  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


</head>

<body>
  <header>
    <section id="navbar" class="px-3 py-2 text-bg-dark border-bottom">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
          <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            <li>
              <a href="mainPage.php" class="nav-link text-secondary">
                Home
              </a>
            </li>
            <li>
              <a href="expenses.php" class="nav-link text-white">
                Expenses
              </a>
            </li>
            <li>
              <a href="incomes.php" class="nav-link text-white">
                Incomes
              </a>
            </li>
            <li>
              <a href="dashboards.php" class="nav-link text-white">
                Dashboards
              </a>
            </li>
            <li>
              <a href="about" class="nav-link text-white">
                About
              </a>
            </li>
            <li>
              <a href="#" class="nav-link text-white">
                Planner & Analyzer
              </a>
            </li>
            <li>
              <a href="logout.php" class="nav-link text-white">
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </section>