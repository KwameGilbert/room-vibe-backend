<?php

namespace App\Controller;

use App\Model\HostelImage;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

class HostelImageController
{
    private $conn;
    private $hostelImage;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->hostelImage = new HostelImage();

        // Set Cloudinary configuration.
        Configuration::instance([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
                'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    // Upload an image for a given hostel ID
    public function uploadImage($hostel_id)
    {
        // Check that a file was uploaded
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return json_encode([
                'error' => 'No image file uploaded or upload error.'
            ] , 400);
        }

        $filePath = $_FILES['image']['tmp_name'];

        try {
            // Upload the image to Cloudinary; store it in a folder for the hostel
            $uploadApi = new UploadApi();
            $result = $uploadApi->upload($filePath, [
                'folder' => "hostels/{$hostel_id}"
            ]);

            // Prepare data to store in the database
            $data = [
                'hostel_id' => $hostel_id,
                'public_id' => $result['public_id'],
                'url'       => $result['secure_url']
            ];

            if($this->hostelImage->createHostelImage($data)){
                return json_encode([
                    'message' => 'Image uploaded successfully.',
                    'data'    => $data
                ]);
            }
        } catch (\Exception $e) {
            return json_encode(
                ['error' => 'Upload failed: '. $e->getMessage()],500);
        }
    }

    // Fetch images for a given hostel ID
    public function getImages($hostel_id)
    {
        $images = $this->hostelImage->getImageByHostelId($hostel_id);
        return json_encode([
            'data' => $images],200);
    }
}
