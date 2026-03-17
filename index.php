<?php
session_start(); // PRG pattern - start session [cite: 322]

// Připojení k databázi [cite: 280]
$db = new PDO("sqlite:profile.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Načtení statických dat z JSON (jméno, dovednosti)
$jsonFile = 'profile.json';
$data = json_decode(file_get_contents($jsonFile), true);

// Zpracování POST požadavků
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // AKCE: PŘIDÁNÍ [cite: 286-287]
    if ($action === 'add') {
        $newInterest = trim($_POST["new_interest"] ?? '');
        if (empty($newInterest)) {
            $_SESSION['message'] = "Pole nesmí být prázdné.";
            $_SESSION['messageType'] = "error";
        } else {
            // Kontrola duplicity bez ohledu na velká/malá písmena
            $check = $db->prepare("SELECT COUNT(*) FROM interests WHERE LOWER(name) = LOWER(?)");
            $check->execute([$newInterest]);
            
            if ($check->fetchColumn() > 0) {
                $_SESSION['message'] = "Tento zájem už existuje.";
                $_SESSION['messageType'] = "error";
            } else {
                $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)"); // [cite: 288-289]
                $stmt->execute([$newInterest]); // [cite: 319-320]
                $_SESSION['message'] = "Zájem byl přidán."; // [cite: 310]
                $_SESSION['messageType'] = "success";
            }
        }
    } 
    // AKCE: SMAZÁNÍ [cite: 293-294]
    elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM interests WHERE id = ?"); // [cite: 295-296]
        $stmt->execute([$id]);
        $_SESSION['message'] = "Zájem byl odstraněn."; // [cite: 312]
        $_SESSION['messageType'] = "success";
    }
    // AKCE: ÚPRAVA [cite: 297-298]
    elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $editedInterest = trim($_POST["edited_interest"] ?? '');

        if (empty($editedInterest)) {
            $_SESSION['message'] = "Pole nesmí být prázdné.";
            $_SESSION['messageType'] = "error";
        } else {
            $check = $db->prepare("SELECT COUNT(*) FROM interests WHERE LOWER(name) = LOWER(?) AND id != ?");
            $check->execute([$editedInterest, $id]);
            
            if ($check->fetchColumn() > 0) {
                $_SESSION['message'] = "Tento zájem už existuje.";
                $_SESSION['messageType'] = "error";
            } else {
                $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?"); // [cite: 299-302]
                $stmt->execute([$editedInterest, $id]);
                $_SESSION['message'] = "Zájem byl upraven."; // [cite: 311]
                $_SESSION['messageType'] = "success";
            }
        }
    }

    // PRG Pattern: Přesměrování [cite: 303-307]
    header("Location: index.php");
    exit;
}

// Načtení hlášek ze session [cite: 308-315]
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';
unset($_SESSION['message'], $_SESSION['messageType']);

$editId = $_GET['edit'] ?? -1;

// 1. Zobrazení zájmů - Načtení z databáze [cite: 282-283]
$stmt = $db->query("SELECT * FROM interests");
$interestsDb = $stmt->fetchAll(PDO::FETCH_ASSOC); // [cite: 321]
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Osobní IT profil - SQLite</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <header>
        <h1><?php echo htmlspecialchars($data['name']); ?></h1>
        <p><?php echo htmlspecialchars($data['jobTitle'] ?? ''); ?></p>
    </header>

    <section>
        <h2>Dovednosti</h2>
        <ul>
            <?php foreach ($data['skills'] as $skill): ?>
                <li><?php echo htmlspecialchars($skill); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>Zájmy (z databáze)</h2>
        
        <?php if (!empty($message)): ?>
            <p class="<?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="new_interest" required placeholder="Napiš nový zájem...">
            <button type="submit" class="btn-primary">Přidat zájem</button>
        </form>

        <ul>
            <?php foreach ($interestsDb as $interest): ?>
                <li class="interest-item">
                    <?php if ($editId == $interest['id']): ?>
                        <form method="POST" class="edit-form">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $interest['id']; ?>">
                            <input type="text" name="edited_interest" value="<?php echo htmlspecialchars($interest['name']); ?>" required>
                            <button type="submit" class="btn-small btn-edit">Uložit</button>
                            <a href="index.php" class="btn-small btn-cancel">Zrušit</a>
                        </form>
                    <?php else: ?>
                        <span><?php echo htmlspecialchars($interest['name']); ?></span>
                        <div class="interest-actions">
                            <a href="index.php?edit=<?php echo $interest['id']; ?>" class="btn-small btn-edit">Upravit</a>
                            
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $interest['id']; ?>">
                                <button type="submit" class="btn-small btn-delete">Smazat</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

</body>
</html>