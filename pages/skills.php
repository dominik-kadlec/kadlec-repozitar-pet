<?php
$jsonFile = 'profile.json';
$data = json_decode(file_get_contents($jsonFile), true);
?>
<section>
    <h2>Dovednosti</h2>
    <ul class="skills-list">
        <?php foreach ($data['skills'] as $skill): ?>
            <li><?php echo htmlspecialchars($skill); ?></li>
        <?php endforeach; ?>
    </ul>
</section>