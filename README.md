## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to start the project
4. Run `docker exec -it racebets-task-php-1 bash` to enter the running container
5. Run `php bin/console app:create-tables` to create tables in database
6. When done, run `docker compose down --remove-orphans` (outside the container) to stop the Docker containers.

## Usage

Use Postman to access the following endpoints:

- `https://localhost/users/add` (POST): Send form-data to create a new user.
- `https://localhost/users/edit/{id}` (POST): Send form-data to update an existing user.
- `https://localhost/users/{id}` (GET): Get info about existing user.

POST form-data example for users:
```
{
    'email': 'test@test.com',
    'firstName': 'Bruce',
    'lastName': 'Wayne',
    'gender': 'male',
    'country': 'US'
}
```
