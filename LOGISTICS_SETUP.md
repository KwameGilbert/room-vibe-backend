# Logistics Module Setup Guide

This guide will help you set up and start using the logistics module (Warehouse, Shipment, Parcel) in the room-vibe backend.

## Quick Start

### 1. Database Setup

Run the SQL schema to create the necessary tables:

```bash
# Using MySQL command line
mysql -u your_username -p your_database_name < frontend/manager/logistics_schema.sql

# Or using phpMyAdmin
# - Open phpMyAdmin
# - Select your database (room_vibe)
# - Go to "SQL" tab
# - Copy and paste the contents of frontend/manager/logistics_schema.sql
# - Click "Go"
```

### 2. Verify Tables Created

After running the schema, you should have three new tables:
- `warehouse`
- `shipment`
- `parcel`

You can verify this by running:

```sql
SHOW TABLES LIKE 'warehouse';
SHOW TABLES LIKE 'shipment';
SHOW TABLES LIKE 'parcel';
```

### 3. Start the Server

If not already running, start the PHP development server:

```bash
cd /path/to/room-vibe-backend
php -S localhost:8080 -t public
```

### 4. Test the Endpoints

Test that the API is working:

```bash
# Test warehouse endpoint
curl http://localhost:8080/api/warehouse

# Expected response:
# {"status":true,"warehouses":[]}
```

## Testing the Validation

### Test 1: Create a Warehouse

```bash
curl -X POST http://localhost:8080/api/warehouse \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Warehouse",
    "location": "Accra",
    "address": "123 Test Street"
  }'
```

**Expected:** Success with warehouse ID returned.

### Test 2: Create Shipment with Valid Warehouse

```bash
# Replace {warehouse_id} with the ID from Test 1
curl -X POST http://localhost:8080/api/shipment \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": 1,
    "origin": "Accra",
    "destination": "Lagos",
    "shipment_date": "2025-10-21 10:00:00"
  }'
```

**Expected:** Success with shipment ID and shipment_number returned.

### Test 3: Try to Create Shipment with Invalid Warehouse (Validation Test)

```bash
curl -X POST http://localhost:8080/api/shipment \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": 999,
    "origin": "Accra",
    "destination": "Lagos",
    "shipment_date": "2025-10-21 10:00:00"
  }'
```

**Expected:** Error message "Warehouse does not exist" ✅

### Test 4: Create Parcel with Valid Shipment

```bash
# Replace {shipment_id} with the ID from Test 2
curl -X POST http://localhost:8080/api/parcel \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": 1,
    "description": "Test package",
    "weight": 5.5
  }'
```

**Expected:** Success with parcel ID and tracking_number returned.

### Test 5: Try to Create Parcel without Shipment ID (Validation Test)

```bash
curl -X POST http://localhost:8080/api/parcel \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Test package",
    "weight": 5.5
  }'
```

**Expected:** Error message "Shipment ID is required - all parcels must belong to a shipment" ✅

### Test 6: Try to Create Parcel with Invalid Shipment (Validation Test)

```bash
curl -X POST http://localhost:8080/api/parcel \
  -H "Content-Type: application/json" \
  -d '{
    "shipment_id": 999,
    "description": "Test package",
    "weight": 5.5
  }'
```

**Expected:** Error message "Shipment does not exist" ✅

## Verification Checklist

- [ ] Database tables created successfully
- [ ] Warehouse API endpoint accessible
- [ ] Can create warehouse
- [ ] Can create shipment with valid warehouse
- [ ] **Validation**: Cannot create shipment with invalid warehouse
- [ ] Can create parcel with valid shipment
- [ ] **Validation**: Cannot create parcel without shipment_id
- [ ] **Validation**: Cannot create parcel with invalid shipment

## Troubleshooting

### Issue: "Table doesn't exist" error

**Solution:** Make sure you ran the logistics_schema.sql script in the correct database.

```sql
-- Check current database
SELECT DATABASE();

-- Use the correct database
USE room_vibe;

-- Then run the schema again
```

### Issue: "Foreign key constraint fails"

**Solution:** This is actually good! It means the database constraints are working. Make sure you're:
1. Creating warehouses before shipments
2. Creating shipments before parcels
3. Using valid IDs for foreign keys

### Issue: Validation not working

**Solution:** Check that:
1. You're using the correct route files (WarehouseRoute.php, ShipmentRoute.php, ParcelRoute.php)
2. The routes are registered in src/routes/api.php
3. The models are using the correct validation methods

### Issue: Cannot connect to database

**Solution:** Check your database configuration in `src/config/Database.php`:

```php
// Make sure these match your setup
private $host = "localhost";
private $db_name = "room_vibe";
private $username = "your_username";
private $password = "your_password";
```

## Understanding the Validation Flow

### Shipment → Warehouse Validation

```
1. Client sends POST /api/shipment with warehouse_id
2. ShipmentController receives request
3. Shipment model checks: Does warehouse exist?
   - YES: Create shipment ✅
   - NO: Return error "Warehouse does not exist" ❌
```

### Parcel → Shipment Validation

```
1. Client sends POST /api/parcel with shipment_id
2. ParcelController receives request
3. Parcel model checks:
   a. Is shipment_id provided?
      - NO: Return error "Shipment ID is required..." ❌
   b. Does shipment exist?
      - NO: Return error "Shipment does not exist" ❌
      - YES: Create parcel ✅
```

## Next Steps

After verifying everything works:

1. **Integration**: Integrate the logistics API into your frontend application
2. **Authentication**: Add authentication/authorization if needed
3. **Testing**: Write unit tests for the validation logic
4. **Monitoring**: Set up logging for API usage and errors
5. **Documentation**: Share the API documentation with your team

## Additional Resources

- Full API documentation: `LOGISTICS_API.md`
- Usage examples: `LOGISTICS_EXAMPLES.md`
- Database schema: `frontend/manager/logistics_schema.sql`

## Support

If you encounter any issues:
1. Check the error message returned by the API
2. Review the validation rules in the documentation
3. Verify your database constraints are set up correctly
4. Check the server logs for detailed error information

## Key Validation Points to Remember

✅ **Shipments MUST have a valid warehouse**
- Validated on creation
- Validated on update

✅ **Parcels MUST have a valid shipment**
- Validated on creation
- Validated on update
- Cannot be set to null

✅ **All relationships are enforced at both application and database level**
- Application: PHP validation in models
- Database: Foreign key constraints
