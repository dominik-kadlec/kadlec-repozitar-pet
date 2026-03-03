<?php
$message = "";
$messageType = "";
$jsonFile = 'profile.json';

$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new_interest"])) {
    $newInterest = trim($_POST["new_interest"]);

    if (empty($newInterest)) {
        $message = "Pole nesmí být prázdné.";
        $messageType = "error";
    } else {
        $existingInterestsLower = array_map('strtolower', $data['interests']);
        
        if (in_array(strtolower($newInterest), $existingInterestsLower)) {
            $message = "Tento zájem už existuje.";
            $messageType = "error";
        } else {
            $data['interests'][] = $newInterest;
            file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            $message = "Zájem byl úspěšně přidán.";
            $messageType = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Osobní IT profil - PHP</title>
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
            <p class="<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="new_interest" required placeholder="Napiš nový zájem...">
            <button type="submit">Přidat zájem</button>
        </form>

        <ul>
            <?php foreach ($data['interests'] as $interest): ?>
                <li><?php echo htmlspecialchars($interest); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

</body>
</html>