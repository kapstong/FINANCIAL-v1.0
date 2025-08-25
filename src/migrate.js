import 'dotenv/config'
import { runMigrations } from './db.js'

async function runMigration() {
  console.log('🔄 Running ATIERA Financial System Migrations...')
  
  try {
    await runMigrations()
    console.log('✅ Database migrations completed successfully!')
    console.log('📊 All tables and seed data are now available')
    
  } catch (error) {
    console.error('❌ Migration failed:', error.message)
    console.log('\n🔍 Troubleshooting tips:')
    console.log('1. Ensure database connection is working')
    console.log('2. Check if .env file has correct database settings')
    console.log('3. Verify MySQL user has CREATE/ALTER privileges')
    process.exit(1)
  }
}

// Run migration if this file is executed directly
if (import.meta.url === `file://${process.argv[1]}`) {
  runMigration()
}

export default runMigration
