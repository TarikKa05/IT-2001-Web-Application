<?php
require __DIR__ . '/database.php';
try {
    Database::connect();
    echo "DB OK\n";
} catch (Throwable $e) {
    echo "DB FAIL: {$e->getMessage()}\n";
}
