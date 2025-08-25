# ATIERA Financial System

A comprehensive financial management system built with Node.js/Express backend and PHP admin interface, designed specifically for WAMP Server environments.

## 🚀 Features

- **General Ledger Management** - Chart of accounts, journal entries, double-entry bookkeeping
- **Accounts Receivable** - Customer management, invoicing, payment tracking
- **Accounts Payable** - Vendor management, bill processing, payment management
- **Budget Management** - Fiscal year budgets, period tracking, variance analysis
- **Financial Reporting** - Trial balance, income statement, balance sheet
- **Collections & Disbursements** - Payment processing and expense tracking
- **Activity Logging** - Complete audit trail for all transactions
- **Role-based Access Control** - Admin and user roles with proper permissions
- **Modern UI/UX** - Responsive design with dark/light mode support

## 🛠️ Technology Stack

### Backend
- **Node.js** with Express.js framework
- **MySQL** database with connection pooling
- **JWT** authentication with bcrypt password hashing
- **Security middleware** (Helmet, CORS, Rate limiting)

### Frontend
- **PHP** admin interface
- **Tailwind CSS** for modern styling
- **Chart.js** for financial data visualization
- **Responsive design** with mobile support

## 📋 Prerequisites

- **WAMP Server** (Windows, Apache, MySQL, PHP)
- **Node.js** 18.0.0 or higher
- **npm** 8.0.0 or higher
- **MySQL** 5.7 or higher

## 🚀 Quick Start

### 1. Clone the Repository
```bash
git clone <repository-url>
cd atiera-financial-system
```

### 2. Install Dependencies
```bash
npm install
```

### 3. Environment Configuration
```bash
# Copy environment template
copy env.example .env

# Edit .env file with your database settings
# Default settings for WAMP Server:
# DB_HOST=localhost
# DB_PORT=3306
# DB_USER=root
# DB_PASS=
# DB_NAME=atiera
```

### 4. Database Setup
```bash
# Create database and run migrations
npm run setup

# Or run migrations separately
npm run migrate
```

### 5. Start the System
```bash
# Development mode with auto-reload
npm run dev

# Production mode
npm start
```

### 6. Access the System
- **Backend API**: http://localhost:5050
- **Admin Dashboard**: http://localhost:5050/admin/dashboard.php
- **Health Check**: http://localhost:5050/api/health

## 📚 API Documentation

Comprehensive API documentation is available in [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

### Default Credentials
- **Username**: admin
- **Password**: admin123

## 🧪 Testing

```bash
# Run all tests
npm test

# Run tests with coverage
npm test -- --coverage

# Run tests in watch mode
npm test -- --watch
```

## 📁 Project Structure

```
atiera-financial-system/
├── src/                    # Node.js backend source
│   ├── middleware/        # Express middleware
│   │   └── routes/       # API route handlers
│   ├── sql/              # Database schema and seed data
│   ├── app.js            # Main application file
│   └── db.js             # Database connection and utilities
├── admin/                 # PHP admin interface
│   ├── dashboard.php     # Main admin dashboard
│   ├── *.php            # Module-specific pages
│   └── *.html           # Static HTML pages
├── config/               # Configuration files
├── includes/             # PHP includes and utilities
├── tests/                # Test files
├── uploads/              # File uploads directory
├── .env                  # Environment configuration
├── package.json          # Node.js dependencies
└── README.md            # This file
```

## 🔧 Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | MySQL host | localhost |
| `DB_PORT` | MySQL port | 3306 |
| `DB_USER` | MySQL username | root |
| `DB_PASS` | MySQL password | (empty) |
| `DB_NAME` | Database name | atiera |
| `JWT_SECRET` | JWT signing secret | (auto-generated) |
| `PORT` | Server port | 5050 |
| `NODE_ENV` | Environment | development |

### Database Configuration

The system automatically creates:
- Database if it doesn't exist
- All required tables
- Sample data for immediate use
- Proper indexes and foreign key relationships

## 🚀 Deployment

### WAMP Server Production
1. Ensure WAMP Server is properly configured
2. Set `NODE_ENV=production` in `.env`
3. Use `npm start` instead of `npm run dev`
4. Configure Apache virtual hosts if needed

### Docker Deployment (Optional)
```bash
# Build and run with Docker
docker build -t atiera-financial .
docker run -p 5050:5050 atiera-financial
```

## 🔒 Security Features

- **Password Protection**: bcrypt hashing with salt
- **Session Management**: Secure PHP sessions
- **JWT Authentication**: Stateless API authentication
- **Rate Limiting**: API endpoint protection
- **CORS Configuration**: Cross-origin request control
- **Input Validation**: SQL injection prevention
- **Audit Logging**: Complete transaction history

## 📊 Database Schema

The system includes a comprehensive financial database schema:
- **Users & Roles**: Authentication and authorization
- **Chart of Accounts**: Hierarchical account structure
- **Journal Entries**: Double-entry bookkeeping
- **Customers & Vendors**: Business relationship management
- **Invoices & Bills**: Document management
- **Payments**: Transaction processing
- **Budgets**: Planning and control
- **Activity Logs**: Audit trail

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Check the API documentation
- Review the database schema
- Check the troubleshooting section below

## 🔍 Troubleshooting

### Common Issues

**Database Connection Failed**
- Ensure WAMP Server is running
- Check MySQL service status
- Verify database credentials in `.env`

**Port Already in Use**
- Change `PORT` in `.env` file
- Check if another service is using port 5050

**Module Not Found Errors**
- Run `npm install` to install dependencies
- Check Node.js version compatibility

**Permission Denied**
- Ensure MySQL user has CREATE/ALTER privileges
- Check file permissions for uploads directory

### Logs and Debugging

- Check console output for error messages
- Review MySQL error logs in WAMP Server
- Use `npm run dev` for detailed logging

## 🎯 Roadmap

- [ ] Mobile app development
- [ ] Advanced reporting features
- [ ] Multi-currency support
- [ ] Integration with banking APIs
- [ ] Advanced user management
- [ ] Backup and restore functionality

---

**ATIERA Financial System** - Professional financial management for modern businesses.
