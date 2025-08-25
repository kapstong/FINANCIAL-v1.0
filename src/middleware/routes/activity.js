import { Router } from 'express'
import { all, row } from '../../db.js'

const router = Router()

// Get activity logs with optional filtering
router.get('/logs', async (req, res) => {
  try {
    const { module, actor_id, date_from, date_to, limit = 100 } = req.query
    
    let sql = `
      SELECT al.*, u.username as actor_name
      FROM activity_log al
      LEFT JOIN users u ON al.actor_id = u.id
      WHERE 1=1
    `
    const params = []
    
    if (module) {
      sql += ' AND al.module = ?'
      params.push(module)
    }
    
    if (actor_id) {
      sql += ' AND al.actor_id = ?'
      params.push(actor_id)
    }
    
    if (date_from) {
      sql += ' AND DATE(al.created_at) >= ?'
      params.push(date_from)
    }
    
    if (date_to) {
      sql += ' AND DATE(al.created_at) <= ?'
      params.push(date_to)
    }
    
    sql += ' ORDER BY al.created_at DESC LIMIT ?'
    params.push(parseInt(limit))
    
    const logs = await all(sql, params)
    res.json(logs)
    
  } catch (error) {
    console.error('Error fetching activity logs:', error)
    res.status(500).json({ error: 'Failed to fetch activity logs' })
  }
})

// Get activity log by ID
router.get('/logs/:id', async (req, res) => {
  try {
    const { id } = req.params
    const log = await row('SELECT * FROM activity_log WHERE id = ?', [id])
    
    if (!log) {
      return res.status(404).json({ error: 'Activity log not found' })
    }
    
    res.json(log)
    
  } catch (error) {
    console.error('Error fetching activity log:', error)
    res.status(500).json({ error: 'Failed to fetch activity log' })
  }
})

// Create new activity log entry
router.post('/logs', async (req, res) => {
  try {
    const { actor_id, module, action, ref_table, ref_id, details, ip } = req.body
    
    if (!module || !action) {
      return res.status(400).json({ error: 'Module and action are required' })
    }
    
    const sql = `
      INSERT INTO activity_log (actor_id, module, action, ref_table, ref_id, details, ip, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    `
    
    const result = await row(sql, [actor_id, module, action, ref_table, ref_id, details, ip])
    
    res.status(201).json({ 
      id: result.insertId,
      message: 'Activity log created successfully' 
    })
    
  } catch (error) {
    console.error('Error creating activity log:', error)
    res.status(500).json({ error: 'Failed to create activity log' })
  }
})

// Get activity summary by module
router.get('/summary', async (req, res) => {
  try {
    const { period = '30' } = req.query // days
    
    const sql = `
      SELECT 
        module,
        COUNT(*) as count,
        MAX(created_at) as last_activity
      FROM activity_log 
      WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
      GROUP BY module
      ORDER BY count DESC
    `
    
    const summary = await all(sql, [period])
    res.json(summary)
    
  } catch (error) {
    console.error('Error fetching activity summary:', error)
    res.status(500).json({ error: 'Failed to fetch activity summary' })
  }
})

export default router
