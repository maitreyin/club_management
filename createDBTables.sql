CREATE DATABASE club_management_system;
USE club_management_system;
CREATE TABLE student (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    roll_no VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    department VARCHAR(50),
    year INT,
    password VARCHAR(100) NOT NULL
);
CREATE TABLE club (
    club_id INT PRIMARY KEY AUTO_INCREMENT,
    club_name VARCHAR(100) NOT NULL,
    description TEXT,
    faculty_incharge VARCHAR(100),
    established_date DATE
);
CREATE TABLE membership (
    membership_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    club_id INT,
    role VARCHAR(50),
    join_date DATE,

    FOREIGN KEY (student_id)
    REFERENCES student(student_id),

    FOREIGN KEY (club_id)
    REFERENCES club(club_id)
);
CREATE TABLE event (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    club_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    venue VARCHAR(100),
    event_date DATE,
    status VARCHAR(30),

    FOREIGN KEY (club_id)
    REFERENCES club(club_id)
);


CREATE TABLE registration (
    reg_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    event_id INT,
    reg_date DATE,
    status VARCHAR(30),
    FOREIGN KEY (student_id)
        REFERENCES student (student_id),
    FOREIGN KEY (event_id)
        REFERENCES event (event_id)
);
CREATE TABLE notification (
    notif_id INT PRIMARY KEY AUTO_INCREMENT,
    club_id INT,
    event_id INT,
    message TEXT,
    type VARCHAR(30),
    date DATE,

    FOREIGN KEY (club_id)
    REFERENCES club(club_id),

    FOREIGN KEY (event_id)
    REFERENCES event(event_id)
);

CREATE TABLE volunteer (
    vol_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    event_id INT,
    work_role VARCHAR(100),
    od_status VARCHAR(20),
    status VARCHAR(30),

    FOREIGN KEY (student_id)
    REFERENCES student(student_id),

    FOREIGN KEY (event_id)
    REFERENCES event(event_id)
);
CREATE TABLE prize (
    prize_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT,
    prize_name VARCHAR(100),
    prize_value VARCHAR(100),

    FOREIGN KEY (event_id)
    REFERENCES event(event_id)
);

CREATE TABLE winner (
    win_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    event_id INT,
    prize_id INT,
    position VARCHAR(30),

    FOREIGN KEY (student_id)
    REFERENCES student(student_id),

    FOREIGN KEY (event_id)
    REFERENCES event(event_id),

    FOREIGN KEY (prize_id)
    REFERENCES prize(prize_id)
);



