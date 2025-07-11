<?php
// public/sse.php

// Désactive le buffering
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(1);

// Inclusion du contrôleur
require_once __DIR__ . '/../controller/MessageController.php';

// Démarrage du stream SSE
$controller = new MessageController();
$controller->streamMessages();
