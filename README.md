# php-crud-project
This is a repository of a simple application with silex and angular frameworks, and Javascript and PHP languages

Open your shell linux and...

First, install:

```
Apache2 - sudo apt-get install apache2
        - sudo apt-get install libapache2-mod-php7.0
PHP 7 - sudo apt-get install php7.0
      - sudo apt-get install php7.0-dev
Driver PDO - sudo apt-get install php7.0-mysql
MySQL - sudo apt-get install mysql-server
MongoDB - sudo apt-get install mongodb
Composer - sudo apt-get install composer
```

Now, on folder of your project backend(php-crud-project/back), run:

```
composer install --ignore-platform-reqs
```

Creating database

```
mysql -u root -p (key enter + root password)
create database php_crud_project;
create user 'php-project'@'localhost' identified by 'phpcrud';
grant all privileges on php_crud_project.* to 'php-project'@'localhost';
```

Creating sql and models with propel, 
```
cd php-crud-project/back/vendor/propel/propel/bin
./propel sql:build
./propel model:build
```
