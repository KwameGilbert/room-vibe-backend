<?php 

namespace App\Model;

use App\Helpers\Database;

class Room{
    private $conn;
    private $table_name = 'rooms';

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createRoom($hostelId){
        $query = "";
    }
}

