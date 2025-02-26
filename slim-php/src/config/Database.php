<?php

namespace App\Config;

require '../vendor/autoload.php';

use App\Helpers\LoggerFactory;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Database
{

    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;
    private $logger;

    public function __construct()
    {
        $this->host     = $_ENV['DB_HOST'];
        $this->db_name  = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];

        // Initialize the Monolog logger
        $this->logger = LoggerFactory::getLogger('Database');
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new \PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Log successful connection
            $this->logger->info(date('Y-m-d H:i:s') . " : Database connection established successfully. \n \n");
        } catch (\PDOException $exception) {
            // Log connection error using Monolog
            $this->logger->error("Connection error: " . $exception->getMessage() .
                " in " . $exception->getFile() . " on line " . $exception->getLine() .
                " with code " . $exception->getCode() . " at " . date('Y-m-d H:i:s') . "\n \n");

            // Optionally, you can display the error or handle it differently
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
