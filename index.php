<?php
require_once('init.php');

if (isset($_SESSION[user])) {
    $user = $_SESSION[user];
    $group = [];
    $tasks = [];
    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка MySQL: " . $error);
        die();
    }
    else {
        $group_sql = "SELECT projects.id, projects.name, COUNT(tasks.id) AS tasks FROM projects 
    LEFT JOIN tasks on projects.id = tasks.project_id 
    WHERE projects.user_id = $user[id] GROUP BY projects.id ORDER BY tasks DESC";
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

        $tasks_sql = "SELECT name, project_id, status, DATE_FORMAT(do_date, '%d.%m.%Y') AS do_date, file_name, file_link FROM tasks  WHERE user_id = $user[id] $current_project;";
        $tasks_result = mysqli_query($con, $tasks_sql);
        if (!$tasks_result) {
            $error = mysqli_error($con);
            print("Ошибка MySQL: " . $error);
            die();
        } else {
            $tasks = mysqli_fetch_all($tasks_result, MYSQLI_ASSOC);
        }
    }

    $show_complete_tasks = 0;
    if (isset($_GET['show_completed'])) {
       if ($_GET['show_completed'] == 1) {
           $show_complete_tasks = 1;
       }
    }

    $script_name = pathinfo( __FILE__, PATHINFO_BASENAME);

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
        $group[$key]['link'] = "/" . $script_name . "?" . $query;
    }

    /*if (count($tasks) == 0) {
        http_response_code(404);
        print('Задача не найдена <br> <a href="/">Вернуться на главную</a>');
        die();
    }*/

    $page_content = include_template('main.php', [
        'group' => $group,
        'show_complete_tasks' => $show_complete_tasks,
        'tasks' => $tasks,
    ]);


    $layout_content = include_template('layout.php', [
        'title' => 'Дела в порядке',
        'content' => $page_content,
        'user_name' => $user[name],
        'sidebar' => 1,
    ]);
} else {
    $page_content = include_template('guest.php', [
    ]);

    $layout_content = include_template('layout.php', [
        'title' => 'Дела в порядке',
        'content' => $page_content,
    ]);
}

print($layout_content);
