<?php
require_once('config.php');
require_once('helpers.php');
require_once('functions.php');
require_once('data.php');

$show_complete_tasks = rand(0, 1);

$page_content = include_template('main.php', [
    'group' => $group,
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks,
]);

$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
]);

print($layout_content);
