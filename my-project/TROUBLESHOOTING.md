# Troubleshooting Guide

## Issues and Solutions

### Issue 1: "Failed to add faculty member: Unauthorized access"

**Problem**: Admin authentication mismatch between frontend and backend.

**Solution**: 
1. The admin login now uses proper backend authentication. Make sure to:
   - Run the database setup first (see below)
   - Use the admin login page at `admin_login.html`
   - Login with username: `admin` and password: `admin123`

### Issue 2: "Loading seminar topics..." - No topics appear

**Problem**: Database not set up or seminars table empty.

**Solution**:
1. **First, set up the database**:
   - Open your web browser
   - Navigate to: `http://localhost/your-project-folder/backend/setup_database.php`
   - This will create the database, tables, and populate seminar topics
   - You should see success messages for each step

2. **If setup fails**:
   - Check that MySQL/XAMPP is running
   - Verify database credentials in `backend/config.php`
   - Make sure the `Seminar Topics List.txt` file exists in the project root

3. **Test the setup**:
   - Visit: `http://localhost/your-project-folder/backend/test_seminars.php`
   - This should show database connection status and seminar count

## Quick Setup Steps

1. **Start your web server** (XAMPP/WAMP/etc.)
2. **Run database setup**: Visit `backend/setup_database.php` in your browser
3. **Test admin login**: Visit `admin_login.html`
4. **Test student registration**: Visit `index.html`

## Common Database Issues

### Connection Failed
- Check if MySQL is running
- Verify credentials in `backend/config.php`
- Default XAMPP credentials: username=`root`, password=`` (empty)

### Table Not Found
- Run `setup_database.php` to create all tables
- Check if you have CREATE TABLE privileges

### No Data in Seminars
- The setup script reads from `Seminar Topics List.txt`
- Make sure this file exists in the project root
- Run `setup_database.php` again if needed

## Testing Endpoints

Test these URLs to verify everything is working:

- `backend/test_seminars.php` - Check database and seminar count
- `backend/get_seminars.php` - Get seminar topics (should return JSON)
- `admin_login.html` - Admin login page
- `index.html` - Main page with student/faculty login
- `debug_seminars.html` - **NEW**: Debug page to test seminar loading with detailed console output

## Debugging Seminar Loading Issue

If seminar topics are still not appearing:

1. **Use the debug page**: Visit `debug_seminars.html` in your browser
2. **Check browser console**: Open Developer Tools (F12) and look for errors
3. **Test endpoints**: Use the debug page buttons to test each endpoint
4. **Check network tab**: Look for failed requests in the Network tab

Common issues:
- **CORS errors**: Check if your web server is properly configured
- **Path issues**: Make sure the `backend/` folder is accessible
- **Database connection**: Verify MySQL is running and credentials are correct

## Debug Information

If you're still having issues:

1. Check browser console for JavaScript errors
2. Check browser Network tab for failed API calls
3. Check your web server error logs
4. Test database connection manually using phpMyAdmin

## Default Credentials

- **Admin**: username=`admin`, password=`admin123`
- **Database**: username=`root`, password=`ayush` (as per your config)
