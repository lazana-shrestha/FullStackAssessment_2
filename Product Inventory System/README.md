# Product Inventory System

A complete web application for managing product inventory with user authentication and role-based access control.

## Features

### Authentication System

- User registration and login
- Role-based access (Admin/User)
- Secure password hashing

### Admin Features (Full CRUD)

- Add new products with details
- Edit existing products
- Delete products with confirmation
- View all products with filters

### User Features

- View product list
- Search products by name/category
- Filter by price range
- View product details
- Check stock availability

### Advanced Features

- Advanced search with multiple criteria
- Stock status indicators
- Responsive Bootstrap design
- Form validation
- Security measures against SQL injection/XSS

## Installation Instructions

### 1. Database Setup

1. Open phpMyAdmin
2. Create a new database named `product_inventory`
3. Import the SQL structure from `database.sql`
4. Update `config/db.php` with your database credentials

### 2. File Structure

Place all files in your web server directory (e.g., `htdocs/product_inventory`)

### 3. Test Accounts

- **Admin**: username: `admin` | password: `password`
- **User**: username: `user1` | password: `password`

## Security Features

- Prepared statements for SQL injection prevention
- Output escaping with `htmlspecialchars()`
- Password hashing with `password_hash()`
- Session-based authentication
- Input validation and sanitization

## Files Included

- `config/db.php` - Database configuration
- `public/` - All application pages
- `includes/` - Header, footer, and functions
- `assets/` - CSS and JavaScript files
- `database.sql` - Database structure and sample data

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server
- PDO extension enabled

## Development Notes

This project uses:

- Bootstrap 5 for responsive design
- Font Awesome for icons
- PDO for database operations
- Vanilla JavaScript for interactivity

## Future Enhancements

- AJAX-based live search
- Product image uploads
- Export to CSV/PDF
- Email notifications for low stock
- User profile management
