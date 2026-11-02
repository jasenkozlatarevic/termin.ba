# Termin.ba

A web platform for booking football venues in Sarajevo and finding players to join games.

## Project Description

Termin.ba helps users organize recreational football activities by connecting players with sports venues and other players in Sarajevo.

Users can:
- Discover sports venues and view venue details.
- Make venue reservations.
- Create games and join existing games.
- Manage their profiles.
- Review sports venues.

## Main Features

- Responsive single-page application.
- Venue discovery and details.
- Registration and login interfaces.
- Reservation and game creation forms.
- Existing games listing.
- User profile interface.
- Database schema and sample data.
- PHP DAO layer with CRUD operations.
- Planned weather forecast integration using Open-Meteo.

## Technologies

**Frontend**
- HTML5, CSS3, JavaScript
- Fetch API
- Hash-based navigation

**Backend and Database**
- PHP
- MySQL
- PDO
- DAO architecture
- FlightPHP and OpenAPI planned for API implementation

**Development Tools**
- Visual Studio Code
- XAMPP
- DBeaver
- Git and GitHub
- Live Server

## Project Structure

```text
termin.ba/
├── backend/
│   ├── config/
│   ├── dao/
│   ├── logs/
│   ├── middleware/
│   ├── routes/
│   └── services/
├── database/
│   └── termin.sql
├── docs/
│   ├── AI_LOG.md
│   ├── database-schema.md
│   └── ERD.png
├── frontend/
│   ├── css/
│   ├── js/
│   ├── static/
│   ├── views/
│   └── index.html
├── AGENTS.md
├── README.md
└── .gitignore
```

## Database

The MySQL database is named `termin_ba` and contains six entities:

- `users`
- `venues`
- `games`
- `reservations`
- `game_participants`
- `reviews`

The schema includes primary keys, foreign keys, unique constraints, and validation constraints. Sample data is included in `database/termin.sql`.

## Local Setup

### 1. Start the database

Start MySQL through XAMPP.

### 2. Import the database

Import `database/termin.sql` using DBeaver or another MySQL client.

**Note:** The SQL script drops and recreates the project tables. Back up any data you need before running it again.

### 3. Configure the backend

Copy `backend/config/config.example.php` to `backend/config/config.php` and update the database credentials for your local environment.

The local `config.php` file must not be committed to Git.

### 4. Test the database connection

From the project root, run:

```bash
php backend/test-connection.php
```

### 5. Test the DAO layer

Run:

```bash
php backend/test-dao.php
php backend/test-crud.php
```

### 6. Run the frontend

Open `frontend/index.html` in Visual Studio Code and launch it using the Live Server extension.

## Current Status

Milestone 1 established the project structure and frontend prototype. Milestone 2 adds the MySQL schema, sample data, PDO connection configuration, and DAO classes with CRUD operations.

The frontend is still a prototype, and the backend API integration is planned for a later milestone.

## Future Improvements

- REST API endpoints using FlightPHP.
- API documentation with OpenAPI/Swagger.
- Authentication and authorization using JWT.
- Backend integration for reservations and games.
- Weather forecast integration using Open-Meteo.
