# Logistics API Documentation

This document describes the API endpoints for the warehouse, shipment, and parcel management system.

## Overview

The logistics system consists of three main entities:
- **Warehouse**: Storage facilities where shipments are processed
- **Shipment**: A collection of parcels being transported, must belong to a warehouse
- **Parcel**: Individual items in a shipment, must belong to a shipment

## Relationships and Validations

### Warehouse → Shipment
- Each shipment **must** belong to a valid warehouse
- When creating/updating a shipment, the system validates that the warehouse exists
- If the warehouse doesn't exist, the operation fails with an error message

### Shipment → Parcel
- Each parcel **must** belong to a valid shipment
- When creating/updating a parcel, the system validates that the shipment exists
- Parcels **cannot** be created without a shipment_id
- Parcels **cannot** have their shipment_id set to null
- If the shipment doesn't exist, the operation fails with an error message

## API Endpoints

### Warehouse Endpoints

#### Create Warehouse
```
POST /api/warehouse
Content-Type: application/json

{
  "name": "Main Warehouse",
  "location": "Accra",
  "address": "123 Industrial Area",
  "capacity": 10000,
  "description": "Primary storage facility"
}

Response:
{
  "status": true,
  "message": "Warehouse created successfully",
  "data": {
    "id": 1
  }
}
```

#### Get Warehouse by ID
```
GET /api/warehouse/{id}

Response:
{
  "status": true,
  "warehouse": {
    "id": 1,
    "name": "Main Warehouse",
    "location": "Accra",
    "address": "123 Industrial Area",
    "capacity": 10000,
    "description": "Primary storage facility",
    "created_at": "2025-10-20 15:00:00",
    "updated_at": "2025-10-20 15:00:00"
  }
}
```

#### Get All Warehouses
```
GET /api/warehouse

Response:
{
  "status": true,
  "warehouses": [...]
}
```

#### Update Warehouse
```
PUT /api/warehouse/{id}
Content-Type: application/json

{
  "name": "Updated Warehouse",
  "location": "Kumasi",
  "address": "456 New Location",
  "capacity": 15000,
  "description": "Updated description"
}
```

#### Delete Warehouse
```
DELETE /api/warehouse/{id}
```

### Shipment Endpoints

#### Create Shipment
```
POST /api/shipment
Content-Type: application/json

{
  "warehouse_id": 1,
  "origin": "Accra",
  "destination": "Lagos",
  "shipment_date": "2025-10-20 10:00:00",
  "delivery_date": "2025-10-25 16:00:00",
  "status": "pending",
  "notes": "Fragile items"
}

Response (Success):
{
  "status": true,
  "message": "Shipment created successfully",
  "data": {
    "id": 1,
    "shipment_number": "SHP-ABC123XYZ"
  }
}

Response (Validation Error):
{
  "status": false,
  "message": "Warehouse does not exist"
}
```

#### Get Shipment by ID
```
GET /api/shipment/{id}

Response:
{
  "status": true,
  "shipment": {
    "id": 1,
    "warehouse_id": 1,
    "warehouse_name": "Main Warehouse",
    "warehouse_location": "Accra",
    "shipment_number": "SHP-ABC123XYZ",
    "origin": "Accra",
    "destination": "Lagos",
    "shipment_date": "2025-10-20 10:00:00",
    "delivery_date": "2025-10-25 16:00:00",
    "status": "pending",
    "notes": "Fragile items",
    "created_at": "2025-10-20 15:00:00",
    "updated_at": "2025-10-20 15:00:00"
  }
}
```

#### Get All Shipments
```
GET /api/shipment

Response:
{
  "status": true,
  "shipments": [...]
}
```

#### Get Shipments by Warehouse ID
```
GET /api/shipment/warehouse/{warehouse_id}

Response:
{
  "status": true,
  "shipments": [...]
}
```

#### Update Shipment
```
PUT /api/shipment/{id}
Content-Type: application/json

{
  "warehouse_id": 1,
  "origin": "Accra",
  "destination": "Abidjan",
  "shipment_date": "2025-10-20 10:00:00",
  "delivery_date": "2025-10-26 16:00:00",
  "status": "in_transit",
  "notes": "Updated notes"
}

Response (Validation Error):
{
  "status": false,
  "message": "Warehouse does not exist"
}
```

#### Delete Shipment
```
DELETE /api/shipment/{id}
```

### Parcel Endpoints

#### Create Parcel
```
POST /api/parcel
Content-Type: application/json

{
  "shipment_id": 1,
  "description": "Electronics package",
  "weight": 5.5,
  "dimensions": "30x20x15 cm",
  "status": "pending",
  "sender_name": "John Doe",
  "recipient_name": "Jane Smith"
}

Response (Success):
{
  "status": true,
  "message": "Parcel created successfully",
  "data": {
    "id": 1,
    "tracking_number": "TRK-XYZ789ABC123"
  }
}

Response (Validation Error - Missing shipment_id):
{
  "status": false,
  "message": "Shipment ID is required - all parcels must belong to a shipment"
}

Response (Validation Error - Invalid shipment):
{
  "status": false,
  "message": "Shipment does not exist"
}
```

#### Get Parcel by ID
```
GET /api/parcel/{id}

Response:
{
  "status": true,
  "parcel": {
    "id": 1,
    "shipment_id": 1,
    "shipment_number": "SHP-ABC123XYZ",
    "origin": "Accra",
    "destination": "Lagos",
    "shipment_status": "in_transit",
    "tracking_number": "TRK-XYZ789ABC123",
    "description": "Electronics package",
    "weight": "5.50",
    "dimensions": "30x20x15 cm",
    "status": "pending",
    "sender_name": "John Doe",
    "recipient_name": "Jane Smith",
    "created_at": "2025-10-20 15:00:00",
    "updated_at": "2025-10-20 15:00:00"
  }
}
```

#### Get All Parcels
```
GET /api/parcel

Response:
{
  "status": true,
  "parcels": [...]
}
```

#### Get Parcels by Shipment ID
```
GET /api/parcel/shipment/{shipment_id}

Response:
{
  "status": true,
  "parcels": [...]
}
```

#### Update Parcel
```
PUT /api/parcel/{id}
Content-Type: application/json

{
  "shipment_id": 1,
  "description": "Updated description",
  "weight": 6.0,
  "dimensions": "32x22x16 cm",
  "status": "in_transit",
  "sender_name": "John Doe",
  "recipient_name": "Jane Smith"
}

Response (Validation Error - Empty shipment_id):
{
  "status": false,
  "message": "Shipment ID cannot be empty - all parcels must belong to a shipment"
}

Response (Validation Error - Invalid shipment):
{
  "status": false,
  "message": "Shipment does not exist"
}
```

#### Delete Parcel
```
DELETE /api/parcel/{id}
```

## Database Schema

### Warehouse Table
```sql
CREATE TABLE `warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);
```

### Shipment Table
```sql
CREATE TABLE `shipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `shipment_number` varchar(50) NOT NULL,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `shipment_date` datetime NOT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipment_number` (`shipment_number`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
```

### Parcel Table
```sql
CREATE TABLE `parcel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` int(11) NOT NULL,
  `tracking_number` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `sender_name` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracking_number` (`tracking_number`),
  KEY `shipment_id` (`shipment_id`),
  CONSTRAINT `parcel_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
```

## Setup Instructions

1. Run the SQL script to create the tables:
   ```bash
   mysql -u username -p database_name < frontend/manager/logistics_schema.sql
   ```

2. The routes are automatically loaded in `src/routes/api.php`

3. Ensure your database connection is configured in `src/config/Database.php`

## Error Handling

All endpoints return consistent error responses:

```json
{
  "status": false,
  "message": "Error description"
}
```

Common HTTP status codes:
- `200`: Success
- `201`: Created
- `400`: Validation error
- `404`: Resource not found
- `500`: Server error
