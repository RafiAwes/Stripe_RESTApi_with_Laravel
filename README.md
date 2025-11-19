# Laravel Stripe Payment Platform

A comprehensive Laravel-based payment platform that integrates with Stripe to handle user authentication, payment processing, and fund transfers to connected accounts. This project demonstrates a complete payment ecosystem with user management, order processing, and supplier payout functionality.

## Features

- User authentication with Laravel Sanctum
- Stripe Express account creation for suppliers
- Payment intent creation and processing
- Fund transfers to connected Stripe accounts
- RESTful API architecture
- Secure payment handling with idempotency keys

## Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL or another supported database
- Stripe Account with API keys

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd stripe_project
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Copy and configure the environment file:
   ```bash
   cp .env.example .env
   ```
   
   Update the following variables in `.env`:
   - Database configuration (`DB_*`)
   - Stripe keys (`STRIPE_SECRET_KEY`, `STRIPE_PUBLIC_KEY`)

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. Build frontend assets:
   ```bash
   npm run build
   ```

## Setup Commands

You can also use the built-in setup script:
```bash
composer run setup
```

For development:
```bash
composer run dev
```

## API Endpoints

All API endpoints are prefixed with `/api/`.

### Authentication

#### User Login
```
POST /api/user/login
```
Parameters:
- email (string, required)
- password (string, required)

#### User Registration
```
POST /api/user/register
```
Parameters:
- name (string, required)
- email (string, required)
- password (string, required)

#### Create Stripe Connected Account
```
POST /api/user/create-account
```
Parameters:
- email (string, required)
- Auth token required

### Payments

#### Create Payment Intent
```
POST /api/payment/create
```
Parameters:
- order_id (integer, required)
- currency (string, required, 3-letter ISO code)

#### Confirm Payment
```
POST /api/payment/confirm
```
Parameters:
- payment_intend_id (string, required)

#### Release Funds to Supplier
```
POST /api/payment/release
```
Parameters:
- order_id (integer, required)

## Database Structure

The application uses several key tables:

- `users`: Stores user information including Stripe account IDs
- `orders`: Contains order details with supplier associations
- `payments`: Tracks payment intents and their statuses
- `transfers`: Records fund transfers to suppliers

## Stripe Integration

This project uses Stripe Express accounts for suppliers, allowing them to receive payouts directly from the platform. Key features include:

1. Connected account creation
2. Account onboarding via Stripe-hosted forms
3. Payment intent processing
4. Transfer of funds to connected accounts

## Environment Variables

Required environment variables in `.env`:

- `STRIPE_SECRET_KEY`: Your Stripe secret key
- `STRIPE_PUBLIC_KEY`: Your Stripe public key
- Database configuration variables (`DB_*`)

## Testing

Run the test suite with:
```bash
composer test
```

## License

This project is open-sourced software licensed under the MIT license.