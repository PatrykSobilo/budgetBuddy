<body class="align-items-center py-4 bg-body-tertiary">
  <main class="form-signin w-100 m-auto">
    <div class="loginFormTable mb-5">
      <form action="" method="POST">
        <h1 class="h3 mb-3 fw-normal">Sign in</h1>

        <div class="form-floating">
        <label for="floatingInput">E-mail</label>  
        <input value="<?php echo e($oldFormData['email'] ?? ''); ?>" type="text" class="form-control" id="floatingInput" placeholder="name@example.com" name="login">
        <?php if(array_key_exists('email', $errors)) :  ?>
            <div class="bg-gray-100 mt-2 p-2 text-red-500">
              <?php echo e($errors['email'][0]); ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="form-floating">
        <label for="floatingPassword">Password</label>  
        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
        <?php if(array_key_exists('password', $errors)) :  ?>
            <div class="bg-gray-100 mt-2 p-2 text-red-500">
              <?php echo e($errors['password'][0]); ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="form-check text-start my-3">
          <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
          <label class="form-check-label" for="flexCheckDefault">
            Remember me
          </label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>
      </form>
    </div>
  </main>

  <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>