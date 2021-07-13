<?php

function countTasksInCategory(array $tasks, string $category): int
{
    $counter = 0;
    foreach ($tasks as $task) {
        if ($task['category'] == $category) {
            $counter++;
        }
    }

    return $counter;
}

function isImportantTask($do_date): bool
{
    if (is_null($do_date)) {
        return false;
    }

    if (isset($do_date) && $do_date != '') {
        $today = time();
        $do_date = strtotime($do_date);
        return ($do_date - $today) < 60*60*24;
    }

    return false;
}
