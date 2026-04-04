# AutoGridTestBundle: Demo & Functional Test Suite

This bundle provides a collection of practical examples and a comprehensive functional test suite for the [AutoGridBundle](https://github.com/f0ska/auto-grid-bundle).

---

## Quick Setup

To run the demo application or execute the test suite, follow these steps:

1.  **Update Database Schema**:
    ```shell
    php bin/console doctrine:schema:update --force
    ```

2.  **Load Test Data (Fixtures)**:
    ```shell
    php bin/console doctrine:fixtures:load --no-interaction
    ```

3.  **Register Routes**:
    Include the demo routes in your project's `./config/routes.yaml`:
    ```yaml
    f0ska_auto_grid_test:
        resource: '@F0skaAutoGridTestBundle/config/routes.yaml'
    ```

4.  **Access Demos**:
    Navigate to the `/auto-grid` route path in your browser.

5.  **Run Tests**:
    ```shell
    composer test
    ```

---

## Important Notes

*   **Environment Agnostic**: This bundle is designed to work in any standard Symfony environment (`dev`, `test`, etc.). 
*   **Functional Testing**: The test suite uses `WebTestCase`. Ensure your test environment is correctly configured with a database (e.g., in `phpunit.xml` or `.env.test`) to allow these tests to run successfully.
*   **Autoloading**: If you are integrating this bundle into an existing project, ensure the `F0ska\AutoGridTestBundle\Tests` namespace is registered in your `composer.json` `autoload-dev` section.
