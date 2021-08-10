<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($group as $value): ?>
                <li class="main-navigation__list-item <?= ($value['is_current'] == 1) ? 'main-navigation__list-item--active' : '' ?>">
                    <a class="main-navigation__list-item-link"
                       href="<?= $value['link'] ?>"><?= htmlspecialchars($value['name']) ?></a>
                    <span class="main-navigation__list-item-count">
                        <?= $value['tasks'] ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form" action="/add-project.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?= (isset($errors['name'])) ? 'form__input--error' : '' ?>" type="text"
                   name="name" id="project_name"
                   value="<?= (isset($form_data['name'])) ? htmlspecialchars($form_data['name']) : '' ?>"
                   placeholder="Введите название проекта">
            <p class="form__message">
                <?= (isset($errors['name'])) ? $errors['name'] : '' ?>
            </p>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
