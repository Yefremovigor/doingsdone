<?php
require_once('init.php');

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка MySQL: " . $error);
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

} else {
    $page_content = include_template('registration.php', [

    ]);
}

$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
]);

print($layout_content);
