<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=columbus_leady', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $imie_nazwisko = $_POST['imie_nazwisko'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $adres = $_POST['adres'] ?? '';
    $typ_uslugi = isset($_POST['typ_uslugi']) ? (int)$_POST['typ_uslugi'] : null;

    if (empty($imie_nazwisko) || empty($email) || empty($telefon) || empty($adres) || $typ_uslugi === null) {
        throw new Exception("❗ Brakuje wymaganych danych.");
    }

    $sql = "INSERT INTO leady
        (imie_nazwisko, email, telefon, adres, data_dodania, data_aktualizacji, status, typ_uslugi)
        VALUES 
        (:imie_nazwisko, :email, :telefon, :adres, NOW(), NOW(), 'nowy', :typ_uslugi)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':imie_nazwisko' => $imie_nazwisko,
        ':email' => $email,
        ':telefon' => $telefon,
        ':adres' => $adres,
        ':typ_uslugi' => $typ_uslugi
    ]);

    echo "Lead został dodany pomyślnie!";
} catch (PDOException $e) {
    echo "Błąd bazy danych (PDO): " . $e->getMessage();
} catch (Exception $e) {
    echo "Błąd aplikacji: " . $e->getMessage();
}
?>
