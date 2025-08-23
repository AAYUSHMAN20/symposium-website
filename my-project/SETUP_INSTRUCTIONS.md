# Symposium Contact Form Backend Setup

This guide will help you set up the backend for your symposium website's contact form using PHP, MySQL, and JavaScript.

## 📋 Prerequisites

- **Web Server**: Apache or Nginx with PHP support
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB)
- **Web Browser**: Modern browser with JavaScript enabled

## 🚀 Installation Steps

### 1. Database Setup

1. **Create Database**:
   ```sql
   CREATE DATABASE symposium_db;
   ```

2. **Run the SQL Script**:
   - Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line)
   - Navigate to the `backend/create_table.sql` file
   - Execute the SQL commands to create the `contact_messages` table

   Or via command line:
   ```bash
   mysql -u your_username -p symposium_db < backend/create_table.sql
   ```

### 2. Configure Database Connection

1. **Edit Configuration File**:
   - Open `backend/config.php`
   - Update the database credentials:
   ```php
   define('DB_HOST', 'localhost');        // Your database host
   define('DB_USERNAME', 'your_username'); // Your MySQL username
   define('DB_PASSWORD', 'your_password'); // Your MySQL password
   define('DB_NAME', 'symposium_db');     // Database name
   ```

### 3. File Structure Setup

Ensure your project structure looks like this:
```
my project/
├── backend/
│   ├── config.php
│   ├── submit_contact.php
│   ├── admin.php
│   └── create_table.sql
├── assets/
│   └── js/
│       └── contact.js
├── assest/
│   ├── css/
│   │   └── style.css
│   └── images/
│       └── logo.jpg
├── contact.html
├── index.html
├── about.html
└── SETUP_INSTRUCTIONS.md
```

### 4. Server Configuration

1. **Upload Files**:
   - Upload all files to your web server (shared hosting, VPS, or local server)
   - Ensure the web server can execute PHP files

2. **Set Permissions**:
   ```bash
   chmod 644 backend/*.php
   chmod 644 assets/js/*.js
   chmod 644 *.html
   ```

3. **Test PHP Configuration**:
   - Create a test file `phpinfo.php`:
   ```php
   <?php phpinfo(); ?>
   ```
   - Access it via browser to ensure PHP is working

### 5. Security Considerations

1. **Production Settings**:
   - In `backend/config.php`, disable error reporting for production:
   ```php
   ini_set('display_errors', 0);
   error_reporting(0);
   ```

2. **Database Security**:
   - Use strong passwords for database users
   - Create a dedicated database user with minimal privileges
   - Consider using environment variables for sensitive data

3. **Admin Access**:
   - Protect `backend/admin.php` with authentication
   - Consider adding .htaccess protection

## 🧪 Testing

### 1. Test Contact Form
1. Open your website's contact page
2. Fill out the contact form with test data
3. Submit the form
4. Check for success message

### 2. Test Admin Dashboard
1. Navigate to `your-domain.com/backend/admin.php`
2. Verify that submitted messages appear
3. Test the mark as read/unread functionality
4. Test the delete functionality

### 3. Test Database Connection
1. Check if data is being saved in the `contact_messages` table
2. Verify all form fields are properly stored

## 🎯 Features

### Contact Form Features:
- ✅ Real-time form validation
- ✅ AJAX form submission (no page reload)
- ✅ Responsive design
- ✅ Bootstrap styling
- ✅ Duplicate submission prevention
- ✅ Input sanitization and validation

### Admin Dashboard Features:
- ✅ View all contact messages
- ✅ Mark messages as read/unread
- ✅ Delete messages
- ✅ Pagination for large datasets
- ✅ Statistics overview
- ✅ Reply via email client
- ✅ Responsive design

### Database Features:
- ✅ Secure data storage
- ✅ Indexed fields for performance
- ✅ Automatic timestamps
- ✅ IP address logging
- ✅ Prepared statements (SQL injection prevention)

## 🔧 Customization

### 1. Form Fields
To add or modify form fields:
1. Update the HTML form in `contact.html`
2. Modify validation in `assets/js/contact.js`
3. Update the PHP handler in `backend/submit_contact.php`
4. Modify the database table structure if needed

### 2. Email Notifications
To enable email notifications when forms are submitted:
1. Uncomment the email section in `backend/submit_contact.php`
2. Configure your server's mail settings
3. Update the admin email address

### 3. Styling
- Modify `assest/css/style.css` for custom styling
- Update Bootstrap classes in HTML files
- Customize the admin dashboard appearance

## 🐛 Troubleshooting

### Common Issues:

1. **Database Connection Failed**:
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Form Not Submitting**:
   - Check browser console for JavaScript errors
   - Ensure `assets/js/contact.js` is loading
   - Verify PHP file paths

3. **Admin Dashboard Not Loading**:
   - Check PHP error logs
   - Ensure database table exists
   - Verify file permissions

4. **404 Errors**:
   - Check file paths in HTML
   - Ensure all files are uploaded
   - Verify web server configuration

## 📱 Browser Compatibility

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers

## 🔐 Security Notes

- All user inputs are sanitized and validated
- SQL injection protection via prepared statements
- XSS protection via output escaping
- CSRF protection can be added for enhanced security
- Consider implementing rate limiting for production use

## 📞 Support

If you encounter any issues:
1. Check the browser console for JavaScript errors
2. Check server error logs for PHP errors
3. Verify database connection and table structure
4. Ensure all file paths are correct

---

**Note**: Remember to backup your database regularly and test thoroughly before deploying to production!
