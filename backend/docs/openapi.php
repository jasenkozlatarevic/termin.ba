<?php

use OpenApi\Attributes as OA;

// =============================================
// REUSABLE SCHEMAS
// =============================================

#[OA\Schema(
    schema: "User",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 9),
        new OA\Property(property: "full_name", type: "string", example: "Swagger Test"),
        new OA\Property(property: "email", type: "string", format: "email", example: "swagger.test@example.com"),
        new OA\Property(property: "created_at", type: "string", example: "2026-11-22 20:00:00")
    ]
)]
class UserSchema {}


// =============================================
// OPENAPI CONFIGURATION
// =============================================

#[OA\OpenApi(
    info: new OA\Info(
        title: "Termin.ba API",
        version: "1.0.0",
        description: "REST API documentation for Termin.ba application"
    ),
    servers: [
        new OA\Server(
            url: "http://localhost:8000",
            description: "Local development server"
        )
    ],
    tags: [
        new OA\Tag(name: "Users"),
        new OA\Tag(name: "Venues"),
        new OA\Tag(name: "Games"),
        new OA\Tag(name: "Reservations"),
        new OA\Tag(name: "Game Participants"),
        new OA\Tag(name: "Reviews"),
        new OA\Tag(name: "Weather")
    ]
)]
class OpenApiSpec
{
    // =============================================
    // USERS
    // =============================================

    #[OA\Get(
        path: "/users",
        tags: ["Users"],
        summary: "Get all users",
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/User")
                )
            )
        ]
    )]
    public function getUsers() {}

    #[OA\Get(
        path: "/users/{id}",
        tags: ["Users"],
        summary: "Get user by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User found",
                content: new OA\JsonContent(
                    ref: "#/components/schemas/User"
                )
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUserById() {}

    #[OA\Post(
        path: "/users",
        tags: ["Users"],
        summary: "Create a user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["full_name", "email", "password"],
                properties: [
                    new OA\Property(property: "full_name", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "message", type: "string", example: "User created successfully."),
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/User"
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Invalid input")
        ]
    )]
    public function createUser() {}

    #[OA\Put(
        path: "/users/{id}",
        tags: ["Users"],
        summary: "Update a user",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "full_name", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "User updated"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function updateUser() {}

    #[OA\Delete(
        path: "/users/{id}",
        tags: ["Users"],
        summary: "Delete a user",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "User deleted"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function deleteUser() {}


    // =============================================
    // VENUES
    // =============================================

    #[OA\Get(
        path: "/venues",
        tags: ["Venues"],
        summary: "Get all venues",
        responses: [
            new OA\Response(response: 200, description: "List of venues")
        ]
    )]
    public function getVenues() {}

    #[OA\Get(
        path: "/venues/{id}",
        tags: ["Venues"],
        summary: "Get venue by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Venue found"),
            new OA\Response(response: 404, description: "Venue not found")
        ]
    )]
    public function getVenueById() {}

    #[OA\Get(
        path: "/venues/location/{location}",
        tags: ["Venues"],
        summary: "Get venues by location",
        parameters: [
            new OA\Parameter(
                name: "location",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Venues found")
        ]
    )]
    public function getVenuesByLocation() {}

    #[OA\Post(
        path: "/venues",
        tags: ["Venues"],
        summary: "Create a venue",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "location", "sport", "price_per_hour"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "location", type: "string"),
                    new OA\Property(property: "sport", type: "string"),
                    new OA\Property(property: "price_per_hour", type: "number", format: "float"),
                    new OA\Property(property: "description", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Venue created"),
            new OA\Response(response: 400, description: "Invalid input")
        ]
    )]
    public function createVenue() {}

    #[OA\Put(
        path: "/venues/{id}",
        tags: ["Venues"],
        summary: "Update a venue",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "location", type: "string"),
                    new OA\Property(property: "sport", type: "string"),
                    new OA\Property(property: "price_per_hour", type: "number", format: "float"),
                    new OA\Property(property: "description", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Venue updated"),
            new OA\Response(response: 404, description: "Venue not found")
        ]
    )]
    public function updateVenue() {}

    #[OA\Delete(
        path: "/venues/{id}",
        tags: ["Venues"],
        summary: "Delete a venue",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Venue deleted"),
            new OA\Response(response: 404, description: "Venue not found")
        ]
    )]
    public function deleteVenue() {}


    // =============================================
    // GAMES
    // =============================================

    #[OA\Get(
        path: "/games",
        tags: ["Games"],
        summary: "Get all games",
        responses: [
            new OA\Response(response: 200, description: "List of games")
        ]
    )]
    public function getGames() {}

    #[OA\Get(
        path: "/games/{id}",
        tags: ["Games"],
        summary: "Get game by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Game found"),
            new OA\Response(response: 404, description: "Game not found")
        ]
    )]
    public function getGameById() {}

    #[OA\Get(
        path: "/games/venue/{venue_id}",
        tags: ["Games"],
        summary: "Get games by venue ID",
        parameters: [
            new OA\Parameter(
                name: "venue_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Games found")
        ]
    )]
    public function getGamesByVenue() {}

    #[OA\Get(
        path: "/games/creator/{creator_id}",
        tags: ["Games"],
        summary: "Get games by creator ID",
        parameters: [
            new OA\Parameter(
                name: "creator_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Games found")
        ]
    )]
    public function getGamesByCreator() {}

    #[OA\Post(
        path: "/games",
        tags: ["Games"],
        summary: "Create a game",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["creator_id", "venue_id", "game_name", "game_date", "game_time", "max_players"],
                properties: [
                    new OA\Property(property: "creator_id", type: "integer"),
                    new OA\Property(property: "venue_id", type: "integer"),
                    new OA\Property(property: "game_name", type: "string"),
                    new OA\Property(property: "game_date", type: "string", format: "date"),
                    new OA\Property(property: "game_time", type: "string"),
                    new OA\Property(property: "max_players", type: "integer"),
                    new OA\Property(property: "skill_level", type: "string"),
                    new OA\Property(property: "description", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Game created"),
            new OA\Response(response: 400, description: "Invalid input")
        ]
    )]
    public function createGame() {}

    #[OA\Put(
        path: "/games/{id}",
        tags: ["Games"],
        summary: "Update a game",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "creator_id", type: "integer"),
                    new OA\Property(property: "venue_id", type: "integer"),
                    new OA\Property(property: "game_name", type: "string"),
                    new OA\Property(property: "game_date", type: "string", format: "date"),
                    new OA\Property(property: "game_time", type: "string"),
                    new OA\Property(property: "max_players", type: "integer"),
                    new OA\Property(property: "skill_level", type: "string"),
                    new OA\Property(property: "description", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Game updated"),
            new OA\Response(response: 404, description: "Game not found")
        ]
    )]
    public function updateGame() {}

    #[OA\Delete(
        path: "/games/{id}",
        tags: ["Games"],
        summary: "Delete a game",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Game deleted"),
            new OA\Response(response: 404, description: "Game not found")
        ]
    )]
    public function deleteGame() {}


    // =============================================
    // RESERVATIONS
    // =============================================

    #[OA\Get(
        path: "/reservations",
        tags: ["Reservations"],
        summary: "Get all reservations",
        responses: [
            new OA\Response(response: 200, description: "List of reservations")
        ]
    )]
    public function getReservations() {}

    #[OA\Get(
        path: "/reservations/{id}",
        tags: ["Reservations"],
        summary: "Get reservation by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Reservation found"),
            new OA\Response(response: 404, description: "Reservation not found")
        ]
    )]
    public function getReservationById() {}

    #[OA\Get(
        path: "/reservations/user/{user_id}",
        tags: ["Reservations"],
        summary: "Get reservations by user ID",
        parameters: [
            new OA\Parameter(
                name: "user_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Reservations found")
        ]
    )]
    public function getReservationsByUser() {}

    #[OA\Get(
        path: "/reservations/venue/{venue_id}",
        tags: ["Reservations"],
        summary: "Get reservations by venue ID",
        parameters: [
            new OA\Parameter(
                name: "venue_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Reservations found")
        ]
    )]
    public function getReservationsByVenue() {}

    #[OA\Post(
        path: "/reservations",
        tags: ["Reservations"],
        summary: "Create a reservation",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id", "venue_id", "reservation_date", "start_time", "end_time", "number_of_players"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer"),
                    new OA\Property(property: "venue_id", type: "integer"),
                    new OA\Property(property: "reservation_date", type: "string", format: "date"),
                    new OA\Property(property: "start_time", type: "string"),
                    new OA\Property(property: "end_time", type: "string"),
                    new OA\Property(property: "number_of_players", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Reservation created"),
            new OA\Response(response: 400, description: "Invalid input or overlapping reservation")
        ]
    )]
    public function createReservation() {}

    #[OA\Put(
        path: "/reservations/{id}",
        tags: ["Reservations"],
        summary: "Update a reservation",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "user_id", type: "integer"),
                    new OA\Property(property: "venue_id", type: "integer"),
                    new OA\Property(property: "reservation_date", type: "string", format: "date"),
                    new OA\Property(property: "start_time", type: "string"),
                    new OA\Property(property: "end_time", type: "string"),
                    new OA\Property(property: "number_of_players", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Reservation updated"),
            new OA\Response(response: 404, description: "Reservation not found")
        ]
    )]
    public function updateReservation() {}

    #[OA\Delete(
        path: "/reservations/{id}",
        tags: ["Reservations"],
        summary: "Delete a reservation",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Reservation deleted"),
            new OA\Response(response: 404, description: "Reservation not found")
        ]
    )]
    public function deleteReservation() {}


    // =============================================
    // GAME PARTICIPANTS
    // =============================================

    #[OA\Get(
        path: "/game-participants",
        tags: ["Game Participants"],
        summary: "Get all game participants",
        responses: [
            new OA\Response(response: 200, description: "List of game participants")
        ]
    )]
    public function getGameParticipants() {}

    #[OA\Get(
        path: "/game-participants/{id}",
        tags: ["Game Participants"],
        summary: "Get participant by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Participant found"),
            new OA\Response(response: 404, description: "Participant not found")
        ]
    )]
    public function getGameParticipantById() {}

    #[OA\Get(
        path: "/game-participants/game/{game_id}",
        tags: ["Game Participants"],
        summary: "Get participants by game ID",
        parameters: [
            new OA\Parameter(
                name: "game_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Participants found")
        ]
    )]
    public function getParticipantsByGame() {}

    #[OA\Get(
        path: "/game-participants/user/{user_id}",
        tags: ["Game Participants"],
        summary: "Get participants by user ID",
        parameters: [
            new OA\Parameter(
                name: "user_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Participants found")
        ]
    )]
    public function getParticipantsByUser() {}

    #[OA\Post(
        path: "/game-participants",
        tags: ["Game Participants"],
        summary: "Add a participant to a game",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["game_id", "user_id"],
                properties: [
                    new OA\Property(property: "game_id", type: "integer", example: 1),
                    new OA\Property(property: "user_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Participant added"),
            new OA\Response(response: 400, description: "Invalid input or duplicate participant")
        ]
    )]
    public function createGameParticipant() {}

    #[OA\Put(
        path: "/game-participants/{id}",
        tags: ["Game Participants"],
        summary: "Update a game participant",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "game_id", type: "integer"),
                    new OA\Property(property: "user_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Participant updated"),
            new OA\Response(response: 404, description: "Participant not found")
        ]
    )]
    public function updateGameParticipant() {}

    #[OA\Delete(
        path: "/game-participants/{id}",
        tags: ["Game Participants"],
        summary: "Remove a participant",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Participant removed"),
            new OA\Response(response: 404, description: "Participant not found")
        ]
    )]
    public function deleteGameParticipant() {}


    // =============================================
    // REVIEWS
    // =============================================

    #[OA\Get(
        path: "/reviews",
        tags: ["Reviews"],
        summary: "Get all reviews",
        responses: [
            new OA\Response(response: 200, description: "List of reviews")
        ]
    )]
    public function getReviews() {}

    #[OA\Get(
        path: "/reviews/{id}",
        tags: ["Reviews"],
        summary: "Get review by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Review found"),
            new OA\Response(response: 404, description: "Review not found")
        ]
    )]
    public function getReviewById() {}

    #[OA\Get(
        path: "/reviews/venue/{venue_id}",
        tags: ["Reviews"],
        summary: "Get reviews by venue ID",
        parameters: [
            new OA\Parameter(
                name: "venue_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Reviews found")
        ]
    )]
    public function getReviewsByVenue() {}

    #[OA\Get(
        path: "/reviews/user/{user_id}",
        tags: ["Reviews"],
        summary: "Get reviews by user ID",
        parameters: [
            new OA\Parameter(
                name: "user_id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Reviews found")
        ]
    )]
    public function getReviewsByUser() {}

    #[OA\Post(
        path: "/reviews",
        tags: ["Reviews"],
        summary: "Create a review",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id", "venue_id", "rating"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 1),
                    new OA\Property(property: "venue_id", type: "integer", example: 1),
                    new OA\Property(property: "rating", type: "integer", minimum: 1, maximum: 5, example: 5),
                    new OA\Property(property: "comment", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Review created"),
            new OA\Response(response: 400, description: "Invalid input")
        ]
    )]
    public function createReview() {}

    #[OA\Put(
        path: "/reviews/{id}",
        tags: ["Reviews"],
        summary: "Update a review",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "user_id", type: "integer"),
                    new OA\Property(property: "venue_id", type: "integer"),
                    new OA\Property(property: "rating", type: "integer", minimum: 1, maximum: 5),
                    new OA\Property(property: "comment", type: "string", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Review updated"),
            new OA\Response(response: 404, description: "Review not found")
        ]
    )]
    public function updateReview() {}

    #[OA\Delete(
        path: "/reviews/{id}",
        tags: ["Reviews"],
        summary: "Delete a review",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Review deleted"),
            new OA\Response(response: 404, description: "Review not found")
        ]
    )]
    public function deleteReview() {}


    // =============================================
    // WEATHER (EXTERNAL API)
    // =============================================

    #[OA\Get(
        path: "/weather",
        tags: ["Weather"],
        summary: "Get current weather for a city",
        description: "Fetches current weather data from the OpenWeather API.",
        parameters: [
            new OA\Parameter(
                name: "city",
                in: "query",
                required: true,
                description: "City name, for example Sarajevo",
                schema: new OA\Schema(type: "string", example: "Sarajevo")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Weather data retrieved successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "city", type: "string", example: "Sarajevo"),
                        new OA\Property(property: "country", type: "string", example: "BA"),
                        new OA\Property(property: "temperature", type: "number", format: "float", example: 18.5),
                        new OA\Property(property: "feels_like", type: "number", format: "float", example: 18.1),
                        new OA\Property(property: "humidity", type: "integer", example: 65),
                        new OA\Property(property: "description", type: "string", example: "clear sky"),
                        new OA\Property(property: "icon", type: "string", example: "01d")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "City parameter is missing or invalid"
            ),
            new OA\Response(
                response: 502,
                description: "Weather service is currently unavailable"
            )
        ]
    )]
    public function getWeather() {}
}
