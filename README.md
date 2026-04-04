# AutoGridTestBundle: Demo & Functional Test Suite

This repository serves as a collection of practical examples and the comprehensive functional test suite for the [AutoGridBundle](https://github.com/f0ska/auto-grid-bundle).

---

## Setup for Demos & Tests

To run the demo application and execute the test suite, follow these steps:

1.  **Update Test Database Schema**:
    Ensure your test database (`var/test.db` for SQLite) has the latest schema.
    ```shell
    php bin/console doctrine:schema:update --force --env=test
    ```

2.  **Load Test Data (Fixtures)**:
    Populate your test database with sample data for the demos and tests.
    ```shell
    php bin/console doctrine:fixtures:load --env=test --no-interaction
    ```

3.  **Add Demo Routes**:
    Include the demo application's routes in your project's `./config/routes.yaml`:
    ```yaml
    f0ska_auto_grid_test:
        resource: '@F0skaAutoGridTestBundle/config/routes.yaml'
    ```

4.  **Access the Demo Application**:
    Navigate to the `/auto-grid` route path in your browser (e.g., `http://localhost:8000/auto-grid`).

5.  **Run the Test Suite**:
    Execute the functional tests to verify the bundle's features:
    ```shell
    composer test
    ```
    (This command uses the `test` script defined in `composer.json`, which runs `vendor/bin/phpunit`.)

---

## Important Notes:

*   This bundle is intended for demonstration and testing purposes within the `symfony-docker` project. It is not meant to be installed as a standalone Composer dependency in other projects.
*   The test environment uses an SQLite database (`var/test.db`) for isolation and speed. Ensure your `phpunit.dist.xml` and `.env.test` are correctly configured as per the project's setup.
