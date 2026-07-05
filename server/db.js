import mysql from 'mysql2/promise';
import dotenv from 'dotenv';

dotenv.config();

const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'club_management_system',
  connectionLimit: 10,
  waitForConnections: true,
  queueLimit: 0,
});

export async function testDb() {
  const connection = await pool.getConnection();
  await connection.query('SELECT 1');
  connection.release();
}

export async function query(sql, params = []) {
  const [rows] = await pool.execute(sql, params);
  return rows;
}

export default pool;
