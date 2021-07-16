<?php
require_once('init.php');
// require_once('data.php');

$group = [];
$tasks = [];
if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка MySQL: " . $error);
    die();
}
else {
    $groupSQL = "SELECT projects.name, COUNT(tasks.id) AS tasks FROM projects 
LEFT JOIN tasks on projects.id = tasks.project_id 
WHERE projects.user_id = 1 GROUP BY projects.name ORDER BY tasks DESC";
    $groupResult = mysqli_query($con, $groupSQL);
    if (!$groupResult) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        die();
    } else {
        $group = mysqli_fetch_all($groupResult, MYSQLI_ASSOC);
    }

    $tasksSQL = "SELECT name, project_id, status, DATE_FORMAT(do_date, '%d.%m.%Y') AS do_date, file FROM tasks  WHERE user_id = 1;";
    $tasksResult = mysqli_query($con, $tasksSQL);
    if (!$tasksResult) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        die();
    } else {
        $tasks = mysqli_fetch_all($tasksResult, MYSQLI_ASSOC);
    }
}

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
