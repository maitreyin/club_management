import { useEffect, useState } from 'react';

const API = '/api';

function App() {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [clubs, setClubs] = useState([]);
  const [events, setEvents] = useState([]);
  const [notifications, setNotifications] = useState([]);
  const [members, setMembers] = useState([]);
  const [message, setMessage] = useState('');
  const [activeView, setActiveView] = useState('dashboard');
  const [authForm, setAuthForm] = useState({ email: '', password: '', role: 'student' });
  const [adminForm, setAdminForm] = useState({ clubName: '', description: '', faculty: '', date: '' });
  const [notifyForm, setNotifyForm] = useState({ clubId: '', message: '' });
  const [selectedEvent, setSelectedEvent] = useState(null);
  const [eventWinners, setEventWinners] = useState([]);

  async function fetchDashboard() {
    try {
      const [meRes, clubsRes, eventsRes, notificationsRes] = await Promise.all([
        fetch(`${API}/auth/me`, { credentials: 'include' }),
        fetch(`${API}/clubs`),
        fetch(`${API}/events`),
        fetch(`${API}/notifications`),
      ]);

      const meData = await meRes.json();
      const clubsData = await clubsRes.json();
      const eventsData = await eventsRes.json();
      const notificationsData = await notificationsRes.json();

      setUser(meData.user);
      setClubs(clubsData.clubs || []);
      setEvents(eventsData.events || []);
      setNotifications(notificationsData.notifications || []);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  async function loadAdminMembers() {
    if (!user || user.role !== 'admin') return;
    const res = await fetch(`${API}/admin/members`, { credentials: 'include' });
    const data = await res.json();
    setMembers(data.members || []);
  }

  useEffect(() => {
    fetchDashboard();
  }, []);

  useEffect(() => {
    if (user?.role === 'admin') {
      loadAdminMembers();
    }
  }, [user]);

  async function handleLogin(e) {
    e.preventDefault();
    const res = await fetch(`${API}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(authForm),
    });
    const data = await res.json();
    if (!res.ok) {
      setMessage(data.error || 'Login failed');
      return;
    }
    setUser(data.user);
    setMessage('Welcome back!');
    await fetchDashboard();
  }

  async function handleLogout() {
    await fetch(`${API}/auth/logout`, { method: 'POST', credentials: 'include' });
    setUser(null);
    setMessage('You have been logged out.');
  }

  async function handleJoinClub(clubId) {
    const res = await fetch(`${API}/clubs/join`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ clubId }),
    });
    const data = await res.json();
    setMessage(data.message || 'Action completed');
  }

  async function handleRegisterEvent(eventId) {
    const res = await fetch(`${API}/events/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ eventId }),
    });
    const data = await res.json();
    setMessage(data.message || 'Registration updated');
  }

  async function handleViewEvent(eventId) {
    const res = await fetch(`${API}/events/${eventId}`);
    const data = await res.json();
    setSelectedEvent(data.event);
    setEventWinners(data.winners || []);
    setActiveView('event');
  }

  async function handleCreateClub(e) {
    e.preventDefault();
    const res = await fetch(`${API}/admin/clubs`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        clubName: adminForm.clubName,
        description: adminForm.description,
        faculty: adminForm.faculty,
        date: adminForm.date,
      }),
    });
    const data = await res.json();
    setMessage(data.message || 'Club created');
    await fetchDashboard();
  }

  async function handleSendNotification(e) {
    e.preventDefault();
    const res = await fetch(`${API}/admin/notifications`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ clubId: notifyForm.clubId, message: notifyForm.message }),
    });
    const data = await res.json();
    setMessage(data.message || 'Notification sent');
    await fetchDashboard();
  }

  if (loading) return <div className="app-shell"><div className="card">Loading dashboard...</div></div>;

  if (!user) {
    return (
      <div className="app-shell auth-shell">
        <div className="card auth-card">
          <div className="brand-block">
            <p className="eyebrow">Modern campus portal</p>
            <h1>Club Management Platform</h1>
            <p>Manage clubs, events, memberships, and communications from one polished workspace.</p>
          </div>
          <form onSubmit={handleLogin} className="form-stack">
            <label>
              Role
              <select value={authForm.role} onChange={(e) => setAuthForm({ ...authForm, role: e.target.value })}>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
              </select>
            </label>
            <label>
              Email
              <input type="email" value={authForm.email} onChange={(e) => setAuthForm({ ...authForm, email: e.target.value })} required />
            </label>
            <label>
              Password
              <input type="password" value={authForm.password} onChange={(e) => setAuthForm({ ...authForm, password: e.target.value })} required />
            </label>
            <button type="submit">Sign In</button>
          </form>
          {message ? <p className="message">{message}</p> : null}
        </div>
      </div>
    );
  }

  return (
    <div className="app-shell">
      <header className="topbar">
        <div>
          <p className="eyebrow">Connected campus experience</p>
          <h2>{user.role === 'admin' ? 'Admin Control Center' : 'Student Dashboard'}</h2>
        </div>
        <div className="topbar-actions">
          <button className="ghost" onClick={() => setActiveView('dashboard')}>Overview</button>
          {user.role === 'admin' ? (
            <>
              <button className="ghost" onClick={() => setActiveView('clubs')}>Clubs</button>
              <button className="ghost" onClick={() => setActiveView('members')}>Members</button>
            </>
          ) : (
            <>
              <button className="ghost" onClick={() => setActiveView('clubs')}>Clubs</button>
              <button className="ghost" onClick={() => setActiveView('events')}>Events</button>
            </>
          )}
          <button onClick={handleLogout}>Logout</button>
        </div>
      </header>

      {message ? <p className="message">{message}</p> : null}

      {activeView === 'event' && selectedEvent ? (
        <section className="card section-card">
          <h3>{selectedEvent.title}</h3>
          <p><strong>Club:</strong> {selectedEvent.club_name}</p>
          <p><strong>Venue:</strong> {selectedEvent.venue}</p>
          <p><strong>Date:</strong> {selectedEvent.event_date}</p>
          <p><strong>Status:</strong> {selectedEvent.status}</p>
          <p>{selectedEvent.description}</p>
          <h4>Winners</h4>
          {eventWinners.length === 0 ? <p>No winners announced yet.</p> : (
            <ul>
              {eventWinners.map((winner, index) => (
                <li key={index}>{winner.name} — {winner.position} — {winner.prize_name}</li>
              ))}
            </ul>
          )}
        </section>
      ) : null}

      {activeView === 'dashboard' ? (
        <div className="dashboard-grid">
          <section className="card">
            <h3>Clubs</h3>
            <p>{clubs.length} active clubs</p>
            <div className="chip-row">
              {clubs.slice(0, 4).map((club) => <span className="chip" key={club.club_id}>{club.club_name}</span>)}
            </div>
          </section>
          <section className="card">
            <h3>Events</h3>
            <p>{events.length} upcoming events</p>
            <div className="chip-row">
              {events.slice(0, 4).map((event) => <span className="chip" key={event.event_id}>{event.title}</span>)}
            </div>
          </section>
          <section className="card">
            <h3>Announcements</h3>
            {notifications.slice(0, 4).map((notification) => (
              <div key={notification.notif_id} className="list-item">
                <strong>{notification.club_name}</strong>
                <p>{notification.message}</p>
              </div>
            ))}
          </section>
        </div>
      ) : null}

      {activeView === 'clubs' ? (
        <section className="card section-card">
          <h3>{user.role === 'admin' ? 'Club Portfolio' : 'Explore Clubs'}</h3>
          <div className="card-grid">
            {clubs.map((club) => (
              <div key={club.club_id} className="tile">
                <h4>{club.club_name}</h4>
                <p>{club.description}</p>
                <p><strong>Faculty:</strong> {club.faculty_incharge}</p>
                {user.role !== 'admin' ? <button onClick={() => handleJoinClub(club.club_id)}>Join Club</button> : null}
              </div>
            ))}
          </div>
        </section>
      ) : null}

      {activeView === 'events' && user.role !== 'admin' ? (
        <section className="card section-card">
          <h3>Event Schedule</h3>
          <div className="card-grid">
            {events.map((event) => (
              <div key={event.event_id} className="tile">
                <h4>{event.title}</h4>
                <p>{event.club_name}</p>
                <p>{event.event_date}</p>
                <p>{event.venue}</p>
                <div className="button-row">
                  <button onClick={() => handleViewEvent(event.event_id)}>View</button>
                  <button onClick={() => handleRegisterEvent(event.event_id)}>{event.winner_count > 0 ? 'Completed' : 'Register'}</button>
                </div>
              </div>
            ))}
          </div>
        </section>
      ) : null}

      {activeView === 'members' && user.role === 'admin' ? (
        <section className="card section-card">
          <h3>Members Overview</h3>
          <div className="card-grid">
            {members.map((member, index) => (
              <div key={index} className="tile">
                <h4>{member.name}</h4>
                <p>{member.club_name}</p>
                <p>{member.role}</p>
              </div>
            ))}
          </div>
        </section>
      ) : null}

      {user.role === 'admin' ? (
        <div className="dashboard-grid admin-grid">
          <section className="card">
            <h3>Create New Club</h3>
            <form onSubmit={handleCreateClub} className="form-stack">
              <input placeholder="Club name" value={adminForm.clubName} onChange={(e) => setAdminForm({ ...adminForm, clubName: e.target.value })} required />
              <textarea placeholder="Description" value={adminForm.description} onChange={(e) => setAdminForm({ ...adminForm, description: e.target.value })} required />
              <input placeholder="Faculty incharge" value={adminForm.faculty} onChange={(e) => setAdminForm({ ...adminForm, faculty: e.target.value })} required />
              <input type="date" value={adminForm.date} onChange={(e) => setAdminForm({ ...adminForm, date: e.target.value })} required />
              <button type="submit">Create Club</button>
            </form>
          </section>
          <section className="card">
            <h3>Send Announcement</h3>
            <form onSubmit={handleSendNotification} className="form-stack">
              <select value={notifyForm.clubId} onChange={(e) => setNotifyForm({ ...notifyForm, clubId: e.target.value })} required>
                <option value="">Select club</option>
                {clubs.map((club) => <option key={club.club_id} value={club.club_id}>{club.club_name}</option>)}
              </select>
              <textarea placeholder="Notification text" value={notifyForm.message} onChange={(e) => setNotifyForm({ ...notifyForm, message: e.target.value })} required />
              <button type="submit">Send</button>
            </form>
          </section>
        </div>
      ) : null}
    </div>
  );
}

export default App;
