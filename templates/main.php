<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($group as $value): ?>
                <li class="main-navigation__list-item <?=($value['is_current'] == 1) ? 'main-navigation__list-item--active' : '' ?>">
                    <a class="main-navigation__list-item-link" href="<?=$value['link'] ?>"><?=htmlspecialchars($value['name']) ?></a>
                    <span class="main-navigation__list-item-count">
                        <?=$value['tasks'] ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
       href="/add-project.php" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?=htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item <?=empty($_GET['time-filter']) ? 'tasks-switch__item--active' : '' ?>">Все задачи</a>
            <a href="/?time-filter=today" class="tasks-switch__item <?=!empty($_GET['time-filter']) && $_GET['time-filter'] == 'today' ? 'tasks-switch__item--active' : '' ?>">Повестка дня</a>
            <a href="/?time-filter=tomorrow" class="tasks-switch__item <?=!empty($_GET['time-filter']) && $_GET['time-filter'] == 'tomorrow' ? 'tasks-switch__item--active' : '' ?>">Завтра</a>
            <a href="/?time-filter=expired" class="tasks-switch__item <?=!empty($_GET['time-filter']) && $_GET['time-filter'] == 'expired' ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>
        </nav>

        <label class="checkbox">
            <input class="checkbox__input visually-hidden show_completed"
                   type="checkbox" <?= ($show_complete_tasks === 1) ? "checked" : ""; ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <?php if (count($tasks) == 0): ?>
        <h2 class="content__header-text">Задач не найдено</h2>
    <?php else: ?>
        <table class="tasks">
            <?php foreach ($tasks as $task): ?>
                <?php if ($task['status'] && $show_complete_tasks === 0) {
                    continue;
                }
                ?>
                <tr class="tasks__item task <?=($task['status']) ? 'task--completed' : '' ?> <?=(isImportantTask($task['do_date'])) ? 'task--important' : '' ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?=$task['id'] ?>"
                                <?= ($task['status']) ? 'checked' : '' ?>>
                            <span class="checkbox__text"><?=htmlspecialchars($task['name']) ?></span>
                        </label>
                    </td>

                    <td class="task__file">
                        <?php if (isset($task['file_link'])): ?>
                            <a class="download-link" href="/uploads/<?=$task['file_link'] ?>"><?=$task['file_name'] ?></a>
                        <?php endif; ?>
                    </td>

                    <td class="task__date"><?=$task['do_date'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>
