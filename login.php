<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO('mysql:host=localhost;dbname=columbus_leady', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM administratorzy WHERE login = ?");
    $stmt->execute([$login]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($haslo, $admin['haslo'])) {
        $_SESSION['zalogowany'] = true;
        header("Location: wyswietl_leady.php");
        exit;
    } else {
        $blad = "Nieprawidłowy login lub hasło.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
</head>
<body>
    <h2>Logowanie</h2>
    <?php if (!empty($blad)) echo "<p style='color:red;'>$blad</p>"; ?>
    <form method="POST">
        <label>Login:</label><br>
        <input type="text" name="login" required><br><br>
        <label>Hasło:</label><br>
        <input type="password" name="haslo" required><br><br>
        <input type="submit" value="Zaloguj">
    </form>
</body>
</html>
