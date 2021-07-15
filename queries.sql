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

INSERT INTO tasks (name, user_id, project_id, status, do_date, file)
VALUES
       ('Собеседование в IT компании', 1, 3, 0, '14.07.2021', null),
       ('Выполнить тестовое задание', 1, 3, 0, '25.08.2021', null),
       ('Сделать задание первого раздела', 1, 2, 0, '21.08.2021', null),
       ('Встреча с другом', 1, 1, 0, '15.07.2021', null),
       ('Купить корм для кота', 1, 4, 0, null, null),
       ('Заказать пиццу', 1, 4, 0, null, null);

# получить список из всех проектов для одного пользователя
SELECT * FROM projects  WHERE user_id = 1;
# получить список из всех задач для одного проекта
SELECT * FROM tasks  WHERE user_id = 1;
# пометить задачу как выполненную
UPDATE tasks SET status = 1  WHERE id = 3;
# обновить название задачи по её идентификатору
