# Shopway E-commerce Website

A modern e-commerce platform built with PHP and MySQL, featuring a responsive design and comprehensive admin dashboard.

## Features

### Customer Features
- User registration and authentication
- Product browsing with filter and search capabilities
- Product categories
- Shopping cart functionality
- Checkout process
- Order history
- Responsive design for all devices

### Admin Features
- Comprehensive dashboard with sales analytics
- Product management (add, edit, delete)
- Category management
- Order management
- Customer management
- Sales reports

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for future package management)

### Setup Instructions

1. **Clone the repository**
   ```
   git clone https://github.com/yourusername/shopway.git
   cd shopway
   ```

2. **Create the database**
   - Create a MySQL database named `shop`
   - Import the database schema from `database.sql`
   ```
   mysql -u your_username -p shop < database.sql
   ```

3. **Configure the database connection**
   - Open `includes/config.php`
   - Update the database credentials:
   ```php
   define('DB_HOST', 'localhost'); // Your database host
   define('DB_USER', 'root');      // Your database username
   define('DB_PASS', '');          // Your database password
   define('DB_NAME', 'shop');      // Database name
   ```

4. **Update the base URL**
   - Still in `includes/config.php`, update the BASE_URL constant to match your server setup:
   ```php
   define('BASE_URL', 'http://localhost/shopway'); // Adjust as needed
   ```

5. **Set proper permissions**
   - Ensure the `assets/images` directory is writable by the web server
   ```
   chmod 755 assets/images
   ```

6. **Access the application**
   - Navigate to your website in the browser

## Default Login Credentials

### Admin User
- Username: admin
- Password: admin123

### Regular User
- Username: johndoe
- Password: password123

## Project Structure

```
shopway/
├── admin/                  # Admin dashboard files
├── assets/                 # Frontend assets
│   ├── css/               # CSS files
│   ├── js/                # JavaScript files
│   └── images/            # Product images and other media
├── includes/               # Reusable PHP files
│   ├── config.php         # Configuration variables
│   ├── db.php             # Database connection and helpers
│   ├── header.php         # Common header
│   └── footer.php         # Common footer
├── database.sql            # Database schema
├── index.php               # Home page
├── products.php            # Product listing
├── product.php             # Single product details
├── cart.php                # Shopping cart
├── checkout.php            # Checkout process
├── login.php               # User login
├── register.php            # User registration
├── account.php             # User account
└── README.md               # This file
```

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements for all database queries
- Input validation and sanitization
- CSRF protection
- XSS prevention

## Customization

- To add new product categories, use the admin dashboard
- To change the site theme, modify the CSS files in `assets/css/`
- To change the site logo, replace the logo image in `assets/images/`

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.

## Contact

Your Name - [@yourusername](https://twitter.com/yourusername) - email@example.com

Project Link: [https://github.com/yourusername/shopway](https://github.com/yourusername/shopway) 