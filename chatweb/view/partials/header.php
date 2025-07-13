<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mini Chat Web</title>
    <link rel="stylesheet" href="/AvengersAssemble/chatweb/public/css/style.css">
</head>

<body>

<?php if (isset($_SESSION['user']) && $_SESSION['user']['fkRole'] == 2): ?>
    <nav class="admin-nav">
        <a href="index.php?action=adminPanel">Panneau Admin</a>
    </nav>
<?php endif; ?>