<?php
// 1. Inicializace proměnných pro hlášky dle nápovědy ze zadání
$message = ""; [cite: 150]
$messageType = ""; [cite: 151]
$jsonFile = 'profile.json';

// 2. Načtení dat ze souboru JSON
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

// 3. Zpracování POST požadavku z formuláře
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new_interest"])) { [cite: 118, 120]
    // Očištění vstupu
    $newInterest = trim($_POST["new_interest"]); [cite: 121]

    // Kontrola, zda pole není prázdné
    if (empty($newInterest)) { [cite: 122]
        $message = "Pole nesmí být prázdné."; [cite: 146]
        $messageType = "error"; [cite: 157]
    } else {
        // Zabráníme duplicitě bez ohledu na velikost písmen (Web == web == WEB)
        // Převedeme všechny existující zájmy na malá písmena
        $existingInterestsLower = array_map('strtolower', $data['interests']); [cite: 128]
        
        // Zkontrolujeme, zda zadaný zájem (převedený na malá písmena) už existuje
        if (in_array(strtolower($newInterest), $existingInterestsLower)) { [cite: 123, 127]
            $message = "Tento zájem už existuje."; [cite: 145, 156]
            $messageType = "error"; [cite: 157]
        } else {
            // Přidání nového zájmu a uložení
            $data['interests'][] = $newInterest; [cite: 124]
            file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); [cite: 125, 129, 130]
            
            $message = "Zájem byl úspěšně přidán."; [cite: 142, 153]
            $messageType = "success"; [cite: 154]
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
        <p><?php echo htmlspecialchars($data['jobTitle']); ?></p>
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
        
        [cite_start]<?php if (!empty($message)): ?> [cite: 159]
            [cite_start]<p class="<?php echo $messageType; ?>"> [cite: 160]
                <?php echo htmlspecialchars($message); [cite_start]?> [cite: 161]
            [cite_start]</p> [cite: 162]
        <?php endif; ?>

        [cite_start]<form method="POST"> [cite: 114]
            [cite_start]<input type="text" name="new_interest" required placeholder="Napiš nový zájem..."> [cite: 115]
            [cite_start]<button type="submit">Přidat zájem</button> [cite: 116]
        [cite_start]</form> [cite: 117]

        <ul>
            <?php foreach ($data['interests'] as $interest): ?>
                <li><?php echo htmlspecialchars($interest); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

</body>
</html>