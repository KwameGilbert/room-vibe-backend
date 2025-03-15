<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../helpers/LoggerFactory.php';

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
        $this->logger = new LoggerFactory('Database');
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
            $this->logger->getLogger()->info(date('Y-m-d H:i:s') . " : Database connection established successfully. \n \n");
        } catch (\PDOException $exception) {
            // Log connection error using Monolog
            $this->logger->getLogger()->error("\n Connection error: " . $exception->getMessage() .
                "\n in " . $exception->getFile() . "\n on line " . $exception->getLine() .
                "\n with code " . $exception->getCode() . "\n at " . date('Y-m-d H:i:s') . "\n \n");

            // Optionally, you can display the error or handle it differently
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}