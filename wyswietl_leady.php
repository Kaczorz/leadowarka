<?php

session_start();
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    header("Location: login.php");
    exit;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO('mysql:host=localhost;dbname=columbus_leady', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$uslugiStmt = $pdo->query("SELECT id, nazwa FROM uslugi");
$typyUslug = [];
foreach ($uslugiStmt as $row) {
    $typyUslug[$row['id']] = $row['nazwa'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM leady WHERE id = ?");
    $stmt->execute([$_POST['delete']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $stmt = $pdo->prepare("UPDATE leady SET imie_nazwisko = ?, email = ?, telefon = ?, adres = ?, status = ?, typ_uslugi = ? WHERE id = ?");
    $stmt->execute([
        $_POST['imie_nazwisko'],
        $_POST['email'],
        $_POST['telefon'],
        $_POST['adres'],
        $_POST['status'],
        $_POST['typ_uslugi'],
        $_POST['save']
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$stmt = $pdo->query("
    SELECT leady.*, uslugi.nazwa AS nazwa_uslugi
    FROM leady
    LEFT JOIN uslugi ON leady.typ_uslugi = uslugi.id
    ORDER BY leady.data_dodania DESC
");
$leady = $stmt->fetchAll(PDO::FETCH_ASSOC);

$editId = $_POST['edit'] ?? null;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Leady – operacje</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #aaa; padding: 8px; }
        th { background: #eee; }
        input, select, textarea { width: 100%; }
        form { margin: 0; }
    </style>
</head>
<body>
<h1>Lista leadów</h1>
<p><a href="logout.php">Wyloguj</a></p>
<p><a href="eksport.php">Eksportuj</a></p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Imię i nazwisko</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Adres</th>
            <th>Typ usługi</th>
            <th>Status</th>
            <th>Dodano</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($leady as $lead): ?>
            <tr>
                <?php if ($editId == $lead['id']): ?>
                    <form method="POST">
                        <td><?= $lead['id'] ?><input type="hidden" name="save" value="<?= $lead['id'] ?>"></td>
                        <td><input name="imie_nazwisko" value="<?= htmlspecialchars($lead['imie_nazwisko']) ?>"></td>
                        <td><input name="email" value="<?= htmlspecialchars($lead['email']) ?>"></td>
                        <td><input name="telefon" value="<?= htmlspecialchars($lead['telefon']) ?>"></td>
                        <td><textarea name="adres"><?= htmlspecialchars($lead['adres']) ?></textarea></td>
                        <td>
                            <select name="typ_uslugi">
                                <?php foreach ($typyUslug as $id => $nazwa): ?>
                                    <option value="<?= $id ?>" <?= $lead['typ_uslugi'] == $id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nazwa) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="status">
                                <?php foreach (['nowy','w_kontakcie','w_realizacji','zrealizowany'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $lead['status'] == $status ? 'selected' : '' ?>>
                                        <?= $status ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?= $lead['data_dodania'] ?></td>
                        <td><button type="submit">Zapisz</button></td>
                    </form>
                <?php else: ?>
                    <td><?= $lead['id'] ?></td>
                    <td><?= htmlspecialchars($lead['imie_nazwisko']) ?></td>
                    <td><?= htmlspecialchars($lead['email']) ?></td>
                    <td><?= htmlspecialchars($lead['telefon']) ?></td>
                    <td><?= htmlspecialchars($lead['adres']) ?></td>
                    <td><?= htmlspecialchars($lead['nazwa_uslugi'] ?? 'Nieznana') ?></td>
                    <td><?= htmlspecialchars($lead['status']) ?></td>
                    <td><?= $lead['data_dodania'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <button type="submit" name="edit" value="<?= $lead['id'] ?>">Edytuj</button>
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Na pewno usunąć ten rekord?');">
                            <button type="submit" name="delete" value="<?= $lead['id'] ?>">Usuń</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
