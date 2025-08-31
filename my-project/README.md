# Symposium Management System

A comprehensive web-based symposium management system with student and faculty registration, seminar topic selection, and administrative features.

## Features

### For Students
- **Registration**: Complete registration with personal details and seminar topic selection
- **Login**: Secure login with email and password
- **Dashboard**: View registration details, seminar information, and QR code
- **Seminar Selection**: Choose from 210+ categorized seminar topics

### For Faculty
- **Login**: Department-specific login system
- **Dashboard**: View all registered students with their seminar details
- **Search & Filter**: Search students by name, email, roll number, or filter by seminar topic
- **Statistics**: View registration statistics and trends

### For Administrators
- **Admin Panel**: Manage faculty registrations
- **Add Faculty**: Register new faculty members with all required details
- **System Overview**: View system statistics and user counts

## Database Structure

The system uses MySQL with the following tables:

### `faculty` Table
- id (Primary Key)
- first_name, last_name
- email (Unique)
- contact_number
- faculty_id (Unique)
- password (Hashed)
- department
- created_at, updated_at

### `students` Table
- id (Primary Key)
- first_name, last_name
- email (Unique)
- contact_number
- roll_number (Unique)
- password (Hashed)
- seminar_topic
- seminar_link
- qr_code
- created_at, updated_at

### `seminars` Table
- id (Primary Key)
- topic
- category
- is_active
- created_at

### `admin` Table
- id (Primary Key)
- username (Unique)
- password (Hashed)
- email (Unique)
- created_at

## Setup Instructions

### Prerequisites
- XAMPP (or similar local server with PHP and MySQL)
- Web browser
- Text editor

### Installation Steps

1. **Database Setup**
   ```bash
   # Start XAMPP and ensure Apache and MySQL are running
   # Open phpMyAdmin (http://localhost/phpmyadmin)
   # Create a new database named 'symposium_db'
   # Import the SQL file: backend/create_tables.sql
   ```

2. **Configuration**
   ```bash
   # Edit backend/config.php with your database credentials
   # Update DB_HOST, DB_USERNAME, DB_PASSWORD as needed
   ```

3. **File Structure**
   ```
   my-project/
   ├── index.html              # Main homepage with login/registration
   ├── student_dashboard.html  # Student dashboard
   ├── faculty_dashboard.html  # Faculty dashboard
   ├── admin_login.html        # Admin login page
   ├── admin_panel.html        # Admin panel
   ├── backend/
   │   ├── config.php          # Database configuration
   │   ├── create_tables.sql   # Database schema
   │   ├── faculty_login.php   # Faculty login handler
   │   ├── student_login.php   # Student login handler
   │   ├── student_register.php # Student registration handler
   │   ├── get_seminars.php    # Seminar topics API
   │   ├── get_students.php    # Students data API
   │   └── add_faculty.php     # Add faculty handler
   └── assets/                 # CSS, JS, and images
   ```

4. **Access the System**
   ```
   # Open your web browser and navigate to:
   http://localhost/my-project/
   ```

## Usage Guide

### Student Registration & Login
1. Click "Student Login" on the homepage
2. Choose "Register" tab for new registration
3. Fill in all required fields including seminar topic selection
4. Submit to complete registration
5. Use "Login" tab for existing students

### Faculty Login
1. Click "Faculty Login" on the homepage
2. Enter email, password, and select department
3. Access faculty dashboard to view student registrations

### Admin Access
1. Navigate to "Admin Panel" from footer or directly access `/admin_login.html`
2. Use default credentials: `admin` / `admin123`
3. Add new faculty members through the admin panel

## Seminar Topics

The system includes 210+ seminar topics organized into categories:
- Artificial Intelligence & Machine Learning
- Data Science & Big Data Analytics
- Cybersecurity
- Internet of Things
- Computer Vision
- Natural Language Processing
- Blockchain
- Large Language Models
- And many more...

## Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **Input Validation**: Comprehensive server-side validation
- **SQL Injection Prevention**: Prepared statements for all database queries
- **Session Management**: Secure session handling for logged-in users

## Default Credentials

### Admin
- Username: `admin`
- Password: `admin123`

### Faculty
- Faculty members need to be added through the admin panel
- Use the email and password provided during registration

### Students
- Students register themselves through the student portal
- Use email and password from registration

## API Endpoints

### Authentication
- `POST /backend/faculty_login.php` - Faculty login
- `POST /backend/student_login.php` - Student login
- `POST /backend/student_register.php` - Student registration

### Data Retrieval
- `GET /backend/get_seminars.php` - Get seminar topics
- `GET /backend/get_students.php` - Get students data (faculty only)

### Admin Functions
- `POST /backend/add_faculty.php` - Add new faculty member

## Customization

### Adding New Seminar Topics
1. Edit `backend/create_tables.sql`
2. Add new entries to the seminars table
3. Re-run the SQL script or manually insert new topics

### Modifying Departments
1. Update department options in HTML forms
2. Update database queries if needed
3. Ensure consistency across all forms

### Styling Changes
1. Modify CSS in individual HTML files
2. Update Bootstrap classes as needed
3. Customize the gradient backgrounds and card styles

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check XAMPP is running
   - Verify database credentials in `config.php`
   - Ensure database exists

2. **Form Submission Errors**
   - Check browser console for JavaScript errors
   - Verify PHP error logs
   - Ensure all required fields are filled

3. **Session Issues**
   - Clear browser cache and cookies
   - Check PHP session configuration
   - Verify session storage permissions

### Error Logs
- Check XAMPP error logs: `xampp/apache/logs/error.log`
- Check PHP error logs: `xampp/php/logs/php_error_log`

## Future Enhancements

- Email verification system
- Password reset functionality
- Advanced reporting and analytics
- Mobile-responsive design improvements
- Real-time notifications
- Export functionality for reports
- Multi-language support

## Support

For technical support or questions:
- Check the troubleshooting section above
- Review the code comments for implementation details
- Ensure all prerequisites are properly installed

## License

This project is created for educational purposes. Feel free to modify and use as needed.

---

**Note**: This is a demonstration system. For production use, implement additional security measures, proper error handling, and comprehensive testing.
