# DOCKER + NGINX + PHP + MYSQL + SYMFONY 6.3.8

## Description

The project consists of two containers:
- project container with pre-installed PHP-fpm 8.2 И Symfony 6.3.8
- container with nginx

## Working with the project

Local domain: ``http://localhost:8080``

Installed Xdebug v3.2.0. Configured as standard, no extras. no configuration steps required.

See ``Makefile`` -  the main operations are described there (building the project, launching the project, etc.)

## Project architecture
The project has a primitive architecture:
- Symfony configuration files are located in the ``config`` directory
- configuration files of the docker project image are located inside the ``docker`` directory
- migrations in the ``migrations`` directory
- public project files are located in the ``public`` directory
- it is recommended to add project executable files to the ``src`` directory
- twig templates in the ``templates`` directory

## Project installation
1. Download the project
2. Run the command ``make docker-build`` and ``make docker-exec`` for enter into to container
3. NelmioApiDocBundle  http://localhost:8080/api/doc
4. ???
5. Profit

## Tutorials

```https://symfonycasts.com/```

```https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/tutorials/getting-started.html```

```https://www.binaryboxtuts.com/php-tutorials/symfony-6-json-web-tokenjwt-authentication/```