<?php
$jsonFile = 'profile.json';
$data = json_decode(file_get_contents($jsonFile), true);
?>
<header style="background: white; color: #333; box-shadow: none; padding: 20px 0;">
    <h1><?php echo htmlspecialchars($data['name']); ?></h1>
    <p class="subtitle"><?php echo htmlspecialchars($data['jobTitle'] ?? ''); ?></p>
</header>