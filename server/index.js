import express from 'express';
import cors from 'cors';
import session from 'express-session';
import cookieParser from 'cookie-parser';
import path from 'path';
import { fileURLToPath } from 'url';
import bcrypt from 'bcryptjs';
import { query, testDb } from './db.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const app = express();
const port = process.env.PORT || 5000;

app.use(cors({ origin: true, credentials: true }));
app.use(cookieParser());
app.use(express.json());
app.use(
  session({
    secret: process.env.SESSION_SECRET || 'club-management-secret',
    resave: false,
    saveUninitialized: false,
    cookie: { httpOnly: true, sameSite: 'lax' },
  })
);

function requireAuth(req, res, next) {
  if (!req.session.user) {
    return res.status(401).json({ error: 'Authentication required' });
  }
  next();
}

function requireAdmin(req, res, next) {
  if (!req.session.user || req.session.user.role !== 'admin') {
    return res.status(403).json({ error: 'Admin access required' });
  }
  next();
}

async function comparePassword(input, stored) {
  if (!stored) return false;
  if (stored.startsWith('$2')) {
    return bcrypt.compare(input, stored);
  }
  return input === stored;
}

app.get('/api/health', (_req, res) => res.json({ status: 'ok' }));

app.post('/api/auth/login', async (req, res) => {
  try {
    const { email, password, role } = req.body;

    if (role === 'admin') {
      if (email === 'admin@gmail.com' && password === 'admin123') {
        req.session.user = { id: 0, name: 'Administrator', role: 'admin' };
        return res.json({ user: req.session.user });
      }
      return res.status(401).json({ error: 'Invalid admin credentials' });
    }

    const rows = await query('SELECT * FROM student WHERE email = ? LIMIT 1', [email]);
    const user = rows[0];

    if (!user) {
      return res.status(401).json({ error: 'User not found' });
    }

    const valid = await comparePassword(password, user.password);
    if (!valid) {
      return res.status(401).json({ error: 'Wrong password' });
    }

    req.session.user = { id: user.student_id, name: user.name, role: 'student' };
    res.json({ user: req.session.user });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Login failed' });
  }
});

app.post('/api/auth/logout', (req, res) => {
  req.session.destroy(() => res.json({ success: true }));
});

app.get('/api/auth/me', (req, res) => {
  res.json({ user: req.session.user || null });
});

app.get('/api/clubs', async (_req, res) => {
  try {
    const clubs = await query('SELECT club_id, club_name, description, faculty_incharge, established_date FROM club ORDER BY club_name');
    res.json({ clubs });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to load clubs' });
  }
});

app.post('/api/clubs/join', requireAuth, async (req, res) => {
  try {
    const { clubId } = req.body;
    const existing = await query('SELECT * FROM membership WHERE student_id = ? AND club_id = ?', [req.session.user.id, clubId]);
    if (existing.length > 0) {
      return res.json({ message: 'You already joined this club.' });
    }

    await query('INSERT INTO membership (student_id, club_id, role, join_date) VALUES (?, ?, ?, CURDATE())', [req.session.user.id, clubId, 'General Member']);
    res.json({ message: 'You joined the club successfully.' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to join club' });
  }
});

app.get('/api/events', async (_req, res) => {
  try {
    const events = await query(`
      SELECT e.event_id, e.title, e.description, e.venue, e.event_date, e.status, c.club_name,
             (SELECT COUNT(*) FROM winner w WHERE w.event_id = e.event_id) AS winner_count
      FROM event e
      JOIN club c ON e.club_id = c.club_id
      ORDER BY e.event_date DESC
    `);
    res.json({ events });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to load events' });
  }
});

app.get('/api/events/:eventId', async (req, res) => {
  try {
    const event = await query(
      `SELECT e.event_id, e.title, e.description, e.venue, e.event_date, e.status, c.club_name
       FROM event e JOIN club c ON e.club_id = c.club_id WHERE e.event_id = ? LIMIT 1`,
      [req.params.eventId]
    );
    const winners = await query(
      `SELECT s.name, w.position, p.prize_name
       FROM winner w
       JOIN student s ON w.student_id = s.student_id
       JOIN prize p ON w.prize_id = p.prize_id
       WHERE w.event_id = ?`,
      [req.params.eventId]
    );
    res.json({ event: event[0] || null, winners });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to load event details' });
  }
});

app.post('/api/events/register', requireAuth, async (req, res) => {
  try {
    const { eventId } = req.body;
    const winners = await query('SELECT * FROM winner WHERE event_id = ?', [eventId]);
    if (winners.length > 0) {
      return res.json({ message: 'This event is already completed.' });
    }

    const existing = await query('SELECT * FROM registration WHERE student_id = ? AND event_id = ?', [req.session.user.id, eventId]);
    if (existing.length > 0) {
      return res.json({ message: 'You have already registered for this event.' });
    }

    await query('INSERT INTO registration (student_id, event_id, reg_date, status) VALUES (?, ?, CURDATE(), ?)', [req.session.user.id, eventId, 'Registered']);
    res.json({ message: 'Registered successfully.' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to register for event' });
  }
});

app.get('/api/notifications', async (_req, res) => {
  try {
    const notifications = await query(`
      SELECT n.notif_id, n.message, n.date, c.club_name
      FROM notification n
      JOIN club c ON n.club_id = c.club_id
      ORDER BY n.date DESC, n.notif_id DESC
    `);
    res.json({ notifications });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to load notifications' });
  }
});

app.get('/api/admin/members', requireAdmin, async (_req, res) => {
  try {
    const members = await query(`
      SELECT s.name, c.club_name, m.role
      FROM membership m
      JOIN student s ON m.student_id = s.student_id
      JOIN club c ON m.club_id = c.club_id
      ORDER BY c.club_name, s.name
    `);
    res.json({ members });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to load members' });
  }
});

app.post('/api/admin/clubs', requireAdmin, async (req, res) => {
  try {
    const { clubName, description, faculty, date } = req.body;
    await query('INSERT INTO club (club_name, description, faculty_incharge, established_date) VALUES (?, ?, ?, ?)', [clubName, description, faculty, date]);
    res.json({ message: 'Club created successfully.' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to create club' });
  }
});

app.post('/api/admin/notifications', requireAdmin, async (req, res) => {
  try {
    const { clubId, message } = req.body;
    await query('INSERT INTO notification (club_id, event_id, message, type, date) VALUES (?, NULL, ?, ?, CURDATE())', [clubId, message, 'Announcement']);
    res.json({ message: 'Notification sent successfully.' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Unable to send notification' });
  }
});

const distPath = path.join(__dirname, '../dist');
if (process.env.NODE_ENV === 'production') {
  app.use(express.static(distPath));
  app.get('*', (_req, res) => res.sendFile(path.join(distPath, 'index.html')));
}

app.listen(port, async () => {
  try {
    await testDb();
    console.log(`Server running on http://localhost:${port}`);
  } catch (error) {
    console.error('Database connection failed:', error.message);
    console.log(`Server running on http://localhost:${port} (database unavailable)`);
  }
});
