<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <title>Document</title>
</head>

<body class="align-items-center bg-body-tertiary">
  <section id="navbar" class="px-3 py-2 text-bg-dark border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <div></div>
        <div class="d-flex align-items-center gap-2">
          <a href="/" class="btn btn-primary"><i class="bi bi-arrow-left me-1"></i>Back to main page</a>
        </div>
      </div>
    </div>
  </section>
  <main class="d-flex justify-content-center align-items-center" style="min-height:70vh;">
    <div class="loginFormTable w-50" style="min-width:320px; max-width:500px; padding:2rem 1.5rem; box-shadow:0 2px 16px rgba(0,0,0,0.07); border-radius:1rem; background:#fff;">
      <form method="POST">

        <?php include $this->resolve('partials/_csrf.php'); ?>

        <h1 class="h4 mb-3 fw-normal text-center">Sign in</h1>

        <div class="form-floating mb-2">
          <input value="<?php echo e($oldFormData['email'] ?? ''); ?>" type="email" class="form-control" id="floatingInput" placeholder="E-mail" name="email">
          <label for="floatingInput">E-mail</label>
          <?php if(array_key_exists('email', $errors)) :  ?>
            <div class="bg-gray-100 mt-2 p-2 text-danger">
              <?php echo e($errors['email'][0]); ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="form-floating mb-2">
          <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
          <label for="floatingPassword">Password</label>
          <?php if(array_key_exists('password', $errors)) :  ?>
            <div class="bg-gray-100 mt-2 p-2 text-danger">
              <?php echo e($errors['password'][0]); ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="form-check text-start my-2">
          <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
          <label class="form-check-label" for="flexCheckDefault">
            Remember me
          </label>
        </div>
        <button class="btn btn-primary w-100 py-2 mt-2" type="submit">Sign in</button>
      </form>
    </div>
  </main>

  <?php include $this->resolve("partials/_footer.php"); ?>

  <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>