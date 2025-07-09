<?php require __DIR__ . '/partials/header.php'; ?>
<h2>Salons</h2>
<a href="index.php?action=createSalon" style="display:inline-block;margin-bottom:10px;">+ Créer un nouveau salon</a>
<ul>
<?php foreach ($salons as $salon): ?>
    <li><a href="index.php?action=chat&id=<?= $salon['pkS'] ?>"><?= htmlspecialchars($salon['nom']) ?></a></li>
<?php endforeach; ?>
</ul>
<a href="index.php?action=logout">Déconnexion</a>
