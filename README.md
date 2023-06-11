Blog2 - MVC blog implemented from scratch using PHP 8

### How to run?

1. Clone repo
2. run `docker compose up -d` in project root
3. login into app container and run `cd blog2 && composer install`
4. run migrations - `vendor/bin/phinx migrate && vendor/bin/phinx seed:run`
5. open address `http://localhost:8080` in browser