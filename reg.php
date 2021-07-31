<?php
require_once('init.php');

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка MySQL: " . $error);
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;

    $required = ['email', 'password', 'name',];

    $errors = [];

    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }
    }

    foreach ($form as $key => $value) {
        if ($key == 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$key] = 'Email должен быть корректным';
            }
        } elseif ($key == 'password') {
            if (strlen($value) < 6 || strlen($value) > 32) {
                $errors[$key] = 'Введите пароль длинной от 6 до 32 символов';
            }
        } elseif ($key == 'name') {
            if (strlen($value) < 3 || strlen($value) > 32) {
                $errors[$key] = 'Введите имя длинной от 3 до 32 символов';
            }
        }
    }



    if (empty($errors)) {
        $email = mysqli_real_escape_string($con, $form['email']);
        $email_check_sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($con, $email_check_sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        } else {
            $password = password_hash($form['password'], PASSWORD_DEFAULT);
            $add_user_sql = "INSERT INTO `users` SET"
                . " `name` = '" . mysqli_real_escape_string($con, $form['name']) . "'"
                . ", `email` = '" . mysqli_real_escape_string($con, $form['email']) . "'"
                . ", `password` = '" . $password . "'";

            $add_user = mysqli_query($con, $add_user_sql);

            if ($add_user && empty($errors)) {
                header("Location: /");
                exit();
            }
        }
    }
    $page_content = include_template('registration.php', [
        'errors' => $errors,
        'form_data' => $form,
    ]);

} else {
    $page_content = include_template('registration.php', []);
}

$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
]);

print($layout_content);
