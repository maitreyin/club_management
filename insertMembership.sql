INSERT INTO membership(student_id, club_id, role, join_date)
SELECT s.student_id, c.club_id, 'President','2024-01-10'
FROM student s, club c WHERE s.roll_no='24CS01' AND c.club_name='Coding Club';

INSERT INTO membership(student_id, club_id, role, join_date)
SELECT s.student_id, c.club_id, 'Member', '2024-01-12'
FROM student s, club c WHERE s.roll_no='24EC02' AND c.club_name='Coding Club';

INSERT INTO membership (student_id, club_id, role, join_date)
SELECT s.student_id, c.club_id, 'Vice President', '2024-02-01'
FROM student s, club c WHERE s.roll_no='23ME03' AND c.club_name='Robotics Club';

INSERT INTO membership (student_id, club_id, role, join_date)
SELECT s.student_id, c.club_id, 'Member','2024-02-10'
FROM student s, club c WHERE s.roll_no='22IT04' AND c.club_name='Photography Club';

INSERT INTO membership(student_id, club_id, role, join_date)
SELECT s.student_id, c.club_id,'Core Member','2024-03-05'
FROM student s, club c WHERE s.roll_no='24EE05' AND c.club_name='AI Club';
