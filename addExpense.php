<?php
session_start();

if (!isset($_SESSION['zalogowany'])) {
  header('Location: loginForm.php');
  exit();
}

require_once "connect.php";
$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $category = $_POST['expensesCategory'];
  $paymentMethods = $_POST['paymentMethods'];
  $amount = $_POST['amount'];
  $date = $_POST['date'];
  $description = $_POST['description'];

  try {
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    if ($polaczenie->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      $addExpenseQuery = "INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES ('$user_id', '$category', '$paymentMethods', '$amount', '$date', '$description')";
      
      if ($polaczenie->query($addExpenseQuery)) {
        header('Location: mainPage.php');
        exit();
      } else {
        echo 'Error: ' . $polaczenie->error;
      }

      $polaczenie->close();
    }
  } catch (Exception $e) {
    echo '<span style="color:red;">Błąd serwera!</span>';
    echo '<br />Informacja developerska: ' . $e;
  }
}
?>