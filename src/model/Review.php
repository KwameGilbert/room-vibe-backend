<?php

namespace App\Model;

use App\Config\Database;

class Review{

    private $conn;
    //Hotel review
    private $table_name = "review";

    public function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createHostelReview($review)
    {
        // Construct the INSERT query. The `id` field is auto-generated.
        $query = "INSERT INTO " . $this->table_name . " 
        (hostel_id, rating, `text`, review_date, review_time)
        VALUES (:hostel_id, :rating, :text, :review_date, :review_time)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters. Make sure the $review array includes the following keys.
        $stmt->bindParam(':hostel_id', $review['hostel_id']);
        $stmt->bindParam(':rating', $review['rating']);
        $stmt->bindParam(':text', $review['text']);
        $stmt->bindParam(':review_date', $review['review_date']);
        $stmt->bindParam(':review_time', $review['review_time']);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getReview($id)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $review =  $stmt->fetch(\PDO::FETCH_ASSOC);
        return $review;
    }

    //Get all reviews of a hostel
    public function getHostelReviews($hostel_id)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE hostel_id = :hostel_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hostel_id', $hostel_id);
        $stmt->execute();

        $reviews = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $reviews;
    }

    public function updateReview($id, $review)
    {
        // Build the UPDATE query. We do not change created_at.
        $query = "UPDATE {$this->table_name} SET 
                rating      = :rating,
                `text`      = :text,
                review_date = :review_date,
                review_time = :review_time,
                updated_at  = NOW()
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind the parameters. The $review array should contain the keys below.
        $stmt->bindParam(':hostel_id', $review['hostel_id']);
        $stmt->bindParam(':rating', $review['rating']);
        $stmt->bindParam(':text', $review['text']);
        $stmt->bindParam(':review_date', $review['review_date']);
        $stmt->bindParam(':review_time', $review['review_time']);
        $stmt->bindParam(':id', $id);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function deleteReview($id)
    {
        $query = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
}