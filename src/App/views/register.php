<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>Register Form</title>
</head>

<body class="align-items-center py-2 bg-body-tertiary">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="/" class="btn btn-link text-decoration-none d-inline-flex align-items-center mt-2 ms-2"
                    style="font-size:1rem;">
                    <i class="bi bi-arrow-left" style="font-size:1.2rem;"></i>&nbsp;Back to main Page
                </a>
            </div>
        </div>
    </div>
    <main class="d-flex justify-content-center align-items-center" style="min-height:70vh;">
        <div class="registerFormTable w-50"
            style="min-width:320px; max-width:500px; padding:2rem 1.5rem; box-shadow:0 2px 16px rgba(0,0,0,0.07); border-radius:1rem; background:#fff;">
            <form method="POST">

                <?php include $this->resolve('partials/_csrf.php'); ?>

                <h1 class="h4 mb-3 fw-normal text-center">Register</h1>

                <div class="form-floating mb-2">
                    <input type="email" value="<?php echo e($oldFormData['email'] ?? ''); ?>" name="email" class="form-control"
                        id="" placeholder="name@example.com">
                    <?php if (array_key_exists('email', $errors)) : ?>
                        <div class="bg-gray-100 mt-2 p-2 text-danger">
                            <?php echo e($errors['email'][0]); ?>
                        </div>
                    <?php endif; ?>
                    <label for="floatingInput">Email address</label>
                </div>

                <div class="form-floating mb-2">
                    <input type="number" step="1" value="<?php echo e($oldFormData['age'] ?? ''); ?>" name="age" class="form-control"
                        id="" placeholder="Age">
                    <?php if (array_key_exists('age', $errors)) : ?>
                        <div class="bg-gray-100 mt-2 p-2 text-danger">
                            <?php echo e($errors['age'][0]); ?>
                        </div>
                    <?php endif; ?>
                    <label for="floatingPassword">Age</label>
                </div>

                <div class="form-floating mb-2">
                    <input type="password" value="" name="password" class="form-control" id="" placeholder="Password">
                    <?php if (array_key_exists('password', $errors)) : ?>
                        <div class="bg-gray-100 mt-2 p-2 text-danger">
                            <?php echo e($errors['password'][0]); ?>
                        </div>
                    <?php endif; ?>
                    <label for="floatingPassword">Password</label>
                </div>

                <div class="form-floating mb-2">
                    <input type="password" value="" name="passwordConfirmation" class="form-control" id=""
                        placeholder="Repeat Password">
                    <?php if (array_key_exists('passwordConfirmation', $errors)) : ?>
                        <div class="bg-gray-100 mt-2 p-2 text-danger">
                            <?php echo e($errors['passwordConfirmation'][0]); ?>
                        </div>
                    <?php endif; ?>
                    <label for="floatingPassword">Repeat Password</label>
                </div>

                <div class="form-floating mb-2">
                    <div class="mt-2">
                        <div>
                            <label class="inline-flex items-center">
                                <input <?php echo $oldFormData['tos'] ?? false ? 'checked' : ''; ?> name="tos"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50"
                                    type="checkbox" />
                                <span class="ms-2">I accept the terms of service.</span>
                            </label>
                            <?php if (array_key_exists('email', $errors)) : ?>
                                <div class="bg-gray-100 mt-2 p-2 text-danger">
                                    <?php echo e($errors['email'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary w-100 py-2 mt-2" type="submit">Register</button>
            </form>
        </div>
    </main>

    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>

    <?php include $this->resolve("partials/_footer.php"); ?>
</body>

</html>