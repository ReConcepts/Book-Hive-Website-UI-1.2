<?php

//Change host name, database type, database, username, password
require "Database.php";

Database::setUp(array(
    'dsn' => 'mysql:host=localhost;dbname=bookhive_v1.0;',
    'username' => 'root',
    'password' => 'sogoni1608'
));

