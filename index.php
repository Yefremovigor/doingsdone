<?php
require_once('init.php');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $group = [];
    $tasks = [];
    if (!$con) {
        $error = mysqli_connect_error();
        print("Ошибка MySQL: " . $error);
        die();
    } elseif (isset($_GET['task_id']) and isset($_GET['check'])) {
        $task_id = intval($_GET['task_id']);
        $check = intval($_GET['check']);

        $get_task_info_sql = "SELECT `user_id` FROM tasks WHERE `id` = $task_id";
        $get_task_info_result = mysqli_query($con, $get_task_info_sql);
        if (!$get_task_info_result) {
            $error = mysqli_error($con);
            print("Ошибка MySQL: " . $error);
            die();
        }
        $get_task_info = mysqli_fetch_all($get_task_info_result, MYSQLI_ASSOC);
        $task_owner = $get_task_info[0]['user_id'];
        if ($task_owner == $user['id']) {
            $new_task_status_sql = "UPDATE `tasks` SET `status` = $check WHERE `id` = $task_id";
            $new_task_status = mysqli_query($con, $new_task_status_sql);
            header('Location: /');
            exit();
        }

    } else {
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

        $time_filter = '';

        if (isset($_GET['time-filter']) && $_GET['time-filter'] !== '') {
            switch ($_GET['time-filter']) {
                case 'today':
                    $time_filter = " AND DATE(do_date) = CURDATE()";
                    break;
                case 'tomorrow':
                    $time_filter = ' AND DATE(do_date) = CURDATE() + 1';
                    break;
                case 'expired':
                    $time_filter = ' AND DATE(do_date) < CURDATE()';
                    break;
                default:
                    break;
            }
        }

        if (empty($_GET['search'])) {
            $tasks_sql = "SELECT id, name, project_id, status, DATE_FORMAT(do_date, '%d.%m.%Y') AS do_date, file_name, file_link FROM tasks  WHERE user_id = $user[id] $current_project $time_filter";
        } else {
            $search_tasks = mysqli_real_escape_string($con, $_GET['search']);
            $tasks_sql = "SELECT id, name, project_id, status, DATE_FORMAT(do_date, '%d.%m.%Y') AS do_date, file_name, file_link FROM tasks  WHERE user_id = $user[id] AND MATCH(name) AGAINST('$search_tasks' IN BOOLEAN MODE)";
        }


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
        'user_name' => $user['name'],
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
