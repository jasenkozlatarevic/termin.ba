
document.addEventListener("DOMContentLoaded", function () {
    const app = document.getElementById("spapp");

    if (!app) {
        console.error("SPA container #spapp was not found.");
        return;
    }

    const views = {
        home: "home.html",
        venues: "venues.html",
        games: "games.html",
        "venue-details": "venue-details.html",
        "create-game": "create-game.html",
        reservations: "reservations.html",
        profile: "profile.html",
        login: "login.html",
        register: "register.html",
        about: "about.html"
    };

    const venues = {
        fis: {
            name: "FIS",
            rating: "⭐ 4.7",
            description: "Sportski teren u Sarajevu za rekreativne fudbalske utakmice."
        },
        goran: {
            name: "Goran Čengić",
            rating: "⭐ 4.6",
            description: "Sportski teren Goran Čengić za rekreativne fudbalske utakmice."
        },
        faris: {
            name: "TC Arena",
            rating: "⭐ 4.4",
            description: "Sportski teren TC Arena za rekreativne fudbalske utakmice."
        }
    };

    const venueImages = {
        fis: "static/fis-venue.jpg",
        goran: "static/goran-cengic-venue.webp",
        faris: "static/tc-arena-venue.jpg"
    };

    const loadedViews = new Set();
    let currentVenueId = "fis";

    async function loadView(viewName) {
        const selectedView = views[viewName] ? viewName : "home";
        const targetSection = document.getElementById(selectedView);

        if (!targetSection) {
            console.error(`View section "${selectedView}" was not found.`);
            return;
        }

        Object.keys(views).forEach(function (name) {
            const section = document.getElementById(name);

            if (section) {
                section.hidden = true;
            }
        });

        targetSection.hidden = false;

        if (loadedViews.has(selectedView)) {
            if (selectedView === "venue-details") {
                renderVenueDetails(currentVenueId);
            }

            if (selectedView === "home") {
                loadWeather();
            }

            return;
        }

        try {
            const response = await fetch(`views/${views[selectedView]}`);

            if (!response.ok) {
                throw new Error(`HTTP error: ${response.status}`);
            }

            const html = await response.text();

            targetSection.innerHTML = html;
            loadedViews.add(selectedView);

            if (selectedView === "venue-details") {
                renderVenueDetails(currentVenueId);
            }

            if (selectedView === "home") {
                loadWeather();
            }

        } catch (error) {
            console.error("Error loading view:", error);

            targetSection.innerHTML = `
                <div class="container section">
                    <h2>Unable to load this page</h2>
                    <p>
                        Please check the HTML file in the views folder
                        and make sure Live Server is running.
                    </p>
                </div>
            `;
        }
    }

    // =========================================
    // WEATHER API
    // =========================================

    async function loadWeather() {
        const content = document.getElementById("weather-content");
        const updated = document.getElementById("weather-updated");

        if (!content) return;

        content.innerHTML = `
            <p class="weather-loading">
                Loading weather for Sarajevo...
            </p>
        `;

        if (updated) {
            updated.textContent = "";
        }

        try {
            const response = await fetch(
                "http://localhost:8000/weather?city=Sarajevo",
                {
                    headers: {
                        "Accept": "application/json"
                    }
                }
            );

            const result = await response.json();

            if (!response.ok || result.status !== "success" || !result.data) {
                throw new Error(
                    result.message || `Weather request failed (${response.status})`
                );
            }

            const weather = result.data;

            const iconUrl =
                `https://openweathermap.org/img/wn/${encodeURIComponent(weather.icon)}@2x.png`;

            const temperature = Number(weather.temperature);
            const feelsLike = Number(weather.feels_like);
            const humidity = Number(weather.humidity);

            content.innerHTML = `
                <div class="weather-main">

                    <img
                        class="weather-icon"
                        src="${iconUrl}"
                        alt="${escapeHtml(weather.description || "Weather icon")}"
                    >

                    <div>
                        <p class="weather-city">
                            ${escapeHtml(weather.city)},
                            ${escapeHtml(weather.country || "")}
                        </p>

                        <p class="weather-temperature">
                            ${Number.isFinite(temperature) ? Math.round(temperature) : "--"}
                            <span>°C</span>
                        </p>

                        <p class="weather-description">
                            ${escapeHtml(weather.description || "Current conditions")}
                        </p>
                    </div>

                </div>

                <div class="weather-details">

                    <div>
                        <span>Feels like</span>
                        <strong>
                            ${Number.isFinite(feelsLike) ? Math.round(feelsLike) : "--"}°C
                        </strong>
                    </div>

                    <div>
                        <span>Humidity</span>
                        <strong>
                            ${Number.isFinite(humidity) ? humidity : "--"}%
                        </strong>
                    </div>

                </div>
            `;

            if (updated) {
                updated.textContent =
                    `Last updated: ${new Date().toLocaleTimeString()}`;
            }

        } catch (error) {
            console.error("Weather loading error:", error);

            content.innerHTML = `
                <div class="weather-error">
                    <p>
                        Weather information is temporarily unavailable.
                    </p>

                    <button
                        type="button"
                        class="btn btn-outline"
                        id="weather-retry"
                    >
                        Try again
                    </button>
                </div>
            `;

            const retryButton = document.getElementById("weather-retry");

            if (retryButton) {
                retryButton.addEventListener("click", loadWeather);
            }
        }
    }

    function escapeHtml(value) {
        return String(value ?? "").replace(/[&<>"']/g, function (character) {
            return {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;"
            }[character];
        });
    }

    // =========================================
    // VENUE DETAILS
    // =========================================

    function renderVenueDetails(venueId) {
        const venue = venues[venueId] || venues.fis;
        const venueImage = document.getElementById("venue-detail-image");
        const imagePath = venueImages[venueId] || venueImages.fis;

        venueImage.src = imagePath;
        venueImage.alt = `Sportski teren ${venue.name} u Sarajevu`;

        document.getElementById("venue-detail-heading").textContent = venue.name;
        document.getElementById("venue-detail-name").textContent = venue.name;
        document.getElementById("venue-detail-rating").textContent = venue.rating;
        document.getElementById("venue-detail-description").textContent = venue.description;
    }

    // =========================================
    // DEMO FORM MESSAGES
    // =========================================

    const demoMessages = {
        "login-form": "Demo verzija: prijava nije aktivna. Nemoj unositi stvarnu lozinku.",
        "register-form": "Demo verzija: registracija nije aktivna. Podaci nisu poslani.",
        "reservation-form": "Demo verzija: rezervacija nije aktivna. Podaci nisu poslani.",
        "create-game-form": "Demo verzija: kreiranje utakmice nije aktivno. Podaci nisu poslani.",
        "review-form": "Demo verzija: recenzija nije sačuvana. Podaci nisu poslani."
    };

    app.addEventListener("submit", function (event) {
        const message = demoMessages[event.target.id];

        if (!message) return;

        event.preventDefault();

        const messageId =
            event.target.id.replace("-form", "") + "-message";

        const messageElement = document.getElementById(messageId);

        if (messageElement) {
            messageElement.textContent = message;
        }
    });

    // =========================================
    // NAVIGATION
    // =========================================

    function navigate() {
        const hash = window.location.hash.substring(1);
        const venueMatch = hash.match(/^venue-details-(fis|goran|faris)$/);

        if (venueMatch) {
            currentVenueId = venueMatch[1];
            loadView("venue-details");

        } else if (views[hash]) {
            loadView(hash);

        } else {
            loadView("home");
        }
    }

    window.addEventListener("hashchange", navigate);

    if (!window.location.hash) {
        window.location.hash = "#home";
    } else {
        navigate();
    }
});
