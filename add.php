<?php
require_once('init.php');

$group = [];
$user_add_file = 0;
if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка MySQL: " . $error);
    die();
}
else {
    $group_sql = "SELECT projects.id, projects.name, COUNT(tasks.id) AS tasks FROM projects 
LEFT JOIN tasks on projects.id = tasks.project_id 
WHERE projects.user_id = 1 GROUP BY projects.id ORDER BY projects.name ASC";
    $group_result = mysqli_query($con, $group_sql);
    if (!$group_result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        die();
    } else {
        $group = mysqli_fetch_all($group_result, MYSQLI_ASSOC);
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

    $required = ['name', 'project',];

    $errors = [];

    if (empty($form['name'])) {
        $errors['name'] = 'Заполните название задачи';
    } elseif ($form['name'] >=5 && $form['name'] <= 64) {
        $errors['name'] = 'Название задачи должно быть длинной от 5 до 64 символов';
    }

    if (empty($form['project'])) {
        $errors['project'] = 'Выберите проект к которому относится задача';
    } else {
        $selected_project = intval($form['project']);
        $project_check_sql = 'SELECT id FROM projects WHERE id = ' . $selected_project;

        $project_check_result = mysqli_query($con, $project_check_sql);
        if (!$group_result) {
            $error = mysqli_error($con);
            print("Ошибка MySQL: " . $error);
            die();
        }

        $project_check = mysqli_num_rows($group_result);
        if (!$project_check) {
            $errors['project'] = 'Выберите проект из списка';
        }
    }

    if (!empty($form['date'])) {
        if (!is_date_valid($form['date'])) {
            $errors['date'] = 'Введите дату в формате ГГГГ-ММ-ДД';
        } elseif (strtotime($form['date']) < strtotime('today')) {
            $errors['date'] = 'Дата выполнения не может быть раньше сегодняшней';
        }
    }

    if ($_FILES['file']['tmp_name']) {
        $file_size = $_FILES['file']['size'];
        if ($file_size > 2000000) {
            $errors['file'] = 'Файл должна весить не больше 2Мб.';
        }

        $file_info = new SplFileInfo($_FILES['file']['name']);
        $extension = $file_info->getExtension();
        $new_file_name = uniqid() . '.' . $extension;

        $user_add_file = 1;
    }

    if (count($errors)) {
        $page_content = include_template('add-task.php', [
            'group' => $group,
            'errors' => $errors
        ]);
    } else {

        if ($user_add_file) {
            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $new_file_name);
        }


        header('Location: /');
        exit();
    }

} else {
    $page_content = include_template('add-task.php', [
        'group' => $group,
    ]);
}

$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
]);

print($layout_content);
