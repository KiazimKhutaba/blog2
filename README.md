Blog2 - MVC blog implemented from scratch using PHP 8

### How to run?

1. Clone repo
2. run `docker compose up -d` in project root
3. login into app container (`docker exec -it blog-php /bin/bash`) and run `su dapp && composer install`
4. run migrations - `chmod +x ./init.sh && ./init.sh`
5. open address `http://localhost:8080` in browser