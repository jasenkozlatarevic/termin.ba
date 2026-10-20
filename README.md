# Termin.ba

A web platform for booking football venues in Sarajevo and finding players to join games.

## Project Description

Termin.ba is designed to make organizing recreational football activities easier by connecting players with sports venues and other players in Sarajevo.

Users can:
- Discover football venues in Sarajevo
- View venue details
- Request venue reservations
- Create football games
- Join existing games
- Manage their profile
- View their games and reservations

## Main Features

- User registration and login interface
- Sports venue discovery
- Venue details
- Reservation form
- Game creation form
- Existing games listing
- User profile interface
- Single-page application navigation
- Responsive web interface

## Technologies

### Frontend
- HTML5
- CSS3
- JavaScript
- Fetch API
- Hash-based navigation

### Development Tools
- Visual Studio Code
- Git
- GitHub
- Live Server

## Project Structure

```text
termin.ba/
├── backend/
│   ├── config/
│   │   └── .gitkeep
│   ├── dao/
│   │   └── .gitkeep
│   ├── logs/
│   │   └── .gitkeep
│   ├── middleware/
│   │   └── .gitkeep
│   ├── routes/
│   │   └── .gitkeep
│   └── services/
│       └── .gitkeep
├── docs/
│   ├── database-schema.md
│   └── ERD.png
├── frontend/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
│   ├── static/
│   ├── views/
│   │   ├── about.html
│   │   ├── create-game.html
│   │   ├── games.html
│   │   ├── home.html
│   │   ├── login.html
│   │   ├── profile.html
│   │   ├── register.html
│   │   ├── reservations.html
│   │   ├── venue-details.html
│   │   └── venues.html
│   └── index.html
├── AGENTS.md
├── README.md
└── .gitignore
```

## Current Project Status

Milestone 1 focuses on the initial project structure, static frontend views, SPA navigation, and database schema planning.

The frontend currently uses sample content. Authentication, reservations, and game management are not yet connected to a backend or database.

## Running the Frontend

1. Clone or download the repository.
2. Open the project in Visual Studio Code.
3. Open `frontend/index.html`.
4. Start the page using the Live Server extension.
5. Navigate through the application using the navigation menu.
