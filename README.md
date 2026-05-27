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

### JWT Authentication

The API is protected using JWT (JSON Web Token). Two roles are implemented: `ROLE_ADMIN` and `ROLE_TECH`.

- **Login Endpoint:** `POST /api/login`
- **Default Credentials:**
  - **Administrator:** `admin@vending.com` / `admin123`
  - **Technician:** `tech@vending.com` / `tech123`
- **Role Hierarchy:** `ROLE_ADMIN` inherits all permissions from `ROLE_TECH`.
- **How to use:**
  1. Send a POST request to `/api/login` with the credentials.
  2. Receive a `token` in the response.
  3. Include this token in the `Authorization` header for subsequent requests:
     `Authorization: Bearer <your_token>`

### Useful Commands

- **Seed Initial Users:**
  ```bash
  docker-compose exec php bin/console app:seed-users
  ```
- **Run Tests:**
  ```bash
  docker-compose exec php bin/phpunit
  ```
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
