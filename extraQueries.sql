USE club_management_system;
-- STUDENTS WITH CLUBS
SELECT student.name,club.club_name,membership.role
FROM membership INNER JOIN student
ON membership.student_id = student.student_id
INNER JOIN club
ON membership.club_id = club.club_id;

-- EVENTS WITH CLUB DETAILS
SELECT event.title, club.club_name,event.venue,
event.event_date
FROM event 
INNER JOIN club
ON event.club_id = club.club_id;

-- EVENT REGISTRATIONS
SELECT student.name, event.title,
registration.status
FROM registration
INNER JOIN student
ON registration.student_id = student.student_id
INNER JOIN event ON registration.event_id = event.event_id;



-- VOLUNTEERS DETAILS
SELECT
student.name,
event.title,
volunteer.work_role,
volunteer.status
FROM volunteer

INNER JOIN student
ON volunteer.student_id = student.student_id

INNER JOIN event
ON volunteer.event_id = event.event_id;



-- WINNERS DETAILS
SELECT student.name, event.title,
prize.prize_name,
winner.position
FROM winner INNER JOIN student
ON winner.student_id = student.student_id
INNER JOIN event
ON winner.event_id = event.event_id 
INNER JOIN prize
ON winner.prize_id = prize.prize_id;



-- =========================================
-- CLAUSES
-- =========================================

-- WHERE CLAUSE
SELECT *FROM event
WHERE status = 'Open';

-- ORDER BY CLAUSE
SELECT *FROM student
ORDER BY name ASC;

-- GROUP BY CLAUSE
SELECT club_id,
COUNT(*) AS total_members
FROM membership
GROUP BY club_id;

-- HAVING CLAUSE
SELECT club_id,
COUNT(*) AS total_members
FROM membership
GROUP BY club_id
HAVING COUNT(*) >= 1;

-- DISTINCT CLAUSE
SELECT DISTINCT department
FROM student;

-- LIKE CLAUSE
SELECT * FROM club
WHERE club_name LIKE '%Club%';

-- BETWEEN CLAUSE
SELECT *FROM event
WHERE event_date
BETWEEN '2025-08-01' AND '2025-10-30';

-- IN CLAUSE
SELECT *FROM student
WHERE department IN ('CSE','ECE');

-- =========================================
-- AGGREGATE FUNCTIONS
-- =========================================

-- COUNT
SELECT COUNT(*) AS total_students
FROM student;



-- SUM
SELECT SUM(year) AS total_years
FROM student;



-- AVG
SELECT AVG(year) AS average_year
FROM student;

-- MAX
SELECT MAX(year) AS highest_year
FROM student;

-- MIN
SELECT MIN(year) AS lowest_year
FROM student;