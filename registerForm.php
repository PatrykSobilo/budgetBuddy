<?php
session_start();

if (isset($_POST['email'])) {
  $wszystko_OK = true;

  $username = $_POST['username'];
  if ((strlen($username) < 3) || (strlen($username) > 20)) {
    $wszystko_OK = false;
    $_SESSION['e_username'] = "Nazwa użytkownika musi posiadać od 3 do 20 znaków!";
  }
  if (ctype_alnum($username) == false) {
    $wszystko_OK = false;
    $_SESSION['e_username'] = "Nazwa użytkownika może składać się tylko z liter i cyfr (bez polskich znaków)";
  }

  $email = $_POST['email'];
  $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
  if (filter_var($emailB, FILTER_VALIDATE_EMAIL) == false || ($emailB != $email)) {
    $wszystko_OK = false;
    $_SESSION['e_email'] = "Podaj poprawny e-mail!";
  }

  $password1 = $_POST['password1'];
  $password2 = $_POST['password2'];
  if ((strlen($password1) < 8) || (strlen($password1) > 20)) {
    $wszystko_OK = false;
    $_SESSION['e_haslo'] = "Hasło musi posiadać od 8 do 20 znaków!";
  }
  if ($password1 != $password2) {
    $wszystko_OK = false;
    $_SESSION['e_haslo'] = "Podane hasła nie są identyczne!";
  }
  $haslo_hash = password_hash($password1, PASSWORD_DEFAULT);

  $_SESSION['fr_username'] = $username;
  $_SESSION['fr_email'] = $email;
  $_SESSION['fr_password1'] = $password1;
  $_SESSION['fr_password2'] = $password2;

  require_once "connect.php";
  mysqli_report(MYSQLI_REPORT_STRICT);
  try {
    $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
    if ($polaczenie->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      $rezultat = $polaczenie->query("SELECT id FROM users WHERE email = '$email'");
      if (!$rezultat) throw new Exception($polaczenie->error);

      $ile_takich_maili = $rezultat->num_rows;
      if ($ile_takich_maili > 0) {
        $wszystko_OK = false;
        $_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu email!";
      }

      $rezultat = $polaczenie->query("SELECT id FROM users WHERE username = '$username'");
      if (!$rezultat) throw new Exception($polaczenie->error);

      $ile_takich_nickow = $rezultat->num_rows;
      if ($ile_takich_nickow > 0) {
        $wszystko_OK = false;
        $_SESSION['e_nick'] = "Istnieje już gracz o takim nicku!";
      }

      if ($wszystko_OK == true) {
        if ($polaczenie->query("INSERT INTO users (username, password, email) VALUES ('$username', '$haslo_hash', '$email')")) {
          $_SESSION['udanarejestracja'] = true;

          $result = $polaczenie->query("SELECT id FROM users WHERE email = '$email'");
          if (!$result) throw new Exception($polaczenie->error);
          $row = $result->fetch_assoc();
          $registeredUserId = $row['id'];

          $polaczenie->query("INSERT INTO expenses_category_assigned_to_users (user_id, name)
                              SELECT '$registeredUserId' AS user_id, name 
                              FROM expenses_category_default");

          $polaczenie->query("INSERT INTO incomes_category_assigned_to_users (user_id, name)
                              SELECT '$registeredUserId' AS user_id, name 
                              FROM incomes_category_default");

          $polaczenie->query("INSERT INTO payment_methods_assigned_to_users (user_id, name)
                              SELECT '$registeredUserId' AS user_id, name 
                              FROM payment_methods_default");

          header('Location: loginForm.php');
        } else {
          throw new Exception($polaczenie->error);
        }
      }
      $polaczenie->close();
    }
  } catch (Exception $e) {
    echo '<span style="color:red;">Błąd serwera!</span>';
    echo '<br />Informacja developerska: ' . $e;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <title>budget_buddy - Register Form</title>
</head>

<body class="align-items-center py-4 bg-body-tertiary">
  <main class="form-register w-100 m-auto">
    <div class="registerFormTable mb-5">
      <form method="post">
        <h1 class="h3 mb-3 fw-normal">Register</h1>

        <div class="form-floating">
          <input type="text" value="<?php
                                    if (isset($_SESSION['fr_username'])) {
                                      echo $_SESSION['fr_username'];
                                      unset($_SESSION['fr_username']);
                                    }
                                    ?>" class="form-control" id="floatingPassword" name="username" placeholder="Username">
          <?php
          if (isset($_SESSION['e_username'])) {
            echo '<div class="error">' . $_SESSION['e_username'] . '</div>';
            unset($_SESSION['e_username']);
          }
          ?>
          <label for="floatingPassword">Username</label>
        </div>

        <div class="form-floating">
          <input type="password" value="<?php
                                        if (isset($_SESSION['fr_password1'])) {
                                          echo $_SESSION['fr_password1'];
                                          unset($_SESSION['fr_password1']);
                                        }
                                        ?>" name="password1" class="form-control" id="floatingPassword" placeholder="Password">
          <?php
          if (isset($_SESSION['e_haslo'])) {
            echo '<div class="error">' . $_SESSION['e_haslo'] . '</div>';
            unset($_SESSION['e_haslo']);
          }
          ?>
          <label for="floatingPassword">Password</label>
        </div>

        <div class="form-floating">
          <input type="password" value="<?php
                                        if (isset($_SESSION['fr_password2'])) {
                                          echo $_SESSION['fr_password2'];
                                          unset($_SESSION['fr_password2']);
                                        }
                                        ?>" name="password2" class="form-control" id="floatingPasswordRepeat" placeholder="Repeat Password">
          <label for="floatingPassword">Repeat Password</label>
        </div>

        <div class="form-floating">
          <input type="email" value="<?php
                                      if (isset($_SESSION['fr_email'])) {
                                        echo $_SESSION['fr_email'];
                                        unset($_SESSION['fr_email']);
                                      }
                                      ?>" name="email" class="form-control" id="floatingInput" placeholder="name@example.com">
          <?php
          if (isset($_SESSION['e_email'])) {
            echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
            unset($_SESSION['e_email']);
          }
          ?>
          <label for="floatingInput">Email address</label>
        </div>

        <button class="btn btn-primary w-100 py-2 mt-5" type="submit">Register</button>
      </form>
    </div>
  </main>

  <section id="footer"></section>
  <div class="container">
    <footer class="py-3">
      <p class="text-center text-body-secondary mt-5">© 2024 BudgetBuddy Sp. z o.o.</p>
    </footer>
  </div>
  </section>

  <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>