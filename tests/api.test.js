import request from 'supertest'
import { app } from '../src/app.js'
import { pool } from '../src/db.js'

describe('ATIERA Financial System API', () => {
  let authToken

  beforeAll(async () => {
    // Setup test database connection
    // You might want to use a separate test database
  })

  afterAll(async () => {
    await pool.end()
  })

  describe('Health Check', () => {
    it('should return health status', async () => {
      const response = await request(app)
        .get('/api/health')
        .expect(200)

      expect(response.body).toHaveProperty('status', 'ok')
      expect(response.body).toHaveProperty('timestamp')
      expect(response.body).toHaveProperty('uptime')
    })
  })

  describe('Authentication', () => {
    it('should login with valid credentials', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({
          username: 'admin',
          password: 'admin123'
        })
        .expect(200)

      expect(response.body).toHaveProperty('token')
      authToken = response.body.token
    })

    it('should reject invalid credentials', async () => {
      const response = await request(app)
        .post('/api/auth/login')
        .send({
          username: 'admin',
          password: 'wrongpassword'
        })
        .expect(401)

      expect(response.body).toHaveProperty('error')
    })
  })

  describe('General Ledger', () => {
    it('should retrieve accounts list', async () => {
      const response = await request(app)
        .get('/api/gl/accounts')
        .set('Authorization', `Bearer ${authToken}`)
        .expect(200)

      expect(Array.isArray(response.body)).toBe(true)
    })

    it('should create new account', async () => {
      const newAccount = {
        code: '1020',
        name: 'Test Account',
        type: 'ASSET'
      }

      const response = await request(app)
        .post('/api/gl/accounts')
        .set('Authorization', `Bearer ${authToken}`)
        .send(newAccount)
        .expect(201)

      expect(response.body).toHaveProperty('id')
      expect(response.body.code).toBe(newAccount.code)
    })
  })

  describe('Accounts Receivable', () => {
    it('should retrieve customers list', async () => {
      const response = await request(app)
        .get('/api/ar/customers')
        .set('Authorization', `Bearer ${authToken}`)
        .expect(200)

      expect(Array.isArray(response.body)).toBe(true)
    })

    it('should create new customer', async () => {
      const newCustomer = {
        name: 'Test Customer',
        email: 'test@example.com',
        phone: '+1234567890'
      }

      const response = await request(app)
        .post('/api/ar/customers')
        .set('Authorization', `Bearer ${authToken}`)
        .send(newCustomer)
        .expect(201)

      expect(response.body).toHaveProperty('id')
      expect(response.body.name).toBe(newCustomer.name)
    })
  })

  describe('Rate Limiting', () => {
    it('should enforce rate limits', async () => {
      // Make multiple requests to trigger rate limiting
      const requests = Array(101).fill().map(() => 
        request(app).get('/api/health')
      )

      const responses = await Promise.all(requests)
      const rateLimited = responses.some(r => r.status === 429)
      
      expect(rateLimited).toBe(true)
    })
  })
})
