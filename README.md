## Conformite GBP Website

Code repository for Conformite GBP website which is a digital marketing website that provides information about the company and allows users place order for different services.

The project was built using [Laravel 11](https://laravel.com/docs/11.x/) and [Bootstrap 5](https://getbootstrap.com/docs/5.3/getting-started/introduction/).

### Requirements

- PHP 8.2 or higher
- Composer
- Node.js 20.14.0 or higher
- NPM 10.7.0 or higher
- MySQL 5.7
- IDE (Visual Studio Code, PHPStorm, Sublime Text, etc.)

### Initial Setup

- Close the repository from Github to local machine.
- Create a new database in MySQL.
- Copy the `.env.example` file to `.env` file and update the database credentials and other credentials.
- Run `composer install` to install all the dependencies.
- Run `npm install` to install all the node modules.
- Run `npm run dev` to compile the assets.
- Create symbolic link for storage by running `php artisan storage:link`.
- Run `php artisan serve` to start the server.
- Open the browser and go to `http://localhost:8000` to view the website.

### Contribution Guidelines

- All development takes place on the `dev` branch as we follow the [git flow workflow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow).
- Create a new branch from the `dev` branch for any new feature or bug fix.
- Once the feature or bug fix is complete, create a pull request to merge the changes to the `dev` branch.
- Once the changes are merged to the `dev` branch, the changes will be deployed to the staging server for testing.
- Once the changes are tested and approved, the changes will be merged to the `main` branch and deployed to the production server.
- The `main` branch is always the production branch.
- The `dev` branch is always the staging branch.

### Notes

- The `.env` file is not included in the repository. You will need to create a `.env` file in the root directory of the project and copy the contents of the `.env.example` file to the `.env` file.
- Run `npm run dev` to compile the assets when working on the project as application uses Vite for asset compilation.
- In .env, ensure FILESYSTEM_DRIVER=public to enable file uploads.
