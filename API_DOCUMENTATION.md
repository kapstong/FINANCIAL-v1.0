# ATIERA Financial System API Documentation

## Overview
The ATIERA Financial System provides a comprehensive REST API for managing all aspects of financial operations including General Ledger, Accounts Receivable, Accounts Payable, Budget Management, and Reporting.

## Base URL
```
http://localhost:5050/api
```

## Authentication
Most endpoints require JWT authentication. Include the token in the Authorization header:
```
Authorization: Bearer <your-jwt-token>
```

## Endpoints

### Authentication

#### POST /auth/login
Authenticate user and receive JWT token.

**Request Body:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response:**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### General Ledger (GL)

#### GET /gl/accounts
Retrieve chart of accounts.

**Query Parameters:**
- `type`: Filter by account type (ASSET, LIABILITY, EQUITY, REVENUE, EXPENSE)
- `active`: Filter by active status (true/false)

#### POST /gl/accounts
Create new account.

**Request Body:**
```json
{
  "code": "1020",
  "name": "Petty Cash",
  "type": "ASSET",
  "parent_id": null
}
```

#### GET /gl/journal-entries
Retrieve journal entries.

**Query Parameters:**
- `date_from`: Start date (YYYY-MM-DD)
- `date_to`: End date (YYYY-MM-DD)
- `je_no`: Journal entry number

#### POST /gl/journal-entries
Create new journal entry.

**Request Body:**
```json
{
  "date": "2025-01-15",
  "memo": "Monthly rent payment",
  "lines": [
    {
      "account_id": 5000,
      "description": "Rent expense",
      "debit": 1000.00,
      "credit": 0.00
    },
    {
      "account_id": 1010,
      "description": "Bank payment",
      "debit": 0.00,
      "credit": 1000.00
    }
  ]
}
```

### Accounts Receivable (AR)

#### GET /ar/customers
Retrieve customer list.

#### POST /ar/customers
Create new customer.

**Request Body:**
```json
{
  "name": "ABC Company",
  "email": "contact@abc.com",
  "phone": "+1234567890",
  "address": "123 Business St, City, State"
}
```

#### GET /ar/invoices
Retrieve invoices.

**Query Parameters:**
- `customer_id`: Filter by customer
- `status`: Filter by status (OPEN, PAID, PARTIAL, VOID)
- `date_from`: Start date
- `date_to`: End date

#### POST /ar/invoices
Create new invoice.

**Request Body:**
```json
{
  "customer_id": 1,
  "invoice_date": "2025-01-15",
  "due_date": "2025-02-15",
  "lines": [
    {
      "item": "Consulting Services",
      "qty": 10,
      "unit_price": 100.00
    }
  ]
}
```

### Accounts Payable (AP)

#### GET /ap/vendors
Retrieve vendor list.

#### POST /ap/vendors
Create new vendor.

#### GET /ap/bills
Retrieve vendor bills.

#### POST /ap/bills
Create new vendor bill.

### Budget Management

#### GET /budget/budgets
Retrieve budgets.

**Query Parameters:**
- `fiscal_year`: Filter by fiscal year
- `department`: Filter by department

#### POST /budget/budgets
Create new budget.

**Request Body:**
```json
{
  "fiscal_year": 2025,
  "department": "Sales",
  "lines": [
    {
      "account_id": 5000,
      "period": 1,
      "amount": 5000.00
    }
  ]
}
```

### Reports

#### GET /reports/trial-balance
Generate trial balance report.

**Query Parameters:**
- `as_of_date`: Date for trial balance (YYYY-MM-DD)

#### GET /reports/income-statement
Generate income statement.

**Query Parameters:**
- `period_from`: Start period (YYYY-MM)
- `period_to`: End period (YYYY-MM)

#### GET /reports/balance-sheet
Generate balance sheet.

**Query Parameters:**
- `as_of_date`: Date for balance sheet (YYYY-MM-DD)

### Collections

#### GET /collections/payments
Retrieve payments.

#### POST /collections/payments
Record customer payment.

### Disbursements

#### GET /disbursement/disbursements
Retrieve disbursements.

#### POST /disbursement/disbursements
Record disbursement.

### Activity Log

#### GET /activity/logs
Retrieve activity logs.

**Query Parameters:**
- `module`: Filter by module
- `actor_id`: Filter by user
- `date_from`: Start date
- `date_to`: End date

## Error Responses

All endpoints return consistent error responses:

```json
{
  "error": "Error message",
  "details": "Additional error details"
}
```

**HTTP Status Codes:**
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Internal Server Error

## Rate Limiting

API endpoints are rate-limited to 100 requests per 15 minutes per IP address.

## Security

- All sensitive endpoints require JWT authentication
- Passwords are hashed using bcrypt
- CORS is configured for security
- Helmet.js provides additional security headers
- Input validation is enforced on all endpoints

## Testing

Test the API using the health check endpoint:
```
GET /api/health
```

## Support

For API support and questions, please refer to the system documentation or contact the development team.
