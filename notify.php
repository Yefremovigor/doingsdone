<?php
require_once('init.php');

$transport = new Swift_SmtpTransport("smtp.mailtrap.io", 2525);
$transport->setUsername("f6c3f1c65e65af");
$transport->setPassword("///");

$mailer = new Swift_Mailer($transport);

$sql = "SELECT u.name, u.email, t.name AS task_name, DATE_FORMAT(t.do_date, '%d.%m.%Y') AS do_date FROM tasks t"
    . " JOIN users u"
    . " ON t.user_id = u.id"
    . " WHERE t.do_date = CURDATE() AND status = 0";

$res = mysqli_query($con, $sql);

if ($res && mysqli_num_rows($res)) {
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $letters = [];
    foreach ($tasks as $task) {
        if (empty($letters[$task['email']])) {
            $letter = [
                'email' => $task['email'],
                'name' => $task['name'],
                'user_tasks' => []
            ];
            $letters[$task['email']] = $letter;
        }
        $user_task = [
            'task_name' => $task['task_name'],
            'do_date' => $task['do_date'],
        ];
        array_push($letters[$task['email']]['user_tasks'], $user_task);
    }

    foreach ($letters as $letter) {
        $name = $letter['name'];
        $planed = "У вас запланирована задача ";
        if (count($letter['user_tasks']) > 1) {
            $planed = "У вас запланированы задачи:" . PHP_EOL;
            foreach ($letter['user_tasks'] as $task) {
                $planed .= '"' . $task['task_name'] . '" на ' . $task['do_date'] . PHP_EOL;
            }
        } else {
            $planed .= '"' . $letter['user_tasks'][0]['task_name'] . '" на ' . $letter['user_tasks'][0]['do_date'];
        }

        $text = "Уважаемый, $name. $planed";

        $message = (new Swift_Message('Ваши дела на сегодня'))
            ->setFrom(['keks@phpdemo.ru' => 'Дела в порядке'])
            ->setTo([$letter['email']])
            ->setBody($text, 'text/plain');

        $mailer->send($message);
    }

}
