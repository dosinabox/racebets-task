## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to start the project
4. Run `docker exec -it racebets-task-php-1 bash` to enter the running container
5. Run `php bin/console app:create-tables` to create tables in the database
6. When done, run `docker compose down --remove-orphans` (outside the container) to stop the Docker containers.

## Usage

Use Postman to access the following endpoints:

- `https://localhost/api/v1/users/add` (POST): Send form-data to create a new user.
- `https://localhost/api/v1/users/edit/{id}` (POST): Send form-data to update an existing user.
- `https://localhost/api/v1/users/{id}` (GET): Get info about an existing user.
- `https://localhost/api/v1/transactions/{id}` (POST): Send form-data to create a transaction for the user with the given ID.
- `https://localhost/api/v1/reports` (POST): Send form-data to get the report about the latest transactions (past 7 days by default).

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
POST form-data example for transactions:
```
{
    'type': 'deposit',  //'deposit' or 'withdrawal' only
    'amount': 100       //float values also accepted
}
```
POST form-data example for report:
```
{
    'type': 'transactions',
    'date_start': '2024-03-18',
    'date_end': '2024-03-19'
}
```
