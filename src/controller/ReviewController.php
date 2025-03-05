<?php

namespace App\Controller;

use App\Model\Review;

class ReviewController{
    private $review;
    public function __construct(){
        $this->review = new Review();
    }

    public function createHostelReview($data){
        if($this->review->createHostelReview($data)){
            return json_encode([
                "status" => true,
                "message" => "Review created successfully.",
                "data" => $data
            ], 201);
        } else {
            return json_encode([
                "message" => "Failed to create review."
            ], 500);
        }
    }

    // Read a single review by Id
    public function getReview($id){
        $review = $this->review->getReview($id);
        if($review){
            return json_encode([
                "status" => true,
                "review" => $review
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to fetch review."
            ], 500);
        }
    }

    //Get all reviews of a hostel
    public function getHostelReviews($hostel_id){
        $reviews = $this->review->getHostelReviews($hostel_id);
        if($reviews){
            return json_encode([
                "status" => true,
                "reviews" => $reviews
            ], 200);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Failed to fetch reviews."
            ], 500);
        }
    }

    // Update a hostel review
    public function updateReview($id, $data){
        if($this->review->updateReview($id, $data)){
            return json_encode([
                "status" => true,
                "message" => "Review updated successfully.",
                "data" => $data
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to update review."
            ], 500);
        }
    }

    //Delete review
    public function deleteReview($id){
        if($this->review->deleteReview($id)){
            return json_encode([
                "status" => true,
                "message" => "Review deleted successfully."
            ], 200);
        } else {
            return json_encode([
                "message" => "Failed to delete review."
            ], 500);
        }
    }
}