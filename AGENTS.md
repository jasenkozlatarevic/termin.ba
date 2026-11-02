# Termin.ba - Project Guidelines

## Project Overview

Termin.ba is a web platform for booking football and sports venues in Sarajevo and finding players to join games.

The main goal of the application is to allow users to:
- Discover sports venues and football fields
- View venue details and available time slots
- Book a football or sports venue
- Create a game and look for additional players
- Join existing games
- Review venues

## Technologies

- Frontend: HTML5, CSS3, JavaScript
- Backend: PHP with FlightPHP
- Database: MySQL
- API Documentation: OpenAPI / Swagger
- Authentication: JWT
- Database Access: PDO
- Development Environment: XAMPP
- Version Control: Git and GitHub

## Architecture

The application follows a layered architecture:

- Presentation Layer - frontend and FlightPHP routes
- Business Logic Layer - services
- Data Access Layer - DAO classes
- Database Layer - MySQL

The frontend communicates with the backend through REST API endpoints.

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
├── docs/
├── frontend/
│   ├── css/
│   ├── js/
│   ├── static/
│   └── views/
├── .gitignore
├── AGENTS.md
└── README.md
```

## Naming Conventions

- Use lowercase names for folders.
- Use meaningful and descriptive names for files.
- Use camelCase for JavaScript variables and functions.
- Use PascalCase for PHP classes.
- Use clear and consistent names for database tables and columns.
- Use descriptive names for API endpoints and service methods.

## Development Guidelines

- Keep frontend and backend code separated.
- Business logic should be placed in service classes.
- Database operations should be handled through DAO classes.
- Routes should remain focused on handling HTTP requests and responses.
- Do not store passwords or secrets directly in the source code.
- Do not commit sensitive configuration files or log files.
- Keep the code organized and easy to maintain.
## Database and DAO Guidelines

- Use PDO for all database operations.
- Use prepared statements for queries that accept user-provided values.
- Reuse the `BaseDao` class for common CRUD operations.
- Each entity should have its own DAO class extending `BaseDao`.
- Keep database connection settings in a local configuration file.
- Use `backend/config/config.example.php` as the template for local database configuration.
- Never commit `backend/config/config.php` or other files containing credentials.
- Maintain primary keys, foreign keys, and appropriate database constraints.
- Keep the database schema and ERD documentation synchronized with the SQL implementation.
- Include representative sample data in the database export.
- Test database connectivity and CRUD operations before submitting a milestone.

## Documentation and Testing

- Keep `README.md` updated with project setup and execution instructions.
- Update `docs/AI_LOG.md` to document AI assistance, important prompts, and examples of AI mistakes or corrections.
- Keep documentation consistent with the actual implementation.
- Run PHP syntax checks and relevant database tests before submitting changes.
- Submit each milestone on its designated Git branch and follow the repository's pull request and review requirements.