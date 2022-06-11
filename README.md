# Key Base

Key Base is lab project. In this project, staff may get access in room, if it's have rights. App use MVC scheme.

If you used laravel framework, you must understand how it works😁

---
### Libs which using in project:
- [Phinx](https://book.cakephp.org/phinx/0/en/index.html) - to easy manage the database migrations

### Project structure:
- database/migrations - directory for phinx migrations
- lang - directory for any lang files
- lang/validation.php - messages for validation class
- src/App - core app
- src/Controllers - directory for controller classes
- src/Model - directory for model classes
- src/Services - directory for any service classes
- src/autoload.php - this autoload app classes, functions and composer libs
- src/functions.php - this project functions
- src/routes.php - contains app routes
- templates - files of template
- templates/pages - contains files to display view pages
- phinx.php - database config

Use `php phinx` in terminal, to work with database migrations.