# Product Requirement Document (PRD) - Ultimate Version
## Project: Sports Venue Booking System

---

## 1. Project Overview & Tech Stack
A web-based application for booking sports venues. Users can check availability, book slots in real-time, and pay instantly using QRIS via Midtrans. 

- **Backend Framework:** Laravel 13 (PHP 8.3+)
- **Frontend Framework:** Laravel Livewire 3 & Tailwind CSS
- **Database:** MySQL
- **Payment Gateway:** Midtrans (Snap/Core API for QRIS)

---

## 2. Functional Requirements (FR)
- **FR-001:** User can register, login, and logout securely.
- **FR-002:** User (Customer) can view a list of available sports fields.
- **FR-003:** User can select a specific date and available hourly slots via a real-time calendar.
- **FR-004:** User can initiate a booking, generating a Midtrans QRIS code with a 10-minute countdown timer.
- **FR-005:** User can view their booking history and payment status.
- **FR-006:** Admin can log in to a separate dashboard.
- **FR-007:** Admin can perform CRUD operations on fields (Create, Read, Update, Delete).
- **FR-008:** Admin can monitor all active, successful, and expired bookings.

## 3. Non-Functional Requirements (NFR)
- **Security:** All passwords must be hashed using Laravel's default (Bcrypt). All forms must use CSRF protection.
- **Performance:** Livewire components must optimize state changes to keep page response time < 2 seconds.
- **UI/UX:** Fully responsive design, mobile-friendly layout using Tailwind CSS.

---

## 4. Database Schema & Constraints

### `users`
- `id` (PK, BigIncrements)
- `name` (string), `email` (string, **UNIQUE**), `password` (string), `role` (enum: 'customer', 'admin')

### `fields`
- `id` (PK, BigIncrements)
- `name` (string), `type` (enum: 'Futsal', 'Badminton', 'Basketball')
- `price_per_hour` (decimal, 10, 2)
- `image_path` (string, nullable)

### `bookings`
- `id` (PK, BigIncrements)
- `user_id` (FK to users, **ON DELETE CASCADE**)
- `field_id` (FK to fields, **ON DELETE CASCADE**)
- `booking_date` (date, **INDEXED**)
- `start_time` (time), `end_time` (time)
- `total_price` (decimal, 10, 2)
- `status` (enum: 'pending', 'paid', 'expired', 'cancelled')
- `expires_at` (datetime, **INDEXED**)

### `payments`
- `id` (PK, BigIncrements)
- `booking_id` (FK to bookings, **ON DELETE CASCADE**)
- `transaction_id` (string, Midtrans reference, **UNIQUE**)
- `payment_type` (string, default: 'qris')
- `gross_amount` (decimal, 10, 2)
- `transaction_status` (string)

---

## 5. Booking & Business Rules
1. **Operational Hours:** Fields are only open from **08:00 to 22:00**.
2. **Duration Limits:** Minimum booking duration is 1 hour, maximum is 5 hours per transaction (must be exact hourly increments, e.g., 08:00-09:00).
3. **Date Constraints:** Users cannot book slots for past dates or past hours of the current day.
4. **Lock Mechanism:** Selecting a slot triggers a **10-minute temporary lock** (Status: `pending`). 
5. **Conflict Avoidance:** A slot is unavailable if there is an existing booking for the same field, date, and time where status is `paid` OR (status is `pending` AND `expires_at > NOW()`).

---

## 6. System Workflow & API Integration

### UI Flow Navigation
[Landing Page] ➔ [Login/Register] ➔ [Dashboard / Field List] ➔ [Field Detail & Livewire Calendar]
↓
[Booking Success / Paid] 🔀 [Midtrans QRIS Screen (10m Timer)] ➔ [Checkout / Lock Slot]

### Midtrans & Error Handling Workflow
1. **Checkout:** Create `booking` (status: `pending`) ➔ Call Midtrans API to get QRIS payload ➔ Show QR Code.
   - *Error Handling:* If Midtrans API fails to generate QRIS, **rollback** the booking transaction immediately and show flash message: *"Failed to generate payment. Please try again."*
2. **Double Booking Protection:** If two users click checkout at the exact same millisecond for the same slot, database transaction locks must prevent it. The second user gets an error: *"This slot is no longer available."*
3. **Webhook Handler:** Midtrans sends notification to `/api/midtrans-webhook`.
   - **Security:** The controller MUST validate the Midtrans `signature_key` before updating database.
   - Map `settlement`/`capture` ➔ Status: `paid`.
   - Map `deny`/`cancel` ➔ Status: `cancelled`.
   - Map `expire` ➔ Status: `expired`.
4. **Automation (Scheduler):** A Laravel Cron Job runs every minute. If a booking is `pending` and `expires_at < NOW()`, change status to `expired` AND call Midtrans API to cancel/expire that specific transaction.

---

## 7. App Pages Structure
- **Customer Pages:** Landing Page, Login/Register, Field Directory, Field Detail (includes Livewire Calendar Selector), Checkout/Payment Page, Booking History.
- **Admin Pages:** Admin Dashboard (Analytics Overview), Field Management (CRUD List), Booking Monitor (Global Schedule View).

---

## 8. Development & Implementation Rules (For AI Agent)
- Use **Laravel 13** architecture standards.
- Use **Livewire 3** for all interactive components (Calendar selector, real-time filters).
- Use **Form Request Classes** for input validation.
- Use **Service Pattern** (`App\Services\MidtransService`) to isolate Midtrans API logic.
- Use **Laravel Scheduler** (`routes/console.php` or dedicated ServiceProvider) for the 1-minute expiration check.
- Use **Laravel Policies** to restrict Admin pages from Customers.
- Never write raw SQL; always utilize **Eloquent ORM** with proper relationships (`belongsTo`, `hasMany`).

## 9. Folder Structure
app/
├── Livewire/
│   ├── Customer/
│   │   ├── FieldDirectory.php
│   │   ├── FieldDetail.php
│   │   ├── Checkout.php
│   │   └── BookingHistory.php
│   │
│   └── Admin/
│       ├── Dashboard.php
│       ├── FieldManagement.php
│       └── BookingMonitor.php
│
├── Models/
│   ├── User.php
│   ├── Field.php
│   ├── Booking.php
│   └── Payment.php
│
├── Services/
│   └── MidtransService.php
│
├── Http/
│   ├── Controllers/
│   │   └── MidtransWebhookController.php
│   │
│   ├── Requests/
│   │   ├── StoreBookingRequest.php
│   │   └── UpdateFieldRequest.php
│   │
│   └── Middleware/
│
└── Policies/
    ├── BookingPolicy.php
    └── FieldPolicy.php

## AI Implementation Rules

When generating code:

- Generate the project incrementally.
- Do not skip any required file.
- Keep code clean and modular.
- Explain briefly before generating code.
- Never leave TODO comments.
- Use Laravel best practices.
- Use Eloquent relationships.
- Use Transactions where required.
- Validate every request.
- Generate migrations before models.
- Generate models before Livewire components.
- Generate routes after components are finished.
- Ensure the project runs without syntax errors.