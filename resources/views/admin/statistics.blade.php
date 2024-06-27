<?php
class Statistics{
    private $connection;

    public function __construct($database){
        $this->connection = $database;
    }

    public function getStat($table, $field, $value){
        $querystring = "SELECT count('$field') as count FROM $table ";
        $querystring .= "WHERE $field LIKE '%$value%'";
        $statement = $this->connection->query($querystring);
        return $statement->fetch();
    }
}
?>