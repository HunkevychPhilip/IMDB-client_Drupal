# Requirements
* Composer
* PHP 7.3 or higher
* Apache 2.4.7 or higher
* Supported databases: MySQL or Percona 5.7.8., MariaDB 10.3.7, SQLite 3.26, PostgreSQL 10

# How to install (with XAMPP)
* `fork` your own copy of repository to your account
* `clone` to your PC
* `cd` into cloned repository folder
* execute `composer install`
* open `phpmyadmin` with Apache and MySQL running
* click on the `import` tab
* choose the `initial-dump.sql` file from your cloned repository folder and click on the `go` button (wait until db creating is done)
* again `cd` into cloned repository folder
* execute `vendor/bin/drush cim`, then type `yes` (wait until configs importing is done)
* now you can open Drupal site on your local web server (log: admin, pass: admin)

# If you encounter theme-loading problem:
* find the `log` button with ctrl-F
* login as an admin (log: admin, pass: admin)
* go to the `Appearance` tab
* press the `Settings` button under the `Movie Barrio` theme
* scroll down and hit the `Save configs` button
