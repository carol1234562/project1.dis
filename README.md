# NightFest

NightFest is a PHP MVC web application for user management and event administration. It supports standard and admin user registration, secure login/logout, profile management, and event CRUD operations using PDO.

## Key Features

- Standard and admin user registration
- Admin registration with optional profile image upload
- Password hashing with `password_hash()` and secure login
- Session-based authentication and logout
- User profile update with optional photo replacement
- Account deletion with uploaded image cleanup
- Event creation, listing, editing, and deletion (admin only)
- Uploaded event image support
- PDO prepared statements for database interaction

## Architecture

The project follows a simple MVC structure:

- `src/Controller/` — request handling and business logic
- `src/view/` — HTML views and UI pages
- `src/assets/` — CSS, JavaScript and image files
- `model/` — database schema and PDO connection helper

## Database and PDO Usage

This project uses PDO for database access in both user and event controllers.

- `model/Db.php` provides a singleton PDO connection for `src/Controller/UserController.php`
- `src/Model/Database.php` provides a singleton PDO connection for `src/Controller/EventController.php`

### User CRUD operations with PDO

- Create: `UserController::register()` inserts a new user into `usuarios`
- Read: `UserController::getUserData($id)` fetches a user by `id`
- Update: `UserController::actualizarPerfil()` updates user name, profile image and password
- Delete: `UserController::deleteAccount($id)` deletes a user and removes the uploaded profile image
- Login: `UserController::login()` selects by email and verifies hashed password

### Event CRUD operations with PDO

- Create: `EventController::createEvent()` inserts new event records into `eventos`
- Read: `EventController::getAllEvents()`, `EventController::getEventById($id)`, `EventController::getTotalEventsCount()`
- Update: `EventController::updateEvent()` updates event fields and optionally replaces the event image
- Delete: `EventController::deleteEvent()` deletes an event and removes the stored image

## File Structure

```
nightfest/
+-- docs/
¦   +-- CONTROLLERS.md
¦   +-- PROJECT_DOCS.md
¦   +-- SETUP.md
+-- model/
¦   +-- Db.php
¦   +-- db.sql
+-- src/
¦   +-- assets/
¦   ¦   +-- css/
¦   ¦   +-- img/
¦   ¦   ¦   +-- uploads/
¦   ¦   +-- jquery.js
¦   +-- Controller/
¦   ¦   +-- EventController.php
¦   ¦   +-- UserController.php
¦   +-- Model/
¦   ¦   +-- Database.php
¦   +-- view/
¦       +-- admin_panel.php
¦       +-- crear_evento.php
¦       +-- discotecas.php
¦       +-- editar_evento.php
¦       +-- editar_perfil.php
¦       +-- footer.php
¦       +-- header.php
¦       +-- infoartista.php
¦       +-- infoevento.php
¦       +-- inicio1.php
¦       +-- login.php
¦       +-- mis_eventos.php
¦       +-- mis_publicaciones.php
¦       +-- perfil.php
¦       +-- registro_admin.php
¦       +-- registro_estandar.php
+-- README.md
```

## Setup Instructions

1. Clone the repository:

```bash
git clone <repository-url>
cd nightfest
```

2. Import the database schema:

```bash
mysql -u <username> -p <database_name> < model/db.sql
```

3. Configure the database connection:

- Edit `model/Db.php`
- Edit `src/Model/Database.php`

Set the host, database name, username, and password for your MySQL environment.

4. Place the project in your webroot:

- Example for XAMPP: `C:\xampp\htdocs\nightfest`

5. Open the app in a browser:

- Apache/XAMPP: `http://localhost/nightfest/src/view/inicio1.php`
- PHP built-in server:

```bash
php -S localhost:8000 -t src
```

## Running the Application

- Access the homepage at `src/view/inicio1.php`
- Register as a standard user or admin
- Log in with email and password
- Use the admin pages to create, edit, or delete events

## Important Files

- `model/Db.php` — PDO connection for user controller
- `src/Model/Database.php` — PDO connection for event controller
- `src/Controller/UserController.php` — handles login, registration, logout, profile update, and account deletion
- `src/Controller/EventController.php` — handles event CRUD and admin authorization
- `model/db.sql` — database schema
- `src/view/` — front-end pages and forms
- `src/assets/` — CSS, JavaScript and image assets

## Notes

- Admin event actions require the `admin` role and are enforced by `EventController::requireAdmin()`.
- User uploads are stored under `src/assets/img/uploads/`.
- Event image uploads are stored under `src/assets/img/`.
- The project currently uses two PDO helper classes, one in `model/Db.php` and one in `src/Model/Database.php`.

## Recommended Improvements

- Consolidate database configuration into a single file or environment variables
- Add CSRF protection to all forms
- Sanitize and validate all form input consistently
- Add a centralized router/front controller
- Harden session handling with secure/httponly cookies
