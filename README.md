# DSC Home Assignment Documentation - Yohana Jocelyn
## Project Structure

### How the Project is Organized
```text
DSC-Home-Assignment/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Auth, MeetingRoom, Booking, and User Controllers
│   │   └── Middleware/          # CheckRole middleware for role guards
│   ├── Models/                  # Eloquent models (User, Role, MeetingRoom, Booking)
│   ├── Policies/                # Fine-grained authorization (BookingPolicy, MeetingRoomPolicy, UserPolicy)
│   └── Providers/               # Policy and proxy service bindings (AppServiceProvider)
├── database/
│   ├── migrations/              # Database schema versioning & SoftDeletes
│   └── seeders/                 # Seeders for default roles, rooms, and users
├── resources/
│   ├── js/                      # App JS (global form double-submit handling & spinners)
│   ├── css/                     # Tailwind CSS entry point
│   └── views/
│       ├── admin/               # Admin views (dashboard, meeting-rooms, bookings, users)
│       ├── staff/               # Staff views (dashboard, meeting-rooms, my-bookings)
│       └── components/          # Reusable Blade components (layout, navbar, calendar-view,
│                                # meeting-room-form, user-form, confirm-modal, booking-details-modal)
└── routes/
    └── web.php                  # Role-segregated route definitions
```

The application follows Laravel's standard MVC architecture, separated by role and responsibility:
* **`app/Http/Controllers/`**: Where application workflows and request are handled. Controllers are split by resource (`AuthController`, `BookingController`, `MeetingRoomController`, `UserController`).
* **`app/Policies/` & `app/Http/Middleware/`**: Enforces all authorization and access control logic.
* **`resources/views/`**: Organized by user persona (`admin/` vs `staff/`) for dedicated views, with shared UI modules extracted into `components/`.

### Why This Structure Was Chosen
* **Role-Driven Directory Separation:** Grouping views and routes into different directories `admin` and `staff` prevents accidental exposure of admin-only controls to staff views.
* **Reusability and DRY (Don't Repeat Yourself):** Shared interfaces (such as `<x-meeting-room-form>`, `<x-user-form>`, `<x-calendar-view>`, and `<x-confirm-modal>`) are centralized as reusable Blade components. For example, room creation and editing share the exact same form component, reducing code duplication.
* **Separation of Concerns:** Business logic is in Controllers, data relationships and constraints are in Eloquent Models, authorization is isolated in Policies, and visual presentation is done by Blade templates.

### Separation of Frontend & Backend Responsibilities
* **Backend Responsibilities:**
  * **Data Integrity & Persistence:** Manages database interactions, foreign keys, and soft deletes through Eloquent ORM.
  * **Authoritative Business Rules:** Enforces domain constraints (preventing past bookings, overlaps, bookings outside 08:00–22:00, and soft-deleted room assignments).
  * **Security & Authorization:** Authenticates requests, maintains stateful sessions, validates CSRF tokens, and evaluates role-based policies before performing changes to the application state.
* **Frontend Responsibilities:**
  * **Presentation & Layout:** Responsive UI styled with Tailwind CSS tokens and Blade Heroicons.
  * **Immediate User Feedback:** Error messaging (`<x-input-error>`) and flash status alerts.
  * **Client-Side Interactivity:** Handles tab toggles, live table search filtering, modal lifecycle transitions, and double-submit prevention with animated loading spinners.

## Technology Choices

* **Frameworks & Libraries:**
  * **Backend:** **Laravel (PHP)** - Chosen because it is the web framework I am most familiar working with. It provides a well-defined MVC architecture, robust Eloquent ORM, built-in CSRF security, and an expressive authorization system using Policies and Gates.
  * **Frontend:** **Laravel Blade** paired with **Tailwind CSS** and custom CSS styling for clean, consistent component design. Reusable Blade components keep UI logic modular and DRY.
  * **Icons & Editor:** **Blade Heroicons** for lightweight inline SVGs, and **Quill.js** for formatted rich-text room descriptions and amenities.
  * **Client-Side Scripting:** **Vanilla JavaScript** to handle interactive features such as real-time search, category/timing filtering, calendar view toggling, modal dialogs, and double-submit prevention with loading spinners.

* **Database Choice:**
  * **MySQL** was selected as the database management system because the application's core functionality (managing meeting rooms, user accounts, and bookings) primarily involves standard CRUD operations and relational data management rather than complex analytical queries. MySQL provides reliable relational data storage and foreign key constraints to maintain data integrity across users, roles, rooms, and bookings.

* **Authentication Approach:**
  * **Laravel Session-Based Authentication:** Employs stateful, encrypted HTTP-only session cookies and CSRF tokens across all form requests.
  * Includes role-based post-login redirection (`redirectBasedOnRole()`) routing Admins to the Admin Dashboard and Staff to My Bookings, backed by an explicit logout confirmation modal.

* **Major Third-Party Services:**
  * **Railway:** Cloud hosting platform used for production deployment, managing both the application runtime environment and the managed MySQL database instance.

## Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd DSC-Home-Assignment
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Configure Environment Variables**
   Copy the example environment file to create your `.env`:
   ```bash
   cp .env.example .env
   ```
   Open `.env` and uncomment the following lines to configure your initial administrator credentials:
   ```env
   ADMIN_NAME="admin"
   ADMIN_EMAIL=admin@example.com
   ADMIN_PASSWORD=your-strong-password
   ```
   > **Note:** Change `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD` to your preferred administrator credentials. These credentials will be used when seeding the database.

   Configure your database connection settings in `.env` (e.g. SQLite or MySQL):
   ```env
   DB_CONNECTION=sqlite
   # or for MySQL:
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=dsc_home_assignment
   # DB_USERNAME=root
   # DB_PASSWORD=your_password
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations and Seed Database**
   Run migrations and seed the initial roles, admin user, and meeting rooms:
   ```bash
   php artisan migrate --seed
   ```
   *(If using SQLite and the file does not exist, run `touch database/database.sqlite` first).*

6. **Install and Build Frontend Assets**
   ```bash
   npm install
   npm run build
   ```
   *(Alternatively, run `npm run dev` to watch assets during development).*

7. **Start the Application Server**
   ```bash
   php artisan serve
   ```
   The application is now running at `http://localhost:8000`.

8. **Log in as Administrator**
   - Open your browser and navigate to `http://localhost:8000`.
   - Log in using the administrator credentials configured in your `.env` (`ADMIN_EMAIL` and `ADMIN_PASSWORD`).

9. **Create a Staff Account**
   - From the left sidebar navigation, click on **Users** (`/admin/users`).
   - Click the **"Create a New User"** button.
   - Fill in the user details:
     - **Name:** Enter the staff member's name (e.g. `Staff Member`)
     - **Email:** Enter the staff member's email (e.g. `staff@example.com`)
     - **Role:** Select `Staff` from the dropdown
     - **Password:** Enter a password (minimum 6 characters, e.g. `StaffAccount@567`)
   - Click **"Create"** to register the account.

10. **Test Staff Portal (Optional)**
    - Click the log out icon at the bottom of the sidebar and confirm.
    - Log in using the newly created Staff credentials to access the Staff dashboard, view available rooms, book time slots, and view personal reservations.

## Test Accounts
1. Admin Account: 
Email: admin@example.com
Password: AdminAccount@567

2. Staff Account
Email: staff@example.com
Password: StaffAccount@567

## Deployment
Deployed using Railway with public URL: https://dsc-home-assignment-production.up.railway.app/

## Technical Decisions

### 1. Authentication & Role-Based Redirection
* Implemented using standard Laravel session-based authentication with encrypted HTTP-only session cookies and CSRF tokens.
* Uses a post-login redirector (`redirectBasedOnRole()`): Admins land directly on the Admin Dashboard (`/admin/dashboard` / meeting rooms), while Staffs are directed to their personal reservation overview (`/staff/my-bookings`).
* An explicit confirmation modal is required prior to logging out to prevent accidental session termination.

### 2. Role-Based Access Control
Authorization is enforced across multiple layers:
* **Route Middleware:** The custom `CheckRole` middleware inspects the authenticated user's role on every route group (`role:Admin` vs `role:Staff`). If a staff member manually types an admin URL, they are immediately redirected to `/staff/my-bookings`.
* **Model Policies & Gates:** Controllers explicitly invoke `Gate::authorize()` via dedicated policies (`BookingPolicy`, `MeetingRoomPolicy`, `UserPolicy`).

### 3. Booking Conflict & Overlap Detection
* Booking collisions are evaluated in real time in `BookingController` using standard interval overlap logic:
  ```php
  Booking::where('meeting_room_id', $roomId)
      ->where('date', $date)
      ->where(function ($q) use ($start, $end) {
          $q->where('start_time', '<', $end)
            ->where('end_time', '>', $start);
      })
      ->exists();
  ```
* This condition detects full and partial overlaps on the same room and date.
* Soft-deleted (cancelled) bookings are excluded automatically, freeing up cancelled slots immediately for other users.

### 4. Database Structure
* **`roles`:** Stores application roles (`Admin`, `Staff`).
* **`users`:** Holds credentials and a foreign key (`role_id`) referencing `roles`.
* **`meeting_rooms`:** Tracks room metadata (name, location, capacity, rich-text description, active flag `is_active`, and `deleted_at`).
* **`bookings`:** Stores foreign keys (`user_id`, `meeting_room_id`), reservation title, booking date, start time, end time, and `deleted_at`.
* Both `MeetingRoom` and `Booking` models implement Laravel's `SoftDeletes` trait to maintain historical records and auditability without breaking relationship integrity.

### 5. Role Readonly on Edit (Prevents Admin Lockout)
* When editing an existing user in the User Management panel, the **Role** field is rendered as read-only in the UI.
* **Reason:** In a system where an admin is managing accounts (or is the only admin), allowing role modifications on edit introduces a risk where an admin could accidentally demote their own account to Staff. Doing so would lock them out of administrative access to the user panel.

### 6. Operating Hours
* Bookings are strictly restricted between **08:00 AM and 10:00 PM**.
* Enforced both on the backend and the frontend.
* **Reason:** Office spaces are usually available within defined working hours. This also ensures that the total height of the calendar grid is compact.

### 7. Secure Credential Seeding via `.env`
* The initial administrator account is provisioned through `UserSeeder`, but its credentials (`ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`) are drawn directly from the local environment (`.env`) rather than hardcoded in migration files or seeders.
* Staff accounts must be created manually through the authenticated Admin UI.
* **Reason:** Reduces the risk of leaking sensitive administrative passwords in code.

### 8. Soft Deletes on Meeting Rooms
* When a room is removed by an admin, it is soft-deleted (`deleted_at` timestamp).
* **Reason:** Prevents users from booking a deleted room or updating existing bookings to point to a deleted room, while preserving past booking logs intact.

