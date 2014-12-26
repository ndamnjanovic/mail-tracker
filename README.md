mail-tracker
============

## Installation

- Clone the repository
- Install dependencies (from console)
    - run ```composer install```
- To set permissions: ```chmod -R 777 app/storage && chmod -R 777 public/uploads```
- Create database: ```mysqladmin -u root -p password YOUR PASSWORD create NAME_FROM_DATABASE_CONFIG```
- Run migrations: ```php artisan migrate```
- Run seeder: ```php artisan db:seed```

Note:
Create config file for Artdarek package. Add new constant for Google Service that will handle Reports API.