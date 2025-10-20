# Logistics API Usage Examples

This document provides practical examples of using the logistics API endpoints.

## Scenario 1: Creating a Complete Shipment Flow

### Step 1: Create a Warehouse

```bash
curl -X POST http://localhost:8080/api/warehouse \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Central Distribution Center",
    "location": "Accra",
    "address": "123 Industrial Area, Accra",
    "capacity": 50000,
    "description": "Main warehouse for West Africa operations"
  }'
```

**Response:**
```json
{
  "status": true,
  "message": "Warehouse created successfully",
  "data": {
    "id": 1
  }
}
```

### Step 2: Create a Shipment (Linked to Warehouse)

```bash
curl -X POST http://localhost:8080/api/shipment \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": 1,
    "origin": "Accra, Ghana",
    "destination": "Lagos, Nigeria",
    "shipment_date": "2025-10-21 08:00:00",
    "delivery_date": "2025-10-25 17:00:00",
    "status": "pending",
    "notes": "Temperature controlled shipment - Keep below 25°C"
  }'
```

**Response:**
```json
{
  "status": true,
  "message": "Shipment created successfully",
  "data": {
    "id": 1,
    "shipment_number": "SHP-A1B2C3D4E5"
  }
}
```

### Step 3: Add Parcels to the Shipment

```bash
# Parcel 1
curl -X POST http://localhost:8080/api/parcel \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": 1,
    "description": "Electronics - Laptop computers (10 units)",
    "weight": 25.5,
    "dimensions": "60x40x30 cm",
    "status": "pending",
    "sender_name": "TechCorp Ghana Ltd",
    "recipient_name": "Electronics Warehouse Lagos"
  }'

# Parcel 2
curl -X POST http://localhost:8080/api/parcel \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": 1,
    "description": "Medical supplies - Temperature sensitive",
    "weight": 15.0,
    "dimensions": "45x35x25 cm",
    "status": "pending",
    "sender_name": "MedSupply Ghana",
    "recipient_name": "Lagos General Hospital"
  }'
```

**Response for each parcel:**
```json
{
  "status": true,
  "message": "Parcel created successfully",
  "data": {
    "id": 1,
    "tracking_number": "TRK-X1Y2Z3A4B5C6"
  }
}
```

## Scenario 2: Validation Examples

### Example 1: Try to create shipment with non-existent warehouse

```bash
curl -X POST http://localhost:8080/api/shipment \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": 999,
    "origin": "Accra",
    "destination": "Lagos",
    "shipment_date": "2025-10-21 08:00:00"
  }'
```

**Response (Error):**
```json
{
  "status": false,
  "message": "Warehouse does not exist"
}
```

### Example 2: Try to create parcel without shipment_id

```bash
curl -X POST http://localhost:8080/api/parcel \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Test package",
    "weight": 5.0
  }'
```

**Response (Error):**
```json
{
  "status": false,
  "message": "Shipment ID is required - all parcels must belong to a shipment"
}
```

### Example 3: Try to create parcel with non-existent shipment

```bash
curl -X POST http://localhost:8080/api/parcel \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": 999,
    "description": "Test package",
    "weight": 5.0
  }'
```

**Response (Error):**
```json
{
  "status": false,
  "message": "Shipment does not exist"
}
```

## Scenario 3: Querying Data

### Get all shipments for a warehouse

```bash
curl http://localhost:8080/api/shipment/warehouse/1
```

**Response:**
```json
{
  "status": true,
  "shipments": [
    {
      "id": 1,
      "warehouse_id": 1,
      "warehouse_name": "Central Distribution Center",
      "warehouse_location": "Accra",
      "shipment_number": "SHP-A1B2C3D4E5",
      "origin": "Accra, Ghana",
      "destination": "Lagos, Nigeria",
      "shipment_date": "2025-10-21 08:00:00",
      "delivery_date": "2025-10-25 17:00:00",
      "status": "pending",
      "notes": "Temperature controlled shipment - Keep below 25°C",
      "created_at": "2025-10-20 15:30:00",
      "updated_at": "2025-10-20 15:30:00"
    }
  ]
}
```

### Get all parcels for a shipment

```bash
curl http://localhost:8080/api/parcel/shipment/1
```

**Response:**
```json
{
  "status": true,
  "parcels": [
    {
      "id": 1,
      "shipment_id": 1,
      "shipment_number": "SHP-A1B2C3D4E5",
      "origin": "Accra, Ghana",
      "destination": "Lagos, Nigeria",
      "shipment_status": "pending",
      "tracking_number": "TRK-X1Y2Z3A4B5C6",
      "description": "Electronics - Laptop computers (10 units)",
      "weight": "25.50",
      "dimensions": "60x40x30 cm",
      "status": "pending",
      "sender_name": "TechCorp Ghana Ltd",
      "recipient_name": "Electronics Warehouse Lagos",
      "created_at": "2025-10-20 15:35:00",
      "updated_at": "2025-10-20 15:35:00"
    },
    {
      "id": 2,
      "shipment_id": 1,
      "shipment_number": "SHP-A1B2C3D4E5",
      "origin": "Accra, Ghana",
      "destination": "Lagos, Nigeria",
      "shipment_status": "pending",
      "tracking_number": "TRK-M1N2O3P4Q5R6",
      "description": "Medical supplies - Temperature sensitive",
      "weight": "15.00",
      "dimensions": "45x35x25 cm",
      "status": "pending",
      "sender_name": "MedSupply Ghana",
      "recipient_name": "Lagos General Hospital",
      "created_at": "2025-10-20 15:36:00",
      "updated_at": "2025-10-20 15:36:00"
    }
  ]
}
```

## Scenario 4: Updating Records

### Update shipment status to in-transit

```bash
curl -X PUT http://localhost:8080/api/shipment/1 \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": 1,
    "origin": "Accra, Ghana",
    "destination": "Lagos, Nigeria",
    "shipment_date": "2025-10-21 08:00:00",
    "delivery_date": "2025-10-25 17:00:00",
    "status": "in_transit",
    "notes": "Temperature controlled shipment - Keep below 25°C. En route to Lagos."
  }'
```

**Response:**
```json
{
  "status": true,
  "message": "Shipment updated successfully"
}
```

### Update parcel status

```bash
curl -X PUT http://localhost:8080/api/parcel/1 \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": 1,
    "description": "Electronics - Laptop computers (10 units)",
    "weight": 25.5,
    "dimensions": "60x40x30 cm",
    "status": "in_transit",
    "sender_name": "TechCorp Ghana Ltd",
    "recipient_name": "Electronics Warehouse Lagos"
  }'
```

**Response:**
```json
{
  "status": true,
  "message": "Parcel updated successfully"
}
```

## Scenario 5: Moving a Parcel to a Different Shipment

**Note:** This demonstrates that parcels can be reassigned to different shipments, but they must ALWAYS belong to a shipment.

```bash
# First, create a new shipment
curl -X POST http://localhost:8080/api/shipment \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": 1,
    "origin": "Accra, Ghana",
    "destination": "Abidjan, Ivory Coast",
    "shipment_date": "2025-10-22 10:00:00",
    "status": "pending"
  }'

# Response: {"status": true, "data": {"id": 2, "shipment_number": "SHP-F6G7H8I9J0"}}

# Now move parcel 2 to the new shipment
curl -X PUT http://localhost:8080/api/parcel/2 \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": 2,
    "description": "Medical supplies - Temperature sensitive",
    "weight": 15.0,
    "dimensions": "45x35x25 cm",
    "status": "pending",
    "sender_name": "MedSupply Ghana",
    "recipient_name": "Abidjan Medical Center"
  }'
```

**Response:**
```json
{
  "status": true,
  "message": "Parcel updated successfully"
}
```

## Scenario 6: Invalid Operations

### Try to update parcel's shipment_id to null

```bash
curl -X PUT http://localhost:8080/api/parcel/1 \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": null,
    "description": "Test",
    "status": "pending"
  }'
```

**Response (Error):**
```json
{
  "status": false,
  "message": "Shipment ID cannot be empty - all parcels must belong to a shipment"
}
```

### Try to update shipment with invalid warehouse

```bash
curl -X PUT http://localhost:8080/api/shipment/1 \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": 999,
    "origin": "Accra",
    "destination": "Lagos",
    "shipment_date": "2025-10-21 08:00:00",
    "status": "pending"
  }'
```

**Response (Error):**
```json
{
  "status": false,
  "message": "Warehouse does not exist"
}
```

## PHP Code Examples

### Example: Creating a complete shipment flow in PHP

```php
<?php

// Initialize API base URL
$baseUrl = 'http://localhost:8080/api';

// Function to make API request
function apiRequest($method, $url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// 1. Create warehouse
$warehouse = apiRequest('POST', "$baseUrl/warehouse", [
    'name' => 'Test Warehouse',
    'location' => 'Accra',
    'address' => '123 Test St',
    'capacity' => 10000
]);

if ($warehouse['status']) {
    $warehouseId = $warehouse['data']['id'];
    echo "Warehouse created: ID $warehouseId\n";
    
    // 2. Create shipment
    $shipment = apiRequest('POST', "$baseUrl/shipment", [
        'warehouse_id' => $warehouseId,
        'origin' => 'Accra',
        'destination' => 'Lagos',
        'shipment_date' => date('Y-m-d H:i:s')
    ]);
    
    if ($shipment['status']) {
        $shipmentId = $shipment['data']['id'];
        echo "Shipment created: ID $shipmentId, Number: {$shipment['data']['shipment_number']}\n";
        
        // 3. Create parcel
        $parcel = apiRequest('POST', "$baseUrl/parcel", [
            'shipment_id' => $shipmentId,
            'description' => 'Test package',
            'weight' => 5.5
        ]);
        
        if ($parcel['status']) {
            echo "Parcel created: ID {$parcel['data']['id']}, Tracking: {$parcel['data']['tracking_number']}\n";
        }
    }
}
```

## Key Validation Rules Summary

1. **Warehouse → Shipment**: Every shipment must have a valid `warehouse_id`
2. **Shipment → Parcel**: Every parcel must have a valid `shipment_id`
3. **Parcel Creation**: Cannot create a parcel without a `shipment_id`
4. **Parcel Update**: Cannot set `shipment_id` to null or empty
5. **Foreign Key Constraints**: Database enforces CASCADE deletes

## Best Practices

1. **Always create warehouse first** before creating shipments
2. **Always create shipment first** before adding parcels
3. **Check validation errors** and handle them appropriately in your application
4. **Use the auto-generated numbers** (shipment_number, tracking_number) for tracking
5. **Join queries return related data** - use them to avoid multiple API calls
