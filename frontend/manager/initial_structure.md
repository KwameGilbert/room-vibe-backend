/manager
├── index.php                   # Entry point (could redirect to dashboard if logged in)
├── login.php                   # Manager login page
├── logout.php                  # Manager logout functionality
├── config/
│   ├── manager-config.php      # Manager-specific configuration (or shared config including Database.php)
│   └── auth.php                # Manager authentication helper functions
├── includes/                   # Reusable components & partials
│   ├── header.php              # Common HTML head elements, meta tags, CSS includes
│   ├── footer.php              # Footer and closing HTML tags
│   ├── navbar.php              # Top navigation bar (if any)
│   └── sidebar.php             # Side navigation/menu (links to dashboard, rooms, bookings, reports, etc.)
├── pages/                      # All manager pages with distinct functionality
│   ├── dashboard.php           # Dashboard with metrics (bookings, revenue, room status, etc.)
│   ├── update-hostel.php       # Page to update hostel information (name, description, address, etc.)
│   ├── manage-room-types.php   # List, add, edit, delete room types
│   ├── add-room-type.php       # Form to add a new room type
│   ├── edit-room-type.php      # Edit existing room type
│   ├── manage-rooms.php         # List rooms with options to add/edit/remove or update status
│   ├── add-room.php            # Form to add a new room
│   ├── edit-room.php           # Form to edit room details
│   ├── hostel-images.php       # Upload and manage hostel images
│   ├── bookings.php            # List of student bookings (only for the manager’s hostel)
│   ├── booking-details.php     # Detailed view of a single booking record
│   ├── end-session.php         # Endpoint (AJAX or form) to mark a booking session as closed
│   └── reports.php             # Generate reports on allocations, payments, etc.
├── assets/
│   ├── css/
│   │   └── style.css           # Custom CSS for the manager panel (could extend Tailwind or custom styles)
│   ├── js/
│   │   └── main.js             # Custom JavaScript for interactions (e.g., AJAX calls, modals)
│   └── images/                 # Images like logos, icons, etc.
└── vendor/                     # (If using Composer, keep the vendor folder here or refer to a shared location)
