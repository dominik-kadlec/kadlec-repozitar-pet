<?php
// Zpracování POST požadavků pro CRUD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $newInterest = trim($_POST["new_interest"] ?? '');
        if (empty($newInterest)) {
            $_SESSION['message'] = "Pole nesmí být prázdné.";
            $_SESSION['messageType'] = "error";
        } else {
            $check = $db->prepare("SELECT COUNT(*) FROM interests WHERE LOWER(name) = LOWER(?)");
            $check->execute([$newInterest]);
            
            if ($check->fetchColumn() > 0) {
                $_SESSION['message'] = "Tento zájem už existuje.";
                $_SESSION['messageType'] = "error";
            } else {
                $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
                $stmt->execute([$newInterest]);
                $_SESSION['message'] = "Zájem byl přidán.";
                $_SESSION['messageType'] = "success";
            }
        }
    } 
    elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Zájem byl odstraněn.";
        $_SESSION['messageType'] = "success";
    }
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
                $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
                $stmt->execute([$editedInterest, $id]);
                $_SESSION['message'] = "Zájem byl upraven.";
                $_SESSION['messageType'] = "success";
            }
        }
    }

    // PRG Pattern: Přesměrování zpět na stránku se zájmy
    header("Location: index.php?page=interests");
    exit;
}

// Načtení hlášek ze session
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';
unset($_SESSION['message'], $_SESSION['messageType']);

$editId = $_GET['edit'] ?? -1;

// Zobrazení zájmů - Načtení z databáze
$stmt = $db->query("SELECT * FROM interests");
$interestsDb = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
    <h2>Správa zájmů</h2>
    
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
                        <a href="?page=interests" class="btn-small btn-cancel">Zrušit</a>
                    </form>
                <?php else: ?>
                    <span><?php echo htmlspecialchars($interest['name']); ?></span>
                    <div class="interest-actions">
                        <a href="?page=interests&edit=<?php echo $interest['id']; ?>" class="btn-small btn-edit">Upravit</a>
                        
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