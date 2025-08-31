# 🎓 Symposium Management System

A comprehensive web-based symposium management system with student and faculty registration, seminar topic selection, administrative features, and contact form management. This system supports 210+ categorized seminar topics and provides a complete solution for managing academic symposiums.

## ✨ Features

### 👨‍🎓 For Students
- **Registration**: Complete registration with personal details
- **Login**: Secure login with email and password
- **Dashboard**: View registration details, seminar information, and QR code
- **Seminar Selection**: Choose from 210+ categorized seminar topics
- **Two-Step Registration**: Register first, then select seminar topic on event page

### 👨‍🏫 For Faculty
- **Login**: Department-specific login system
- **Dashboard**: View all registered students with their seminar details
- **Search & Filter**: Search students by name, email, roll number, or filter by seminar topic
- **Statistics**: View registration statistics and trends

### 👨‍💼 For Administrators
- **Admin Panel**: Manage faculty registrations and view system statistics
- **Add Faculty**: Register new faculty members with all required details
- **Contact Messages**: View and manage contact form submissions
- **System Overview**: View comprehensive system statistics and user counts

## 🛠️ Prerequisites

- **Operating System**: Windows, macOS, or Linux
- **Web Browser**: Chrome, Firefox, Safari, or Edge
- **Internet Connection**: For downloading required software

## 🚀 Installation & Setup Guide

### Step 1: Install MySQL Database

#### For Windows:
1. **Download MySQL**:
   - Go to https://dev.mysql.com/downloads/mysql/
   - Click "Download" for MySQL Community Server
   - Choose "Windows (x86, 64-bit), MSI Installer"
   - Download the larger "mysql-installer-community" file

2. **Install MySQL**:
   - Run the downloaded MSI installer
   - Choose "Custom" installation type
   - Select these components:
     - MySQL Server (latest version)
     - MySQL Workbench (optional but recommended)
     - MySQL Shell (optional)
   - Click "Next" and then "Execute" to install

3. **Configure MySQL**:
   - Choose "Standalone MySQL Server"
   - Keep default port (3306)
   - Set root password (remember this!)
   - Create a user account if desired
   - Complete the configuration

4. **Verify Installation**:
   - Open Command Prompt as Administrator
   - Type: `mysql --version`
   - You should see MySQL version information

#### For macOS:
1. **Download MySQL**:
   - Go to https://dev.mysql.com/downloads/mysql/
   - Select macOS version
   - Download the DMG file

2. **Install MySQL**:
   - Open the downloaded DMG file
   - Run the PKG installer
   - Follow installation wizard
   - Set root password during installation

3. **Start MySQL**:
   - Go to System Preferences → MySQL
   - Click "Start MySQL Server"

4. **Add MySQL to PATH** (optional):
   - Open Terminal
   - Run: `echo 'export PATH="/usr/local/mysql/bin:$PATH"' >> ~/.bash_profile`
   - Restart Terminal

#### For Linux (Ubuntu/Debian):
1. **Update package index**:
   ```bash
   sudo apt update
   ```

2. **Install MySQL**:
   ```bash
   sudo apt install mysql-server
   ```

3. **Secure installation**:
   ```bash
   sudo mysql_secure_installation
   ```

4. **Start MySQL service**:
   ```bash
   sudo systemctl start mysql
   sudo systemctl enable mysql
   ```

### Step 2: Install Local Web Server (XAMPP)

#### Why XAMPP?
XAMPP provides Apache web server, PHP, and additional MySQL tools in one package, making local development easy.

#### Download and Install XAMPP:

1. **Go to XAMPP website**:
   - Visit: https://www.apachefriends.org/
   - Click "Download" for your operating system

2. **For Windows**:
   - Download the Windows version
   - Run the installer
   - Choose installation directory
   - Select components (Apache, MySQL, PHP, phpMyAdmin)
   - Complete installation

3. **For macOS**:
   - Download the macOS version
   - Open the downloaded file
   - Drag XAMPP to Applications folder
   - Open XAMPP Control Panel

4. **For Linux**:
   ```bash
   # Download XAMPP
   wget https://www.apachefriends.org/xampp-files/8.x.x/xampp-linux-x64-8.x.x-installer.run
   
   # Make executable
   chmod +x xampp-linux-x64-8.x.x-installer.run
   
   # Run installer
   sudo ./xampp-linux-x64-8.x.x-installer.run
   ```

### Step 3: Project Setup

1. **Download/Clone Project**:
   - Download the project files
   - Extract to your XAMPP htdocs folder:
     - Windows: `C:\xampp\htdocs\my-project\`
     - macOS: `/Applications/XAMPP/htdocs/my-project/`
     - Linux: `/opt/lampp/htdocs/my-project/`

2. **Start XAMPP Services**:
   - Open XAMPP Control Panel
   - Start Apache and MySQL services
   - Both should show green status

3. **Configure Database**:
   - Open your web browser
   - Go to: `http://localhost/phpmyadmin`
   - Create a new database named `symposium_db`
   - Import the SQL file: `backend/create_tables.sql`

4. **Update Database Configuration**:
   - Open `backend/config.php`
   - Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USERNAME', 'root');        // Default XAMPP username
   define('DB_PASSWORD', '');            // Default XAMPP password (empty)
   define('DB_NAME', 'symposium_db');
   ```

5. **Run Database Setup**:
   - Open your web browser
   - Navigate to: `http://localhost/my-project/backend/setup_database.php`
   - This will create all tables and populate seminar topics
   - You should see success messages for each step

### Step 4: Test the System

1. **Test Admin Login**:
   - Visit: `http://localhost/my-project/admin_login.html`
   - Login with: username=`admin`, password=`admin123`

2. **Test Student Registration**:
   - Visit: `http://localhost/my-project/index.html`
   - Try registering a new student

3. **Test Faculty Login**:
   - First add a faculty member through admin panel
   - Then try logging in with faculty credentials

## 📁 Project Structure

```
my-project/
├── index.html                    # Main homepage with login/registration
├── student_dashboard.html        # Student dashboard
├── faculty_dashboard.html        # Faculty dashboard
├── admin_login.html              # Admin login page
├── admin_panel.html              # Admin panel
├── messages.html                 # Contact messages dashboard
├── event.html                    # Seminar selection page
├── about.html                    # About page
├── contact.html                  # Contact page
├── backend/
│   ├── config.php                # Database configuration
│   ├── create_tables.sql         # Database schema
│   ├── setup_database.php        # Database setup script
│   ├── faculty_login.php         # Faculty login handler
│   ├── student_login.php         # Student login handler
│   ├── student_register.php      # Student registration handler
│   ├── admin_login.php           # Admin login handler
│   ├── add_faculty.php           # Add faculty handler
│   ├── get_faculty.php           # Get faculty data
│   ├── get_messages.php          # Get contact messages
│   ├── toggle_message_read.php   # Toggle message read status
│   ├── delete_message.php        # Delete contact messages
│   ├── submit_contact.php        # Contact form handler
│   └── test_connection.php       # Database connection test
├── assets/
│   ├── css/
│   │   └── style.css            # Main stylesheet
│   ├── js/
│   │   └── contact.js           # Contact form JavaScript
│   └── images/
│       └── logo.jpg             # Logo image
├── Seminar Topics List.txt      # Seminar topics data
└── README.md                    # This file
```

## 🗄️ Database Structure

The system uses MySQL with the following tables:

### `faculty` Table
- `id` (Primary Key, Auto Increment)
- `first_name`, `last_name` (VARCHAR)
- `email` (VARCHAR, Unique)
- `contact_number` (VARCHAR)
- `faculty_id` (VARCHAR, Unique)
- `password` (VARCHAR, Hashed)
- `department` (VARCHAR)
- `created_at`, `updated_at` (TIMESTAMP)

### `students` Table
- `id` (Primary Key, Auto Increment)
- `first_name`, `last_name` (VARCHAR)
- `email` (VARCHAR, Unique)
- `contact_number` (VARCHAR)
- `roll_number` (VARCHAR, Unique)
- `password` (VARCHAR, Hashed)
- `seminar_topic` (VARCHAR, Nullable)
- `seminar_link` (VARCHAR, Nullable)
- `qr_code` (VARCHAR, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

### `seminars` Table
- `id` (Primary Key, Auto Increment)
- `topic` (VARCHAR)
- `category` (VARCHAR)
- `is_active` (BOOLEAN)
- `created_at` (TIMESTAMP)

### `admin` Table
- `id` (Primary Key, Auto Increment)
- `username` (VARCHAR, Unique)
- `password` (VARCHAR, Hashed)
- `email` (VARCHAR, Unique)
- `created_at` (TIMESTAMP)

### `contact_messages` Table
- `id` (Primary Key, Auto Increment)
- `first_name`, `last_name` (VARCHAR)
- `email` (VARCHAR)
- `phone` (VARCHAR, Nullable)
- `subject` (VARCHAR)
- `message` (TEXT)
- `ip_address` (VARCHAR)
- `is_read` (BOOLEAN, Default: FALSE)
- `submitted_at` (TIMESTAMP)

## 🔧 Configuration

### Database Configuration (`backend/config.php`)
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');        // Your MySQL username
define('DB_PASSWORD', '');            // Your MySQL password
define('DB_NAME', 'symposium_db');    // Database name

function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USERNAME,
            DB_PASSWORD,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        return null;
    }
}
?>
```

### Default Credentials
- **Admin**: username=`admin`, password=`admin123`
- **Database**: username=`root`, password=`` (empty for XAMPP)

## 🚨 Troubleshooting

### Common Issues and Solutions

#### Issue 1: "Failed to add faculty member: Unauthorized access"
**Problem**: Admin authentication mismatch between frontend and backend.

**Solution**: 
1. Make sure you've run the database setup first
2. Use the admin login page at `admin_login.html`
3. Login with username: `admin` and password: `admin123`

#### Issue 2: "Loading seminar topics..." - No topics appear
**Problem**: Database not set up or seminars table empty.

**Solution**:
1. **Run database setup**: Visit `http://localhost/my-project/backend/setup_database.php`
2. **Check database connection**: Verify MySQL is running and credentials are correct
3. **Test setup**: Visit `http://localhost/my-project/backend/test_connection.php`

#### Issue 3: Database Connection Failed
**Problem**: MySQL not running or wrong credentials.

**Solution**:
1. Check if MySQL is running in XAMPP Control Panel
2. Verify credentials in `backend/config.php`
3. Default XAMPP credentials: username=`root`, password=`` (empty)

#### Issue 4: Table Not Found
**Problem**: Database tables not created.

**Solution**:
1. Run `setup_database.php` to create all tables
2. Check if you have CREATE TABLE privileges
3. Import `backend/create_tables.sql` manually if needed

### Testing Endpoints

Test these URLs to verify everything is working:

- `http://localhost/my-project/backend/test_connection.php` - Check database connection
- `http://localhost/my-project/backend/setup_database.php` - Setup database and tables
- `http://localhost/my-project/admin_login.html` - Admin login page
- `http://localhost/my-project/index.html` - Main page with student/faculty login
- `http://localhost/my-project/messages.html` - Contact messages dashboard

### Debug Information

If you're still having issues:

1. **Check browser console** for JavaScript errors (F12 → Console)
2. **Check browser Network tab** for failed API calls
3. **Check web server error logs**:
   - Windows: `C:\xampp\apache\logs\error.log`
   - macOS: `/Applications/XAMPP/logs/apache_error.log`
   - Linux: `/opt/lampp/logs/apache_error.log`
4. **Test database connection** manually using phpMyAdmin

## 🔒 Security Considerations

### Production Settings
1. **Disable error reporting** in `backend/config.php`:
   ```php
   ini_set('display_errors', 0);
   error_reporting(0);
   ```

2. **Database Security**:
   - Use strong passwords for database users
   - Create a dedicated database user with minimal privileges
   - Consider using environment variables for sensitive data

3. **File Permissions**:
   ```bash
   chmod 644 backend/*.php
   chmod 644 assets/js/*.js
   chmod 644 *.html
   ```

## 📞 Support

If you encounter any issues:

1. Check the troubleshooting section above
2. Verify all prerequisites are installed correctly
3. Ensure XAMPP services are running
4. Check browser console for error messages
5. Verify database connection and table creation

## 🎯 Quick Start Checklist

- [ ] Install MySQL
- [ ] Install XAMPP
- [ ] Start Apache and MySQL services
- [ ] Place project files in htdocs folder
- [ ] Create database `symposium_db`
- [ ] Run `setup_database.php`
- [ ] Test admin login
- [ ] Test student registration
- [ ] Test faculty login

## 📝 License

This project is developed for educational purposes. Feel free to modify and use for your symposium management needs.

---

**Happy Symposium Management! 🎓**
