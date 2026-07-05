<?php

session_start();

$conn = new mysqli("localhost","root","","club_management_system");

if($conn->connect_error){
    die("Connection Failed");
}

// ======================================
// CHECK LOGIN
// ======================================

if(
!isset($_SESSION['student_id'])
&&
!isset($_SESSION['admin'])
){
    header("Location: login.php");
    exit();
}

// ======================================
// LOGOUT
// ======================================

if(isset($_GET['logout'])){

    session_destroy();

    header("Location: login.php");
    exit();
}

// ======================================
// ADMIN CHECK
// ======================================

$is_admin = isset($_SESSION['admin']);

// ======================================
// JOIN CLUB
// ======================================

$join_message = "";

if(isset($_POST['join_club']) && !$is_admin){

    $student_id = $_SESSION['student_id'];
    $club_id = $_POST['club_id'];

    $check = $conn->query("
    SELECT *
    FROM membership
    WHERE student_id='$student_id'
    AND club_id='$club_id'
    ");

    if($check->num_rows == 0){

        $conn->query("
        INSERT INTO membership
        (student_id,club_id,role,join_date)

        VALUES

        ('$student_id',
        '$club_id',
        'General Member',
        CURDATE())
        ");

        $club_result = $conn->query("
        SELECT club_name
        FROM club
        WHERE club_id='$club_id'
        ");

        $club_data = $club_result->fetch_assoc();

        $join_message = "You have successfully joined ".$club_data['club_name']." club!";
    }
    else{

        $club_result = $conn->query("
        SELECT club_name
        FROM club
        WHERE club_id='$club_id'
        ");

        $club_data = $club_result->fetch_assoc();

        $join_message = "You already joined ".$club_data['club_name']." club.";
    }
}

// ======================================
// REGISTER EVENT
// ======================================

if(isset($_POST['register_event']) && !$is_admin){

    $student_id = $_SESSION['student_id'];
    $event_id = $_POST['event_id'];

    // CHECK IF WINNERS DECLARED

    $winner_check = $conn->query("
    SELECT *
    FROM winner
    WHERE event_id='$event_id'
    ");

    if($winner_check->num_rows == 0){

        $check = $conn->query("
        SELECT *
        FROM registration
        WHERE student_id='$student_id'
        AND event_id='$event_id'
        ");

        if($check->num_rows == 0){

            $conn->query("
            INSERT INTO registration
            (student_id,event_id,reg_date,status)

            VALUES

            ('$student_id',
            '$event_id',
            CURDATE(),
            'Registered')
            ");
        }
    }

    header("Location:index.php");
    exit();
}

// ======================================
// CREATE CLUB
// ======================================

if(isset($_POST['create_club']) && $is_admin){

    $club_name = trim($_POST['club_name']);
    $description = trim($_POST['description']);
    $faculty = trim($_POST['faculty']);
    $date = $_POST['date'];

    $check = $conn->query("
    SELECT *
    FROM club
    WHERE club_name='$club_name'
    ");

    if($check->num_rows == 0){

        $conn->query("
        INSERT INTO club
        (club_name,description,
        faculty_incharge,
        established_date)

        VALUES

        ('$club_name',
        '$description',
        '$faculty',
        '$date')
        ");
    }

    header("Location:index.php");
    exit();
}

// ======================================
// SEND NOTIFICATION
// ======================================

if(isset($_POST['send_notification']) && $is_admin){

    $club_id = $_POST['club_id'];
    $message = trim($_POST['message']);

    $conn->query("
    INSERT INTO notification
    (club_id,event_id,message,type,date)

    VALUES

    ('$club_id',
    NULL,
    '$message',
    'Announcement',
    CURDATE())
    ");

    header("Location:index.php");
    exit();
}

// ======================================
// EVENT DETAILS
// ======================================

$event_details = null;

if(isset($_GET['event'])){

    $event_id = $_GET['event'];

    $sql = "

    SELECT
    event.*,
    club.club_name

    FROM event

    INNER JOIN club
    ON event.club_id = club.club_id

    WHERE event.event_id='$event_id'

    GROUP BY event.event_id

    ";

    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $event_details = $result->fetch_assoc();
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Club Management System</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Segoe UI;
}

body{
    background:#f1f5f9;
}

.navbar{
    background:#0f172a;
    padding:18px 50px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    color:white;
    font-size:28px;
    font-weight:bold;
}

.nav-links a{
    color:white;
    text-decoration:none;
    margin-left:20px;
}

.container{
    width:92%;
    margin:auto;
}

.hero{
    height:260px;
    background:linear-gradient(to right,#2563eb,#1d4ed8);
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
}

.hero h1{
    font-size:55px;
}

.section{
    margin-top:40px;
}

.section-title{
    font-size:35px;
    margin-bottom:20px;
}

.club-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

.club-card{
    background:white;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0px 0px 10px rgba(0,0,0,0.08);
}

.club-banner{
    height:140px;
    background:linear-gradient(to right,#2563eb,#1d4ed8);
}

.club-content{
    padding:20px;
}

.club-content h3{
    margin-bottom:10px;
}

.club-content p{
    margin-bottom:8px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0px 0px 10px rgba(0,0,0,0.05);
}

table th{
    background:#0f172a;
    color:white;
    padding:14px;
}

table td{
    padding:14px;
    border:1px solid #ddd;
}

button{
    padding:10px 20px;
    background:#2563eb;
    border:none;
    color:white;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

input,textarea,select{
    width:100%;
    padding:12px;
    margin-top:12px;
    border:1px solid #ccc;
    border-radius:5px;
}

form{
    background:white;
    padding:25px;
    border-radius:10px;
}

.event-box{
    background:white;
    padding:25px;
    border-radius:10px;
    margin-top:30px;
    box-shadow:0px 0px 10px rgba(0,0,0,0.08);
}

.event-box h2{
    color:#2563eb;
    margin-bottom:15px;
}

.spacer{
    height:50px;
}

.completed-btn{
    background:#16a34a;
}

.success-message{
    background:#dcfce7;
    color:#166534;
    padding:15px;
    margin-top:25px;
    border-radius:8px;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="navbar">

<div class="logo">
Club Management
</div>

<div class="nav-links">

<?php if(!$is_admin){ ?>

<a href="#clubs">Clubs</a>
<a href="#events">Events</a>
<a href="#notifications">Notifications</a>

<?php } ?>

<?php if($is_admin){ ?>

<a href="#allclubs">All Clubs</a>
<a href="#members">Members</a>
<a href="#newclub">Register Club</a>
<a href="#notify">Notifications</a>

<?php } ?>

<a href="?logout=true">
Logout
</a>

</div>

</div>

<div class="hero">

<?php if($is_admin){ ?>

<h1>Admin Dashboard</h1>

<?php } else { ?>

<h1>Student Dashboard</h1>

<?php } ?>

</div>

<div class="container">

<?php if($join_message != ""){ ?>

<div class="success-message">
<?php echo $join_message; ?>
</div>

<?php } ?>

<!-- EVENT DETAILS -->

<?php if($event_details){ ?>

<div class="event-box">

<h2>
<?php echo $event_details['title']; ?>
</h2>

<p><b>Club:</b> <?php echo $event_details['club_name']; ?></p>

<p><b>Venue:</b> <?php echo $event_details['venue']; ?></p>

<p><b>Date:</b> <?php echo $event_details['event_date']; ?></p>

<p><b>Status:</b> <?php echo $event_details['status']; ?></p>

<p><b>Description:</b> <?php echo $event_details['description']; ?></p>

<br>

<h3>Winners & Prizes</h3>

<table>

<tr>
<th>Student</th>
<th>Position</th>
<th>Prize</th>
</tr>

<?php

$sql = "

SELECT
student.name,
winner.position,
prize.prize_name

FROM winner

INNER JOIN student
ON winner.student_id = student.student_id

INNER JOIN prize
ON winner.prize_id = prize.prize_id

WHERE winner.event_id='".$event_details['event_id']."'

GROUP BY winner.win_id

";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?php echo $row['name']; ?></td>
<td><?php echo $row['position']; ?></td>
<td><?php echo $row['prize_name']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<?php } ?>

<!-- STUDENT DASHBOARD -->

<?php if(!$is_admin){ ?>

<div class="section" id="clubs">

<h2 class="section-title">
Explore Clubs
</h2>

<div class="club-grid">

<?php

$r = $conn->query("

SELECT
club.club_id,
club.club_name,
club.description,
club.faculty_incharge

FROM club

GROUP BY club.club_id

");

while($row = $r->fetch_assoc()){

?>

<div class="club-card">

<div class="club-banner"></div>

<div class="club-content">

<h3>
<?php echo $row['club_name']; ?>
</h3>

<p>
<?php echo $row['description']; ?>
</p>

<p>
<b>Faculty:</b>
<?php echo $row['faculty_incharge']; ?>
</p>

<form method="POST">

<input
type="hidden"
name="club_id"
value="<?php echo $row['club_id']; ?>">

<button name="join_club">
Join Club
</button>

</form>

</div>

</div>

<?php } ?>

</div>

</div>

<!-- EVENTS -->

<div class="section" id="events">

<h2 class="section-title">
All Events
</h2>

<table>

<tr>
<th>Event</th>
<th>Club</th>
<th>Date</th>
<th>Venue</th>
<th>Details</th>
<th>Status</th>
</tr>

<?php

$sql = "

SELECT
event.event_id,
event.title,
event.event_date,
event.venue,
club.club_name,

COUNT(winner.win_id) as winner_count

FROM event

INNER JOIN club
ON event.club_id = club.club_id

LEFT JOIN winner
ON event.event_id = winner.event_id

GROUP BY event.event_id

";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['club_name']; ?></td>

<td><?php echo $row['event_date']; ?></td>

<td><?php echo $row['venue']; ?></td>

<td>

<a href="?event=<?php echo $row['event_id']; ?>">

<button>
View
</button>

</a>

</td>

<td>

<?php if($row['winner_count'] > 0){ ?>

<button class="completed-btn">
Completed
</button>

<?php } else { ?>

<form method="POST">

<input
type="hidden"
name="event_id"
value="<?php echo $row['event_id']; ?>">

<button name="register_event">
Register
</button>

</form>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

</div>

<!-- NOTIFICATIONS -->

<div class="section" id="notifications">

<h2 class="section-title">
Notifications
</h2>

<table>

<tr>
<th>Club</th>
<th>Message</th>
<th>Date</th>
</tr>

<?php

$sql = "

SELECT
MAX(notification.notif_id) as notif_id,
club.club_name,
notification.message,
notification.date

FROM notification

INNER JOIN club
ON notification.club_id = club.club_id

GROUP BY
club.club_name,
notification.message,
notification.date

ORDER BY notification.date DESC

";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?php echo $row['club_name']; ?></td>

<td><?php echo $row['message']; ?></td>

<td><?php echo $row['date']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<?php } ?>

<!-- ADMIN DASHBOARD -->

<?php if($is_admin){ ?>

<div class="section" id="allclubs">

<h2 class="section-title">
All Clubs
</h2>

<table>

<tr>
<th>Club Name</th>
<th>Description</th>
<th>Faculty</th>
</tr>

<?php

$r = $conn->query("

SELECT
club.club_id,
club.club_name,
club.description,
club.faculty_incharge

FROM club

GROUP BY club.club_id

");

while($row = $r->fetch_assoc()){

?>

<tr>

<td><?php echo $row['club_name']; ?></td>
<td><?php echo $row['description']; ?></td>
<td><?php echo $row['faculty_incharge']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<div class="section" id="members">

<h2 class="section-title">
Club Members
</h2>

<table>

<tr>
<th>Student</th>
<th>Club</th>
<th>Role</th>
</tr>

<?php

$sql = "

SELECT
student.name,
club.club_name,
MIN(membership.role) as role

FROM membership

INNER JOIN student
ON membership.student_id = student.student_id

INNER JOIN club
ON membership.club_id = club.club_id

GROUP BY
student.student_id,
club.club_id

";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?php echo $row['name']; ?></td>
<td><?php echo $row['club_name']; ?></td>
<td><?php echo $row['role']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<div class="section" id="newclub">

<h2 class="section-title">
Register New Club
</h2>

<form method="POST">

<input
type="text"
name="club_name"
placeholder="Club Name"
required>

<textarea
name="description"
placeholder="Description"
required></textarea>

<input
type="text"
name="faculty"
placeholder="Faculty Incharge"
required>

<input
type="date"
name="date"
required>

<button name="create_club">
Create Club
</button>

</form>

</div>

<div class="section" id="notify">

<h2 class="section-title">
Send Notification
</h2>

<form method="POST">

<select name="club_id">

<?php

$r = $conn->query("

SELECT
club.club_id,
club.club_name

FROM club

GROUP BY club.club_id

");

while($row = $r->fetch_assoc()){

?>

<option value="<?php echo $row['club_id']; ?>">

<?php echo $row['club_name']; ?>

</option>

<?php } ?>

</select>

<textarea
name="message"
placeholder="Notification Message"
required></textarea>

<button name="send_notification">
Send Notification
</button>

</form>

</div>

<?php } ?>

<div class="spacer"></div>

</div>

</body>
</html>