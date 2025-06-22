# Crew Experts Website

This project contains the source code for the Crew Experts website and administration panel.

## Requirements

- Web server with PHP 8.0 or newer
- MySQL or MariaDB database
- Ability to set file permissions (for uploads and logs)

## Initial Setup

1. Upload all repository files to your web hosting account. The document root should point to the project directory so that `index.php` is accessible.
2. Create a MySQL database and user. Grant the user all privileges for this database.
3. Edit `includes/config.php` and set `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` and `SITE_URL` to match your environment.
4. Give the web server write access to `assets/uploads/` and the `logs/` folder.
5. Open `setup_db.php` in your browser to create the required tables and a default administrator account (`admin` / `admin123`).
6. **Delete `setup_db.php` after it finishes** to keep your site secure.
7. Log in to the admin area at `/admin` using the default account and change the password immediately.
8. Configure site settings and add content through the administration panel.

## Updating

When updating the site:

1. Upload the new files, keeping your existing `includes/config.php` and uploaded assets.
2. If the update includes database changes, run the updated `setup_db.php` again or apply the migrations manually.

## Support

For issues or questions please contact the maintainer via the email specified in the site settings.
