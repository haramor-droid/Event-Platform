# Development Environment Setup

How to run the platform locally on Windows.

## Tools

- XAMPP with PHP 8.3
- VS Code
- GitHub Desktop

## Steps

1. Install XAMPP to `C:\xampp` (keep Apache, MySQL, PHP and phpMyAdmin).
2. In `C:\xampp\php\php.ini`, enable `extension=gd` and `extension=zip`, and set `date.timezone=Africa/Windhoek`. Restart Apache.
3. In phpMyAdmin, set a root password. Then set `auth_type` to `cookie` in `C:\xampp\phpMyAdmin\config.inc.php`.
4. In `C:\xampp\mysql\bin\my.ini`, add `default-time-zone='+02:00'` under `[mysqld]`. Restart MySQL.
5. In phpMyAdmin, create a database called `event_platform` and a user called `event_app` with SELECT, INSERT, UPDATE and DELETE on that database only.
6. Put the repository in `C:\xampp\htdocs\Event-Platform`.
7. Copy `config/config.example.php` to `config/config.php` and enter the `event_app` password. Never commit `config.php`.
8. Start Apache and MySQL, then open `http://localhost/Event-Platform/public/`.

## Live server notes

- Only the `public` folder should be reachable from the web.
- Turn off directory listing. If not, it allows free access to all folders via browser
- Do not allow PHP to run in `public/uploads`. This is meant for pictures/videos and etc and if someone uploads a php file they can control the website.
