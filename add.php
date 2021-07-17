<?php
require_once('init.php');

$group = [];
if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка MySQL: " . $error);
    die();
}
else {
    $groupSQL = "SELECT projects.id, projects.name, COUNT(tasks.id) AS tasks FROM projects 
LEFT JOIN tasks on projects.id = tasks.project_id 
WHERE projects.user_id = 1 GROUP BY projects.id ORDER BY projects.name ASC";
    $groupResult = mysqli_query($con, $groupSQL);
    if (!$groupResult) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        die();
    } else {
        $group = mysqli_fetch_all($groupResult, MYSQLI_ASSOC);
    }

    $current_project = '';
    if (isset($_GET['project'])) {
        $current_project = 'AND `project_id` = ' . intval($_GET['project']);
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;

    if ($_FILES['file']['tmp_name']) {
        $file_info = new SplFileInfo($_FILES['file']['name']);
        $extension = $file_info->getExtension();
        $new_file_name = uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $new_file_name);
    }



}


$page_content = include_template('add-task.php', [
    'group' => $group,
]);

$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
]);

print($layout_content);
