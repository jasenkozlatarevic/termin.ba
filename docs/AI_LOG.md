AI Usage Log — Termin.ba

Milestone 2

Tool: ChatGPT

ChatGPT was used as a supplementary tool during development to clarify PDO, database configuration, SQL constraints, and DAO implementation. The project code was reviewed and tested locally.

Examples of prompts:

"How can I implement CRUD operations using PDO?"

"How should I structure a MySQL database schema with foreign keys and constraints?"

AI Errors and Corrections

1. Database configuration

An initial suggestion placed database credentials directly in Database.php. During code review, I identified the security concern and moved the local credentials to config.php, which is excluded from Git. I also added config.example.php as a configuration template.

2. CRUD testing coverage

An initial description could have implied that CRUD operations had been tested for every DAO. After reviewing test-crud.php, I confirmed that the script tests only UserDao. I corrected the documentation to reflect the actual test coverage.

Verification

I tested the database connection, checked the sample-data counts returned by the DAO classes, executed CRUD tests for UserDao, and checked PHP syntax. I also verified that the local database configuration file is ignored by Git.

Milestone 3

Tool: ChatGPT

ChatGPT was used to help with FlightPHP routes, PHP services, Swagger documentation, Weather API integration, and debugging.

Examples of prompts

"Help me implement REST API routes using FlightPHP."

"How can I integrate OpenWeather API into my project?"

"Help me fix CORS errors between frontend and backend."

AI Errors and Corrections

1. CORS configuration: The initial configuration allowed the Swagger origin but not the frontend origin. I identified the mismatch and updated the allowed origins.

2. JSON response diagnosis: AI initially suspected an incomplete backend JSON response. Further testing confirmed that the weather data was successfully displayed, so unnecessary backend changes were avoided.

Verification

I tested API endpoints, Swagger documentation, venue CRUD operations, and confirmed that weather data was displayed correctly in the frontend.