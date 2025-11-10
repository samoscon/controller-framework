# controllerFramework
MVC controller Framework based on book of M. Zandstra PHP 8 Objects, Patterns, and Practise  Resources

Steps to deploy the basic framework in Apache2 web server with Oracle (or MariaDB) database running in a Linux environment:

1. Create a root folder for your project with the name [Your Name of the project root folder]
2. Execute in your project root folder "composer init". Add following dependencies to your composer.json:

    "require": {
        "samoscon/controller-framework": "^1.0"
    }

3. Execute in your project root folder "composer install".
4. Copy the files and folders under ./vendor/samoscon/controller-framework/example/ to your root folder
5. Set-up a datebase with the DatabaseSetup.sql to set-up your members table
6. Update the ./config/app_options.ini file with your passwords and settings
7. Insert manually a first member with a [name], [email], role = "A", active = "1", subscriptionuntil = "2099-12-31" 
        (no password required, as you will set-up a password during your first login) in your database

AND YOUR READY TO TEST AND DEVELOP YOUR OWN PROJECT
