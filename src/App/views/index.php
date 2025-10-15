<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <title>Budget Buddy</title>
</head>

<body>
  <section id="navbar" class="px-3 py-2 text-bg-dark border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <div></div>
        <div class="d-flex align-items-center gap-2">
          <a href="login" class="btn btn-primary">Login</a>
          <a href="register" class="btn btn-warning logout-btn">Register</a>
        </div>
      </div>
    </div>
  </section>

  <section id="nameAndUserAccess">
    <div class="px-4 pt-5 text-center"> 

      <h1 class="display-4 fw-bold text-body-emphasis">BudgetBuddy: Your Personal Finance Manager</h1>
      <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">BudgetBuddy is a user-friendly web application designed to help you manage your budget and
          expenses effortlessly. Accessible through any web browser, BudgetBuddy ensures you have control over your
          finances anytime, anywhere, without the need for downloads or installations.</p>
      </div>
      <div class="overflow-hidden" style="max-height: 45vh;">
        <div class="container px-5">
          <img src="./resources/9055e486-97d1-4c9a-b27a-4f9b897f515f.jpg" class="img-fluid border rounded-3 shadow-lg mb-4"
            alt="BudgetBuddy app preview" width="700" height="500" loading="lazy">
        </div>
      </div>
    </div>
  </section>

  <section id="featuresDescription">
    <div class="container px-4 py-5" id="icon-grid">
      <h2 class="pb-2 border-bottom">What do you get?</h2>

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4 py-5">
        <div class="col d-flex align-items-start">
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">Expense Tracking</h3>
            <p>Easily log daily expenses with a few clicks.</p>
            <p>Categorize expenses for better insights (e.g., groceries, entertainment, utilities).</p>
          </div>
        </div>
        <div class="col d-flex align-items-start">
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">Budget Planning</h3>
            <p>Set monthly or weekly budgets for different categories.</p>
            <p>Receive alerts when you’re nearing your budget limits.</p>
          </div>
        </div>
        <div class="col d-flex align-items-start">
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">Financial Insights</h3>
            <p>Visualize your spending habits with interactive charts and graphs.</p>
            <p>Generate detailed reports to understand where your money goes.</p>
          </div>
        </div>
        <div class="col d-flex align-items-start">
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">Goal Setting</h3>
            <p>Define financial goals (e.g., saving for a vacation, paying off debt).</p>
            <p>Track your progress and stay motivated with milestone reminders.</p>
          </div>
        </div>
        <div class="col d-flex align-items-start">
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">Secure and Private</h3>
            <p>Your data is encrypted and securely stored.</p>
            <p>Privacy-focused design ensures your financial information remains confidential.</p>
          </div>
        </div>
        <div class="col d-flex align-items-start">
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">User-Friendly Interface</h3>
            <p>Intuitive design makes it easy for anyone to use, regardless of tech-savviness.</p>
            <p>Responsive layout ensures a seamless experience on any device.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="whyChooseBudgetBuddy">
    <div class="container px-4 py-5" id="icon-grid">
      <h2 class="pb-2 border-bottom">Why choose BudgetBuddy?</h2>

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4 py-5">
        <div class="col d-flex flex-column">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
            class="bi bi-clock-history mb-3" viewBox="0 0 16 16">
            <path
              d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z" />
            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
            <path
              d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
          </svg>
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">No Installations Needed</h3>
            <p>Access BudgetBuddy directly from your web browser.</p>
          </div>
        </div>
        <div class="col d-flex flex-column">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
            class="bi bi-check-all mb-3" viewBox="0 0 16 16">
            <path
              d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486z" />
          </svg>
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">Real-Time Updates</h3>
            <p>Your financial data is always up-to-date.</p>
          </div>
        </div>
        <div class="col d-flex flex-column">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-tools mb-3"
            viewBox="0 0 16 16">
            <path
              d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3q0-.405-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708M3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z" />
          </svg>
          <div>
            <h3 class="fw-bold mb-0 fs-4 text-body-emphasis mb-2">Customizable</h3>
            <p>Tailor the app to fit your unique financial needs and preferences.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="footer">
    <div class="container">
      <footer class="py-3">
        <p class="text-center text-body-secondary">© <?php echo date('Y'); ?> BudgetBuddy Sp. z o.o.</p>
      </footer>
    </div>
  </section>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="index.js"></script>
</body>

</html>