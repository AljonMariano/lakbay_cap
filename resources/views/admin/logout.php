<?php
    $title_page = 'LAKBAY Reservation System';
    include_once "includes/header.php";
    include_once "config/database.php";
    include_once "classes/authentication.php";

    $database = new Database();
    $connection = $database->getConnection();

    $authentication = new Authentication($connection);
    $authentication->logout();
    if(isset($alert)){
        echo "<div class='alert alert-info alert-dismissible fade show py-1 px-4 d-flex justify-content-between align-items-center' role='alert'><span>&#8505; &nbsp; User successfully logged out!</span><button type='button' class='btn fs-4 py-0 px-0' data-bs-dismiss='alert' aria-label='Close'>&times;</button></div>";
    }
?>