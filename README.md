## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to start the project
4. Run `docker exec -it racebets-task-php-1 bash` to enter the running container
5. Run `php bin/console app:create-tables` to create tables in database
6. Run `docker compose down --remove-orphans` to stop the Docker containers.
