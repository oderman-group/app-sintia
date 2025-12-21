<?php
include("session.php");

header('Content-Type: application/json');

$progress = 0;
if (isset($_SESSION['import_progress'])) {
    $progress = $_SESSION['import_progress'];
}

echo json_encode(['progress' => $progress]);
