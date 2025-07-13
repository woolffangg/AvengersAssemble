<?php
// public/sse.php (ancienne version simple)

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once __DIR__ . '/../controller/MessageController.php';
$controller = new MessageController();
$controller->streamMessages();
