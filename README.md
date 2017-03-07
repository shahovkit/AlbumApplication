Album Application on Zend Framework 2.4.11
=======================

Modules
------------
        'ZfcBase'    
        'ZfcUser'   
        'ZfcUserDoctrineORM'
        'DoctrineModule' 
        'DoctrineORMModule'
        'ZendDeveloperTools'
        'BjyAuthorize' 
            

Introduction
------------
1. composer update in /

2. doctrine db connect change /config/autoload/doctrine.local.php

3. ./vendor/bin/doctrine-module orm:schema-tool:update --force OR execute "db_create.sql"

4. Execute SQL Queries /data/SQL/

Authorize administrator
------------
            mail:  mail@mail.ru
        password:  qwerty