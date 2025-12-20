<?php
require_once __DIR__ . '/../config/config.php';
session_destroy();
header('Location: /agrichain_php_tailwind/index.php');
