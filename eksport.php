<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = new PDO('mysql:host=localhost;dbname=columbus_leady', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $typ = $_POST['typ_uslugi'] ?? 'all';

    switch ($typ) {
        case '1':
            $sql = "SELECT * FROM leady WHERE typ_uslugi = 1";
            $nazwa_pliku = "fotowoltaika.csv";
            break;
        case '2':
            $sql = "SELECT * FROM leady WHERE typ_uslugi = 2";
            $nazwa_pliku = "termomodernizacja.csv";
            break;
        case '3':
            $sql = "SELECT * FROM leady WHERE typ_uslugi = 3";
            $nazwa_pliku = "magazyn_energii.csv";
            break;
        default:
            $sql = "SELECT * FROM leady";
            $nazwa_pliku = "wszystkie_leady.csv";
    }

    $stmt = $pdo->query($sql);
    $dane = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=UTF-8');
    header("Content-Disposition: attachment; filename=\"$nazwa_pliku\"");

    echo "\xEF\xBB\xBF";

    $f = fopen('php://output', 'w');

    if (!empty($dane)) {
        fputcsv($f, array_keys($dane[0]), ';');

        foreach ($dane as $wiersz) {
            fputcsv($f, $wiersz, ';');
        }
    }

    fclose($f);
    exit;
}
?>

<form method="POST">
  <label for="typ_uslugi">Eksportuj leady dla:</label><br>
  <select name="typ_uslugi" id="typ_uslugi">
    <option value="all">Wszystkie</option>
    <option value="1">Fotowoltaika</option>
    <option value="2">Termomodernizacja</option>
    <option value="3">Magazyn energii</option>
  </select><br><br>
  <input type="submit" value="Eksportuj do Excela">
</form>
