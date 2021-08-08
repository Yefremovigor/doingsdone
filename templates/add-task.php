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

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form" action="add.php" method="post" autocomplete="off" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?=(isset($errors['name'])) ? 'form__input--error' : '' ?>" type="text" name="name" id="name" value="<?=(isset($form_data['name'])) ? htmlspecialchars($form_data['name']) : '' ?>" placeholder="Введите название">
            <p class="form__message">
                <?=(isset($errors['name'])) ? $errors['name'] : '' ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date <?=(isset($errors['date'])) ? 'form__input--error' : '' ?>" type="text" name="date" id="date" value="" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <p class="form__message">
                <?=(isset($errors['date'])) ? $errors['date'] : '' ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?=(isset($errors['project'])) ? 'form__input--error' : '' ?>" name="project" id="project">
                <?php foreach ($group as $value): ?>
                    <option value="<?=$value['id'] ?>"><?=$value['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <p class="form__message">
                <?=(isset($errors['project'])) ? $errors['project'] : '' ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="file">Файл</label>

            <div class="form__input-file <?=(isset($errors['file'])) ? 'form__input--error' : '' ?>">
                <input class="visually-hidden" type="file" name="file" id="file" value="">

                <label class="button button--transparent" for="file">
                    <span>Выберите файл</span>
                </label>
                <p class="form__message">
                    <?=(isset($errors['file'])) ? $errors['file'] : '' ?>
                </p>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
