INSERT INTO registration(student_id,event_id,reg_date,status)
SELECT s.student_id, e.event_id,'2025-07-01','Registered'
FROM student s,event e WHERE s.roll_no='24CS01' AND e.title='Hackathon 2025';

INSERT INTO registration(student_id,event_id,reg_date,status)
SELECT s.student_id, e.event_id,'2025-07-02','Registered'
FROM student s,event e WHERE s.roll_no='24EC02' AND e.title='Hackathon 2025';

INSERT INTO registration(student_id,event_id,reg_date,status)
SELECT s.student_id, e.event_id,'2025-07-03','Registered'
FROM student s,event e WHERE s.roll_no='23ME03' AND e.title='Robo Race';

INSERT INTO registration(student_id,event_id,reg_date,status)
SELECT s.student_id, e.event_id,'2025-07-04','Attended'
FROM student s,event e WHERE s.roll_no='22IT04' AND e.title='PhotoWalk';

INSERT INTO registration(student_id,event_id,reg_date,status)
SELECT s.student_id, e.event_id,'2025-07-05','Registered'
FROM student s,event e WHERE s.roll_no='24EE05' AND e.title='AI Workshop';

INSERT INTO notification(club_id,event_id,message,type,date)
SELECT c.club_id, e.event_id, 'Hackathon registrations are now open!', 'Event', '2025-07-01'
FROM club c,event e WHERE c.club_name='Coding Club' AND e.title='Hackathon 2025';

INSERT INTO notification(club_id,event_id,message,type,date)
SELECT c.club_id,e.evet_id,'Volunteer recruitment started for Robo Race','Volunteer', '2025-07-02'
FROM club c,event e WHERE c.club_name='Robotics Club' AND e.title='Robo Race';

INSERT INTO notification(club_id,event_id,message,type,date)
SELECT c.club_id,e.event_id,'PhotoWalk results published','Result','2025-07-03'
FROM club c,event e WHERE c.club_name='Photography Club' AND e.title='PhotoWalk';

INSERT INTO notification(club_id,event_id,message,type,date)
SELECT c.club_id,e.event_id,'Dance Fiesta auditions next week','Announcement','2025-07-04'
FROM club c,event e WHERE c.club_name='Dance Club'AND e.title='Dance Fiesta';

INSERT INTO notification(club_id,event_id,message,type,date)
SELECT c.club_id,e.event_id,'AI Workshop seats filling fast','Event','2025-07-05'
FROM club c,event e WHERE c.club_name='AI Club'AND e.title='AI Workshop';

INSERT INTO volunteer(student_id,event_id,work_role,od_status,status)
SELECT s.student_id, e.event_id,'Coordinator','Approved','Accepted'
FROM student s,event e WHERE s.roll_no='24CS01'AND e.title='Hackathon 2025';

INSERT INTO volunteer(student_id,event_id,work_role,od_status,status)
SELECT s.student_id,e.event_id,'Stage Management','Pending','Under Review'
FROM student s,event e WHERE s.roll_no='24EC02' AND e.title='Robo Race';

INSERT INTO volunteer(student_id,event_id,work_role,od_status,status)
SELECT s.student_id,e.event_id,'Photography','Approved','Accepted'
FROM student s,event e WHERE s.roll_no='23ME03'AND e.title='PhotoWalk';

INSERT INTO volunteer (student_id,event_id,work_role,od_status,status)
SELECT s.student_id,e.event_id,'Backstage','Approved','Accepted'
FROM student s,event e WHERE s.roll_no='22IT04' AND e.title='Dance Fiesta';

INSERT INTO volunteer(student_id,event_id,work_role,od_status,status)
SELECT s.student_id,e.event_id,'Technical Support','Pending','Under Review'
FROM student s,event e WHERE s.roll_no='24EE05'AND e.title='AI Workshop';

INSERT INTO prize(event_id,prize_name,prize_value)
SELECT e.event_id,'1st Prize','₹10000'
FROM event e WHERE e.title='Hackathon 2025';

INSERT INTO prize(event_id,prize_name,prize_value)
SELECT e.event_id,'2nd Prize','₹5000'
FROM event e WHERE e.title='Hackathon 2025';

INSERT INTO prize(event_id,prize_name,prize_value)
SELECT e.event_id,'Winner Trophy','₹7000'
FROM event e WHERE e.title='Robo Race';

INSERT INTO prize(event_id,prize_name,prize_value)
SELECT e.event_id,'Best Photo','₹3000'
FROM event e WHERE e.title='PhotoWalk';

INSERT INTO prize (event_id,prize_name,prize_value)
SELECT e.event_id, 'Top Performer','₹8000'
FROM event e WHERE e.title='AI Workshop';

INSERT INTO winner(student_id,event_id,prize_id,position)
SELECT s.student_id, e.event_id,p.prize_id,'1st Place'
FROM student s,event e,prize p WHERE s.roll_no='24CS01'AND e.title='Hackathon 2025'AND p.prize_name='1st Prize';

INSERT INTO winner(student_id,event_id,prize_id,position)
SELECT s.student_id, e.event_id, p.prize_id,'2nd Place'
FROM student s,event e,prize p WHERE s.roll_no='24EC02' AND e.title='Hackathon 2025' AND p.prize_name='2nd Prize';

INSERT INTO winner (student_id,event_id,prize_id,position)
SELECT s.student_id, e.event_id, p.prize_id,'Winner'
FROM student s,event e,prize p WHERE s.roll_no='23ME03' AND e.title='Robo Race' AND p.prize_name='Winner Trophy';

INSERT INTO winner(student_id,event_id,prize_id,position)
SELECT s.student_id,e.event_id,p.prize_id,'Best Photographer'
FROM student s,event e,prize p WHERE s.roll_no='22IT04'AND e.title='PhotoWalk'AND p.prize_name='Best Photo';

INSERT INTO winner(student_id,event_id,prize_id,position)
SELECT s.student_id,e.event_id,p.prize_id,'Top Performer'
FROM student s,event e,prize p WHERE s.roll_no='24EE05' AND e.title='AI Workshop' AND p.prize_name='Top Performer';
