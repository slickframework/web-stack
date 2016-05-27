<?php

if (file_exists($_SERVER["DOCUMENT_ROOT"] . $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    $_GET['url'] = ltrim($_SERVER["REQUEST_URI"], '/');
    require __DIR__.'/index.php';
}

