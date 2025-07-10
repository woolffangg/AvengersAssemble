<?php require __DIR__ . '/partials/header.php'; ?>

<div class="salons-page">
  <div class="salons-card">
    <h2>Salons</h2>

    <a href="index.php?action=createSalon" class="button">+ Créer un nouveau salon</a>

    <ul class="salon-list">
      <?php foreach ($salons as $salon): ?>
      <li class="salon-item">
        <a href="index.php?action=chat&id=<?= $salon['pkS'] ?>">
          <?= htmlspecialchars($salon['nom']) ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>

    <a href="index.php?action=logout" class="logout-link">Déconnexion</a>
  </div>
</div>