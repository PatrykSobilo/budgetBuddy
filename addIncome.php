<?php
session_start();

if (!isset($_SESSION['zalogowany'])) {
  header('Location: loginForm.php');
  exit();
}

require_once "connect.php";
$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $category = $_POST['incomesCategory'];
  $description = $_POST['description'];
  $amount = $_POST['amount'];
  $date = $_POST['date'];

  try {
    $polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
    if ($polaczenie->connect_errno != 0) {
      throw new Exception(mysqli_connect_errno());
    } else {
      $addIncomeQuery = "INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES ('$user_id', '$category', '$amount', '$date', '$description')";
      
      if ($polaczenie->query($addIncomeQuery)) {
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