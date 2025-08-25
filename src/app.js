import 'dotenv/config'
import express from 'express'
import cors from 'cors'
import helmet from 'helmet'
import rateLimit from 'express-rate-limit'
import { runMigrations } from './db.js'

import authRouter from './middleware/routes/auth.js'
import glRouter from './middleware/routes/gl.js'
import arRouter from './middleware/routes/ar.js'
import apRouter from './middleware/routes/ap.js'
import budgetRouter from './middleware/routes/budget.js'
import reportsRouter from './middleware/routes/reports.js'
import disbRouter from './middleware/routes/disbursement.js'
import collectionsRouter from './middleware/routes/collections.js'
import activityRouter from './middleware/routes/activity.js'

const app = express()

// Security middleware
app.use(helmet({
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'", "https://cdn.tailwindcss.com"],
      scriptSrc: ["'self'", "'unsafe-inline'", "https://cdn.jsdelivr.net"],
      imgSrc: ["'self'", "data:", "https:"],
      fontSrc: ["'self'", "https://fonts.gstatic.com"],
    },
  },
  crossOriginEmbedderPolicy: false
}))

// Rate limiting
const limiter = rateLimit({
  windowMs: parseInt(process.env.RATE_LIMIT_WINDOW_MS) || 15 * 60 * 1000, // 15 minutes
  max: parseInt(process.env.RATE_LIMIT_MAX_REQUESTS) || 100, // limit each IP to 100 requests per windowMs
  message: 'Too many requests from this IP, please try again later.',
  standardHeaders: true,
  legacyHeaders: false,
})
app.use('/api/', limiter)

// CORS configuration for WAMP Server
app.use(cors({
  origin: process.env.NODE_ENV === 'production' 
    ? ['https://yourdomain.com'] 
    : true, // Allow all origins in development for WAMP Server
  credentials: true
}))

// Body parsing middleware
app.use(express.json({ limit: '2mb' }))
app.use(express.urlencoded({ extended: true, limit: '2mb' }))

// Health check endpoint
app.get('/api/health', (req, res) => {
  res.json({ 
    status: 'ok', 
    timestamp: new Date().toISOString(),
    uptime: process.uptime(),
    environment: process.env.NODE_ENV || 'development'
  })
})

// API routes
app.use('/api/auth', authRouter)
app.use('/api/gl', glRouter)
app.use('/api/ar', arRouter)
app.use('/api/ap', apRouter)
app.use('/api/budget', budgetRouter)
app.use('/api/reports', reportsRouter)
app.use('/api/disbursement', disbRouter)
app.use('/api/collections', collectionsRouter)
app.use('/api/activity', activityRouter)

// Error handling middleware
app.use((err, req, res, next) => {
  console.error('Error:', err.stack)
  res.status(500).json({ 
    error: 'Something went wrong!',
    message: process.env.NODE_ENV === 'development' ? err.message : 'Internal server error'
  })
})

// 404 handler for API routes
app.use('/api/*', (req, res) => {
  res.status(404).json({ error: 'API endpoint not found' })
})

const PORT = process.env.PORT || 5050

// Initialize database and start server
async function startServer() {
  try {
    console.log('🔧 Initializing ATIERA Financial System...')
    await runMigrations()
    console.log('✅ Database initialized successfully')
    
    app.listen(PORT, () => {
      console.log('🚀 ATIERA Financial System is running!')
      console.log(`📍 Backend API: http://localhost:${PORT}`)
      console.log(`🔐 Admin Dashboard: http://localhost:${PORT}/index.php`)
      console.log(`📊 Health Check: http://localhost:${PORT}/api/health`)
      console.log(`🌍 Environment: ${process.env.NODE_ENV || 'development'}`)
      console.log(`💡 For WAMP Server: Access via your WAMP domain without localhost`)
    })
  } catch (error) {
    console.error('❌ Failed to start server:', error.message)
    process.exit(1)
  }
}

startServer()
