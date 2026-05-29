# Vending Machine API

This project is a Symfony-based API that models a vending machine and its state during operation.

## Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting Started

1.  **Clone the repository:**

    ```bash
    git clone <repository-url>
    cd vending-machine
    ```

2.  **Environment Setup:**

    The project uses a default `.env` file. For local development, you can create a `.env.local` if you need to override any configuration (like database credentials).

3.  **Build and Start the Containers:**

    ```bash
    docker-compose up -d --build
    ```

    This command will build the PHP image and start the Nginx, PHP-FPM, and MySQL services.

4.  **Install Dependencies:**

    Once the containers are running, install the PHP dependencies:

    ```bash
    docker-compose exec php composer install
    ```

5.  **Database Setup:**

    By default, the project is configured to use SQLite. If you wish to use the provided MySQL service, update your `DATABASE_URL` in `.env` and run migrations:

    ```bash
    docker-compose exec php bin/console doctrine:migrations:migrate
    ```

## Usage

The application will be accessible at: [http://localhost:8000](http://localhost:8000)

### Useful Commands

- **Seed Initial Users:**
  ```bash
  docker-compose exec php bin/console app:seed-users
  ```
- **Seed Initial Products:**
  ```bash
  docker-compose exec php bin/console app:seed-products
  ```
- **Run Tests:**
  ```bash
  docker-compose exec php bin/phpunit
  ```

## API Testing
You can use the provided Postman collection located in `docs/postman/VendingMachine.postman_collection.json` to test the API endpoints.

- **Symfony Console:**
  ```bash
  docker-compose exec php bin/console <command>
  ```
- **Stop Containers:**
  ```bash
  docker-compose down
  ```

## License

This project is licensed under the Apache 2.0 License.
