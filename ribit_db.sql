
CREATE TABLE categories (
    category_id INT(11) NOT NULL AUTO_INCREMENT,
    title TEXT,
    PRIMARY KEY (category_id)
) ENGINE=InnoDB;

CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username TEXT,
    email TEXT,
    password TEXT,
    first_name TEXT,
    last_name TEXT,
    confirmed BOOLEAN,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB;

INSERT INTO users (user_id, username, email, password, first_name, last_name, confirmed)
VALUES (1, 'johndoe', 'johndoe@example.com', 'password', 'John', 'Doe', true);

CREATE TABLE habits (
    habit_id INT(11) NOT NULL AUTO_INCREMENT,
    title TEXT,
    description TEXT,
    category_id INT(11),
    user_id INT(11),
    PRIMARY KEY (habit_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

CREATE TABLE habit_logs (
    log_id INT(11) NOT NULL AUTO_INCREMENT,
    habit_id INT(11),
    log_date DATE,
    status BOOLEAN,
    PRIMARY KEY (log_id),
    FOREIGN KEY (habit_id) REFERENCES habits(habit_id)
) ENGINE=InnoDB;

CREATE TABLE reminders (
    reminder_id INT(11) NOT NULL AUTO_INCREMENT,
    message TEXT,
    PRIMARY KEY (reminder_id)
) ENGINE=InnoDB;

CREATE TABLE habitReminder (
    habitReminder_id INT(11) NOT NULL AUTO_INCREMENT,
    habit_id INT(11),
    reminder_id INT(11),
    scheduled_time DATETIME,
    PRIMARY KEY (habitReminder_id),
    FOREIGN KEY (habit_id) REFERENCES habits(habit_id),
    FOREIGN KEY (reminder_id) REFERENCES reminders(reminder_id)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    pr_id INT(11) NOT NULL AUTO_INCREMENT,
    token VARCHAR(255),
    expires_at TIME,
    user_id INT(11),
    PRIMARY KEY (pr_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;
