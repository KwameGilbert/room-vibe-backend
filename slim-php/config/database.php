<?php


require '../vendor/autoload.php';
require_once '../helpers/Logger.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Database{

    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;
    private $logger;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
       // $this->logger = LoggerFactory::getLogger('Database'); // Initialize the logger
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Log successful connection (optional)
            $this->logger->info(date('Y-m-d H:i:s') . ": Database connection established successfully.");
        } catch (PDOException $exception) {
            // Log connection error
            $this->logger->error("Connection error: " . $exception->getMessage(). " in " . $exception->getFile() . " on line " . $exception->getLine() . " with code " . $exception->getCode(). " at " . date('Y-m-d H:i:s'). "\n \n");
            // Optionally display the error message
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
