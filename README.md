# Laravel API Starter

A small, production-minded REST API starter built with **Laravel 13**, **Sanctum token auth**,
**MySQL**, **Docker** and **GitHub Actions CI**. It ships with a complete example resource (tasks)
so you can see how authentication, validation, authorization, pagination and tests fit together.

![CI](https://github.com/rightturn/laravel-api-starter/actions/workflows/ci.yml/badge.svg)

## Features
- Token authentication (register, login, logout, `me`) with Laravel Sanctum
- `tasks` CRUD with request validation, API resources and pagination
- Ownership rules via a policy: users can only see and change their own tasks
- Filter by status: `GET /api/tasks?status=done`
- JSON error responses for every `/api/*` route
- Login/register rate limiting
- 10 feature tests (auth, validation, ownership, filtering)
- Docker Compose setup (app + MySQL 8.4) and a CI workflow that runs the tests

## API

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/api/register` | no | Create an account, returns a token |
| POST | `/api/login` | no | Log in, returns a token |
| GET | `/api/me` | yes | Current user |
| POST | `/api/logout` | yes | Revoke the current token |
| GET | `/api/tasks` | yes | List your tasks (15 per page, `?status=`) |
| POST | `/api/tasks` | yes | Create a task |
| GET | `/api/tasks/{id}` | yes | Show one of your tasks |
| PATCH/PUT | `/api/tasks/{id}` | yes | Update a task |
| DELETE | `/api/tasks/{id}` | yes | Delete a task |

Task fields: `title` (required), `description`, `status` (`todo`, `in_progress`, `done`), `due_date`.
Send the token as `Authorization: Bearer <token>` and `Accept: application/json`.

## Run with Docker
```bash
export APP_KEY=base64:$(openssl rand -base64 32)
docker compose up --build
```
The API is then at `http://localhost:8000` (set `APP_PORT` to use another port).

```bash
curl -X POST localhost:8000/api/register -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"name":"Demo","email":"demo@example.com","password":"password123"}'
```

## Run the tests
```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan test
```
Tests use an in-memory SQLite database, so no MySQL is needed.

## Project layout
```
app/Http/Controllers/Api/   AuthController, TaskController
app/Http/Requests/          StoreTaskRequest (validation)
app/Http/Resources/         TaskResource (JSON shape)
app/Policies/               TaskPolicy (ownership)
tests/Feature/              AuthTest, TaskApiTest
.github/workflows/ci.yml    Runs the test suite on every push and pull request
```

## License
MIT
