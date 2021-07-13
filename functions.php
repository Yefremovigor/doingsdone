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
