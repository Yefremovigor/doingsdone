CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add DATETIME DEFAULT NOW(),
    email CHAR(128) NOT NULL UNIQUE,
    name CHAR(64) NOT NULL,
    password CHAR(64) NOT NULL
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add DATETIME DEFAULT NOW(),
    status TINYINT(1) DEFAULT 0,
    name CHAR(64) NOT NULL,
    file CHAR(255),
    do_date DATE,
    user_id INT NOT NULL,
    project_id INT NOT NULL
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name CHAR(64) NOT NULL,
    user_id INT NOT NULL
);

CREATE UNIQUE INDEX users_email ON users (email);
CREATE INDEX projects_name ON projects (name);
CREATE INDEX project_creator ON projects (user_id);
CREATE INDEX task_creator ON tasks (user_id);
