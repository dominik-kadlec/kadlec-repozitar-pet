<?php
// 1. Nastartování session pro PRG pattern (musí být úplně první!)
session_start();

$jsonFile = 'profile.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

// 2. Zpracování POST požadavků (přidání, úprava, mazání)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // AKCE: PŘIDÁNÍ
    if ($action === 'add') {
        $newInterest = trim($_POST["new_interest"] ?? '');
        if (empty($newInterest)) {
            $_SESSION['message'] = "Pole nesmí být prázdné.";
            $_SESSION['messageType'] = "error";
        } else {
            $existingInterestsLower = array_map('strtolower', $data['interests']);
            if (in_array(strtolower($newInterest), $existingInterestsLower)) {
                $_SESSION['message'] = "Tento zájem už existuje.";
                $_SESSION['messageType'] = "error";
            } else {
                $data['interests'][] = $newInterest;
                $_SESSION['message'] = "Zájem byl úspěšně přidán.";
                $_SESSION['messageType'] = "success";
            }
        }
    } 
    // AKCE: SMAZÁNÍ
    elseif ($action === 'delete') {
        $index = $_POST['index'] ?? -1;
        if (isset($data['interests'][$index])) {
            unset($data['interests'][$index]); // Odstraní prvek
            $data['interests'] = array_values($data['interests']); // Přečísluje pole
            $_SESSION['message'] = "Zájem byl odstraněn.";
            $_SESSION['messageType'] = "success";
        }
    }
    // AKCE: ÚPRAVA
    elseif ($action === 'edit') {
        $index = $_POST['index'] ?? -1;
        $editedInterest = trim($_POST["edited_interest"] ?? '');

        if (empty($editedInterest)) {
            $_SESSION['message'] = "Pole nesmí být prázdné.";
            $_SESSION['messageType'] = "error";
        } else {
            $existingInterestsLower = array_map('strtolower', $data['interests']);
            $foundIndex = array_search(strtolower($editedInterest), $existingInterestsLower);
            
            // Kontrola duplicity (ignorujeme, pokud jsme našli ten samý prvek, který upravujeme)
            if ($foundIndex !== false && $foundIndex != $index) {
                $_SESSION['message'] = "Tento zájem už existuje.";
                $_SESSION['messageType'] = "error";
            } else {
                $data['interests'][$index] = $editedInterest;
                $_SESSION['message'] = "Zájem byl upraven.";
                $_SESSION['messageType'] = "success";
            }
        }
    }

    // Uložení změn, pokud akce proběhla úspěšně
    if (isset($_SESSION['messageType']) && $_SESSION['messageType'] === 'success') {
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // 3. PRG Pattern: Přesměrování a ukončení skriptu
    header("Location: index.php");
    exit;
}

// 4. Načtení hlášky ze session a její smazání
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';
unset($_SESSION['message'], $_SESSION['messageType']);

// Zjištění, který zájem zrovna upravujeme (přes GET parametr)
$editIndex = $_GET['edit'] ?? -1;
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Osobní IT profil - CRUD</title>
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
        <h2>Zájmy</h2>
        
        <?php if (!empty($message)): ?>
            <p class="<?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="new_interest" required placeholder="Napiš nový zájem...">
            <button type="submit">Přidat zájem</button>
        </form>

        <ul>
            <?php foreach ($data['interests'] as $index => $interest): ?>
                <li class="interest-item">
                    <?php if ($editIndex == $index): ?>
                        <form method="POST" class="edit-form">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <input type="text" name="edited_interest" value="<?php echo htmlspecialchars($interest); ?>" required>
                            <button type="submit" class="btn-small btn-edit">Uložit</button>
                            <a href="index.php" class="btn-small btn-cancel">Zrušit</a>
                        </form>
                    <?php else: ?>
                        <span><?php echo htmlspecialchars($interest); ?></span>
                        <div class="interest-actions">
                            <a href="index.php?edit=<?php echo $index; ?>" class="btn-small btn-edit">Upravit</a>
                            
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
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