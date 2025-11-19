# Laravel Stripe Payment Platform - Technical Documentation

## Project Overview

This Laravel application provides a complete payment processing platform integrated with Stripe. It enables user authentication, order management, payment processing, and fund distribution to connected supplier accounts.

## Architecture

### Key Components

1. **Controllers**: API endpoints for user management, payments, and transfers
2. **Models**: Database entities representing users, orders, payments, and transfers
3. **Services**: Stripe integration layer abstracting Stripe API calls
4. **Migrations**: Database schema definitions

### Directory Structure

```
app/
├── Http/Controllers/Api/
│   ├── userController.php
│   ├── paymentController.php
│   └── TransferController.php
├── Models/
│   ├── User.php
│   ├── order.php
│   ├── payment.php
│   └── Transfer.php
├── Services/
│   └── StripeService.php
config/
├── cashier.php
└── services.php
database/
└── migrations/
routes/
└── api.php
```

## Core Functionality

### User Management

The [userController](app/Http/Controllers/Api/userController.php#L13-L94) handles user authentication and Stripe account creation:

1. **User Registration/Login** (`/api/user/login`, `/api/user/register`):
   - Authenticates users with email and password
   - Uses Laravel Sanctum for API token management
   - Automatically registers new users during login if they don't exist

2. **Stripe Account Creation** (`/api/user/create-account`):
   - Creates a Stripe Express account for suppliers
   - Generates an onboarding link for account setup
   - Stores the Stripe account ID in the user record

### Payment Processing

The [paymentController](app/Http/Controllers/Api/paymentController.php#L9-L72) manages payment intents:

1. **Create Payment Intent** (`/api/payment/create`):
   - Creates a Stripe PaymentIntent for an order
   - Stores payment details in the database
   - Uses idempotency keys to prevent duplicate charges

2. **Confirm Payment** (`/api/payment/confirm`):
   - Confirms a previously created payment intent

### Fund Distribution

The [TransferController](app/Http/Controllers/Api/TransferController.php#L14-L82) handles fund transfers to suppliers:

1. **Release to Supplier** (`/api/payment/release`):
   - Calculates supplier share after platform fees
   - Creates a Stripe transfer to the supplier's connected account
   - Records transfer details in the database

## Database Schema

### Users Table
Extended with `stripe_account_id` column to store connected account identifiers.

### Orders Table
- `supplier_id`: Links to the supplier who will receive funds
- `amount`: Order amount in cents
- `currency`: 3-letter currency code
- `platform_fee`: Fee retained by the platform
- `status`: Order status (pending, paid, shipped, completed, cancelled)

### Payments Table
- Tracks payment intents and their statuses
- Links to orders via `order_id`

### Transfers Table
- Records fund transfers to suppliers
- Stores transfer status and Stripe response data

## Stripe Integration Details

### StripeService Class

The [StripeService](app/Services/StripeService.php#L12-L98) provides a clean abstraction over the Stripe API:

1. **createConnectedAccount()**: Creates a Stripe Express account
2. **createAccountLink()**: Generates onboarding URLs
3. **createPaymentIntent()**: Creates payment intents with idempotency
4. **createTransfer()**: Transfers funds to connected accounts

### Key Stripe Features Used

1. **Express Accounts**: Simplified onboarding for suppliers
2. **Payment Intents**: Secure payment processing with built-in authentication
3. **Transfers**: Direct fund distribution to connected accounts
4. **Idempotency Keys**: Prevents duplicate operations
5. **Webhook Handling**: (Configured but implementation not shown in codebase)

## API Usage Examples

### 1. User Authentication
```bash
# Register a new user
curl -X POST http://localhost/api/user/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secret123"
  }'

# Login as existing user
curl -X POST http://localhost/api/user/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "secret123"
  }'
```

### 2. Create Stripe Account
```bash
# Requires authentication token from login
curl -X POST http://localhost/api/user/create-account \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com"
  }'
```

### 3. Process Payment
```bash
# Create payment intent
curl -X POST http://localhost/api/payment/create \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": 1,
    "currency": "usd"
  }'
```

### 4. Release Funds
```bash
# Transfer funds to supplier
curl -X POST http://localhost/api/payment/release \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": 1
  }'
```

## Error Handling

The application implements comprehensive error handling:
- Validation for all API requests
- Stripe API error catching and re-throwing as application exceptions
- Database transaction safety
- Proper HTTP status codes for different error scenarios

## Security Considerations

1. **Authentication**: All sensitive endpoints require API tokens
2. **Data Validation**: All inputs are validated before processing
3. **Idempotency**: Critical Stripe operations use idempotency keys
4. **Environment Separation**: Sensitive keys stored in environment variables
5. **SQL Injection**: Eloquent ORM prevents direct SQL injection

## Extending the Platform

### Adding New Features

1. **New Payment Methods**: Extend the [StripeService](file:///c%3A/Users/Bd%20calling/Desktop/laravel%20test%20projects/stripe_project/app/Services/StripeService.php#L12-L98) with additional payment method functions
2. **Enhanced User Roles**: Add role-based access control to the [User](file:///c%3A/Users/Bd%20calling/Desktop/laravel%20test%20projects/stripe_project/app/Models/User.php#L12-L50) model
3. **Subscription Management**: Leverage Laravel Cashier for recurring payments
4. **Advanced Reporting**: Add analytics endpoints to track payment and transfer metrics

### Customization Points

1. **Fee Calculation**: Modify the transfer logic in [TransferController](file:///c%3A/Users/Bd%20calling/Desktop/laravel%20test%20projects/stripe_project/app/Http/Controllers/Api/TransferController.php#L14-L82)
2. **Order Status Management**: Extend the order workflow in the [order](file:///c%3A/Users/Bd%20calling/Desktop/laravel%20test%20projects/stripe_project/app/Models/order.php#L7-L19) model
3. **Webhook Processing**: Implement Stripe webhook handlers for real-time event processing
4. **Frontend Integration**: Build client-side interfaces using the API endpoints

## Troubleshooting

### Common Issues

1. **Stripe API Errors**:
   - Verify `STRIPE_SECRET_KEY` in `.env`
   - Check Stripe dashboard for account restrictions

2. **Database Connection Errors**:
   - Verify database credentials in `.env`
   - Ensure MySQL service is running

3. **Migration Failures**:
   - Check for missing foreign key relationships
   - Ensure database is empty or properly seeded

### Debugging Tips

1. Enable debug mode in `.env` (`APP_DEBUG=true`)
2. Check Laravel logs in `storage/logs/laravel.log`
3. Use `php artisan tinker` for interactive debugging
4. Monitor Stripe dashboard for API request logs

## Performance Considerations

1. **Database Indexing**: Ensure proper indexes on frequently queried columns
2. **Caching**: Implement Redis caching for frequently accessed data
3. **Queue Processing**: Use Laravel queues for background processing of transfers
4. **Rate Limiting**: Implement API rate limiting to prevent abuse

## Dependencies

### PHP Packages
- `laravel/framework`: Core Laravel framework
- `laravel/cashier`: Stripe integration for Laravel
- `laravel/sanctum`: API token authentication
- `stripe/stripe-php`: Official Stripe PHP library

### Development Tools
- `pestphp/pest`: Testing framework
- `fakerphp/faker`: Test data generation
- `nunomaduro/collision`: Error formatting

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is open-sourced software licensed under the MIT license.