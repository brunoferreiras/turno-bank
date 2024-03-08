# TurnoBank

## Gettings Started

### Prerequisites
It's need to install in your computer:
`Docker: 20+`

## Tools
- [Laravel 10](https://laravel.com/)
- [MySQL 8](https://www.mysql.com/)
- [Docker](https://www.docker.com/)
- [Redis 6](https://redis.io/)

Execute this commands to start the application:
```bash
// Create the .env file
cp .env.example .env
// Start all containers docker
make up
// Enter in container
make bash
// Generate the key (inside the container)
php artisan key:generate
// Run the migrations (inside the container)
php artisan migrate
// It's everything!
// You can access the api in: http://localhost:8840/
```

### Migrations
```bash
// Create migrations (First time)
php artisan migrate --seed
// Drop/Create migrations with seeds
php artisan migrate:fresh --seed
// Install database without datas
php artisan migrate:fresh
```

### Tests
```bash
// Run the tests (inside the container), you can use the make bash command to enter in the container
composer run test

// Run the tests with coverage
composer run test:cov
```

### API Documentation (Postman)
Import the file `docs/TurnoBank.postman_collection.json` in your Postman.

The collection have two basic variables:
- `BASE_URL`: The base url of the API, default is `http://localhost:8840/`
- `JWT_TOKEN`: The token to access the API, you can get it in the login endpoint. This token is automatically set in the `Authorization` header when you login in the API.

### Credentials to sign in of Admin:

- Endpoint UI: [turnobank.brunoferreiras.dev/login](http://turnobank.brunoferreiras.dev/login)
- Endpoint API: [turno-api.brunoferreiras.dev/api/auth/login](http://turno-api.brunoferreiras.dev/api/auth/login)

```bash
Username: admin
Password: password
```
