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
    $groupSQL = "SELECT projects.id, projects.name, COUNT(tasks.id) AS tasks FROM projects 
LEFT JOIN tasks on projects.id = tasks.project_id 
WHERE projects.user_id = 1 GROUP BY projects.id ORDER BY tasks DESC";
    $groupResult = mysqli_query($con, $groupSQL);
    if (!$groupResult) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        die();
    } else {
        $group = mysqli_fetch_all($groupResult, MYSQLI_ASSOC);
    }

    $currentProject = '';
    if (isset($_GET['project'])) {
        $currentProject = 'AND `project_id` = ' . intval($_GET['project']);
    }

    $tasksSQL = "SELECT name, project_id, status, DATE_FORMAT(do_date, '%d.%m.%Y') AS do_date, file FROM tasks  WHERE user_id = 1 $currentProject;";
    $tasksResult = mysqli_query($con, $tasksSQL);
    if (!$tasksResult) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        die();
    } else {
        $tasks = mysqli_fetch_all($tasksResult, MYSQLI_ASSOC);
    }
}

$show_complete_tasks = 0;
if (isset($_GET['show_completed'])) {
   if ($_GET['show_completed'] == 1) {
       $show_complete_tasks = 1;
   }
}

$scriptName = pathinfo( __FILE__, PATHINFO_BASENAME);

foreach ($group as $key => $value) {
    $params = [];
    $params['project'] = $value['id'];
    if (isset($_GET['show_complete_tasks'])) {
        if ($_GET['show_complete_tasks'] == 1) {
            $params['show_complete_tasks'] = 1;
        }
    }

    $group[$key]['is_current'] = 0;
    if (isset($_GET['project'])) {
        if ($_GET['project'] == $value['id']) {
            $params['show_complete_tasks'] = 1;
            $group[$key]['is_current'] = 1;
        }
    }

    $query = http_build_query($params);
    $group[$key]['link'] = "/" . $scriptName . "?" . $query;
}

if (count($tasks) > 0) {
    $page_content = include_template('main.php', [
        'group' => $group,
        'show_complete_tasks' => $show_complete_tasks,
        'tasks' => $tasks,
    ]);
} else {
    http_response_code(404);
    print('Задача не найдена <br> <a href="/">Вернуться на главную</a>');
    die();
}


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
