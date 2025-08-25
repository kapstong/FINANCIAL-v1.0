import 'dotenv/config'

// Set test environment
process.env.NODE_ENV = 'test'

// Mock console methods to reduce noise during tests
global.console = {
  ...console,
  log: jest.fn(),
  debug: jest.fn(),
  info: jest.fn(),
  warn: jest.fn(),
  error: jest.fn(),
}

// Global test timeout
jest.setTimeout(10000)

// Setup test database configuration
process.env.DB_NAME = 'atiera_test'
process.env.JWT_SECRET = 'test-secret-key'

// Clean up function for after each test
afterEach(() => {
  jest.clearAllMocks()
})
