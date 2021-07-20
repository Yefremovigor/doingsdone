INSERT INTO users (email, name, password)
VALUES
       ('yefremovigor@yandex.ru', 'Игорь', 'password'),
       ('test@testmail.ru', 'Константин', 'password');

INSERT INTO projects (user_id, name)
VALUES
       (1, 'Входящие'),
       (1, 'Учеба'),
       (1, 'Работа'),
       (1, 'Домашние дела'),
       (1, 'Авто');

INSERT INTO tasks (name, user_id, project_id, status, do_date, file_name, file_link)
VALUES
       ('Собеседование в IT компании', 1, 3, 0, '2021.07.17', null, null),
       ('Выполнить тестовое задание', 1, 3, 0, '2021.08.25', null, null),
       ('Сделать задание первого раздела', 1, 2, 1, '2021.08.21', null, null),
       ('Встреча с другом', 1, 1, 0, '2021.07.20', null, null),
       ('Купить корм для кота', 1, 4, 0, null, null, null),
       ('Заказать пиццу', 1, 4, 0, null, null, null);

# получить список из всех проектов для одного пользователя
SELECT * FROM projects  WHERE user_id = 1;
# получить список из всех задач для одного проекта
SELECT name, project_id, status, DATE_FORMAT(do_date, '%d.%m.%Y') AS do_date, file FROM tasks  WHERE user_id = 1;
# пометить задачу как выполненную
UPDATE tasks SET status = 1  WHERE id = 3;
# обновить название задачи по её идентификатору
