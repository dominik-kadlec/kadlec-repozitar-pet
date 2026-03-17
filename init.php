<?php
// Připojení (nebo vytvoření) SQLite databáze
$db = new PDO("sqlite:profile.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vytvoření tabulky podle zadání
$query = "CREATE TABLE IF NOT EXISTS interests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
)";

$db->exec($query);
echo "Databáze profile.db a tabulka interests byly úspěšně vytvořeny!";
?>