<?php

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function upload_image($file) {
    $folder = __DIR__ . '/../public/assets/uploads/';

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $name = $file['name'];
    $tmp  = $file['tmp_name'];
    $size = $file['size'];

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        return false;
    }

    if ($size > 2 * 1024 * 1024) {
        return false;
    }

    $newName = time() . "_" . rand(1000,9999) . "." . $ext;
    if (move_uploaded_file($tmp, $folder . $newName)) {
        return "assets/uploads/" . $newName;
    }
    return false;
}
?>