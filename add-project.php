<?php
require_once('init.php');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    http_response_code(403);
    header('Location: /auth.php');
    exit();
}

$group = [];
$user_add_file = 0;
if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка MySQL: " . $error);
    exit();
} else {
    $group_sql = "SELECT projects.id, projects.name, COUNT(tasks.id) AS tasks FROM projects 
LEFT JOIN tasks on projects.id = tasks.project_id 
WHERE projects.user_id = " . $user['id'] . " GROUP BY projects.id ORDER BY projects.name ASC";
    $group_result = mysqli_query($con, $group_sql);
    if (!$group_result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        exit();
    } else {
        $group = mysqli_fetch_all($group_result, MYSQLI_ASSOC);
    }

    $current_project = '';
    if (isset($_GET['project'])) {
        $current_project = 'AND `project_id` = ' . intval($_GET['project']);
    }
}

$scriptName = pathinfo(__FILE__, PATHINFO_BASENAME);

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

    $required = ['name',];
    $errors = [];

    if (empty($form['name'])) {
        $errors['name'] = 'Заполните название проекта';
    } elseif ($form['name'] >= 5 && $form['name'] <= 64) {
        $errors['name'] = 'Название проекта должно быть длинной от 5 до 64 символов';
    }

    if (empty($errors)) {
        $name = mysqli_real_escape_string($con, $form['name']);
        $name_check_sql = "SELECT `id` FROM `projects` WHERE `user_id` = " . $user['id'] . " AND `name` = '$name'";

        $res = mysqli_query($con, $name_check_sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['name'] = 'Такой проект уже есть';
        } else {
            $add_project_sql = "INSERT INTO `projects` SET"
                . " `name` = '" . mysqli_real_escape_string($con, $form['name']) . "'"
                . ", `user_id` = '" . $user['id'] . "'";

            $add_project = mysqli_query($con, $add_project_sql);

            if ($add_project && empty($errors)) {
                header("Location: /");
                exit();
            }
        }
    }
    $page_content = include_template('add-project.php', [
        'group' => $group,
        'errors' => $errors,
        'form_data' => $form,
    ]);

} else {
    $page_content = include_template('add-project.php', [
        'group' => $group,
    ]);
}

$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
    'user_name' => $user['name'],
    'sidebar' => 1,
]);

print($layout_content);
