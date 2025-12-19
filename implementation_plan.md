# Implementation Plan - PHP/MySQL Backend & Booking System

## Goal
Remove Firebase integration, implement a PHP/MySQL backend, and add a travel package booking system with cost estimation.

## User Review Required
> [!IMPORTANT]
> This plan assumes a standard PHP/MySQL environment (e.g., XAMPP/WAMP). The frontend will communicate with PHP scripts in an `api/` directory.

## Proposed Changes

### Database (MySQL)
#### [NEW] [database.sql](file:///d:/Github/traveltime/traveltime/database.sql)
- Schema for `users`, `destinations` (with base price), `bookings` (new), `messages`.

### Backend (PHP)
#### [NEW] [api/db_connect.php](file:///d:/Github/traveltime/traveltime/api/db_connect.php)
- Database connection script (PDO).

#### [NEW] [api/auth.php](file:///d:/Github/traveltime/traveltime/api/auth.php)
- Login and Register endpoints.

#### [NEW] [api/data.php](file:///d:/Github/traveltime/traveltime/api/data.php)
- Endpoints for Destinations, Messages, and **Bookings**.
- Logic to handle GET (fetch) and POST (create/update) requests.

### Frontend
#### [MODIFY] [js/data-service.js](file:///d:/Github/traveltime/traveltime/js/data-service.js)
- Rewrite to replace `localStorage` mocks with `fetch()` calls to the PHP API.
- Add `calculateTripCost` function.

#### [MODIFY] [index.html](file:///d:/Github/traveltime/traveltime/index.html)
- Update Search Form to be a "Booking Form".
- Add "Travel Mode" dropdown (Bus, Train, Plane).
- Add "Cost Estimate" display.

#### [MODIFY] [script.js](file:///d:/Github/traveltime/traveltime/script.js)
- Handle form changes: Calculate cost dynamically or on button click.
- Submit booking to `data-service.js`.

#### [DELETE] Firebase Files
- `js/firebase-config.js`
- `js/firebase-utils.js`
- `FIREBASE_INTEGRATION_COMPLETED.md`
- `FIREBASE_INTEGRATION_TASK_LIST.md`
- `README_Firebase.md`
- `test_firebase.html`

## Verification Plan
1. **Database Setup**: Import `database.sql` into local MySQL server.
2. **Backend Test**: Test PHP endpoints using small fetch scripts or browser.
3. **Frontend Test**:
    - Register/Login with PHP backend.
    - Create a booking with different travel modes and verify cost calculation.
    - Check User Dashboard to see created bookings.
