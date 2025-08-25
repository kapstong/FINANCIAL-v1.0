import 'dotenv/config'
import mysql from 'mysql2/promise'

async function setupDatabase() {
  console.log('🔧 Setting up ATIERA Financial System Database...')
  
  // Create connection without database to create it if needed
  const connection = await mysql.createConnection({
    host: process.env.DB_HOST || 'localhost',
    port: +(process.env.DB_PORT || 3306),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || ''
  })

  try {
    // Create database if it doesn't exist
    const dbName = process.env.DB_NAME || 'atiera'
    console.log(`📊 Creating database '${dbName}' if it doesn't exist...`)
    
    await connection.execute(`CREATE DATABASE IF NOT EXISTS \`${dbName}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`)
    console.log(`✅ Database '${dbName}' is ready`)
    
    // Switch to the database
    await connection.execute(`USE \`${dbName}\``)
    
    // Test connection
    const [rows] = await connection.execute('SELECT 1 as test')
    if (rows[0].test === 1) {
      console.log('✅ Database connection successful')
    }
    
    console.log('\n🎉 Database setup completed successfully!')
    console.log('📝 You can now run the application with: npm run dev')
    
  } catch (error) {
    console.error('❌ Database setup failed:', error.message)
    console.log('\n🔍 Troubleshooting tips:')
    console.log('1. Make sure WAMP Server is running')
    console.log('2. Check if MySQL service is active')
    console.log('3. Verify database credentials in .env file')
    console.log('4. Ensure MySQL user has CREATE privileges')
    process.exit(1)
  } finally {
    await connection.end()
  }
}

// Run setup if this file is executed directly
if (import.meta.url === `file://${process.argv[1]}`) {
  setupDatabase()
}

export default setupDatabase
