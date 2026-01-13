# Installation Guide - Sample E-commerce Platform

## Prerequisites

Before installing this Laravel application, ensure your system meets the following requirements:

### System Requirements

-   **PHP:** 8.1 or higher
-   **Composer:** Latest version
-   **Node.js:** 16.x or higher
-   **NPM:** 8.x or higher
-   **MySQL:** 5.7+ or MariaDB 10.3+
-   **Web Server:** Apache 2.4+ or Nginx 1.18+

### PHP Extensions Required

```bash
php -m | grep -E "(openssl|pdo|mbstring|tokenizer|xml|ctype|json|bcmath|curl|fileinfo|gd)"
```

Required extensions:

-   OpenSSL PHP Extension
-   PDO PHP Extension
-   Mbstring PHP Extension
-   Tokenizer PHP Extension
-   XML PHP Extension
-   Ctype PHP Extension
-   JSON PHP Extension
-   BCMath PHP Extension
-   cURL PHP Extension
-   Fileinfo PHP Extension
-   GD PHP Extension

## Installation Steps

### 1. Clone the Repository

```bash
git clone [repository-url] sample-ecommerce
cd sample-ecommerce
```

### 2. Install PHP Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

For development environment:

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Environment Variables

Edit the `.env` file with your specific configuration:

```env
# Application Settings
APP_NAME="Sample E-commerce"
APP_ENV=production  # or local for development
APP_KEY=[auto-generated]
APP_DEBUG=false     # true for development
APP_URL=https://yourdomain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sample_ecommerce
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# SMS Configuration (Hutch)
SMS_SERVICE=true
HUTCH_SMS_USERNAME=your_hutch_username
HUTCH_SMS_PASSWORD=your_hutch_password
HUTCH_CAMPAIGN_NAME=your_campaign_name
HUTCH_MASK=your_sender_mask
HUTCH_DEBUG=false

# PromptAPT Waybill API
PROMPTAPT_BASE_URL=https://api.promptapt.com
PROMPTAPT_API_KEY=your_promptapt_api_key
PROMPTAPT_CLIENT_ID=your_promptapt_client_id

# Session and Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Optional: Redis Configuration (recommended for production)
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
# SESSION_DRIVER=redis
# CACHE_DRIVER=redis
```

### 6. Database Setup

```bash
# Create database (if not exists)
mysql -u root -p -e "CREATE DATABASE sample_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed
```

### 7. Storage and Permissions

```bash
# Create storage link
php artisan storage:link

# Set proper permissions (Linux/macOS)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# For development (adjust user as needed)
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 8. Build Frontend Assets

For production:

```bash
npm run build
```

For development:

```bash
npm run dev
```



### 9. Local Development Server

```bash
# Start development server
php artisan serve

# Watch for asset changes
npm run dev
```

**Installation Guide Version:** 1.0  
**Compatible with:** Laravel 10.x  
**Last Updated:** January 13, 2026

For support, contact: [developer@email.com]
