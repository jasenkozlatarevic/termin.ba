document.addEventListener("DOMContentLoaded", function () {
    const app = document.getElementById("spapp");

    if (!app) {
        console.error("SPA container #spapp was not found.");
        return;
    }

    // Available application views
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
        fis: { name: "FIS", rating: "⭐ 4.7", description: "Sportski teren u Sarajevu za rekreativne fudbalske utakmice." },
        goran: { name: "Goran Čengić", rating: "⭐ 4.6", description: "Sportski teren Goran Čengić za rekreativne fudbalske utakmice." },
        faris: { name: "Faris Pendek", rating: "⭐ 4.4", description: "Sportski teren Faris Pendek za rekreativne fudbalske utakmice." }
    };

    // Keep track of views that have already been loaded
    const loadedViews = new Set();
    let currentVenueId = "fis";

    // Load and display a selected view
    async function loadView(viewName) {
        const selectedView = views[viewName] ? viewName : "home";
        const targetSection = document.getElementById(selectedView);

        if (!targetSection) {
            console.error(`View section "${selectedView}" was not found.`);
            return;
        }

        // Hide all sections
        Object.keys(views).forEach(function (name) {
            const section = document.getElementById(name);

            if (section) {
                section.hidden = true;
            }
        });

        // Display the selected section
        targetSection.hidden = false;

        // Avoid loading the same HTML file more than once
        if (loadedViews.has(selectedView)) {
            if (selectedView === "venue-details") renderVenueDetails(currentVenueId);
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
            if (selectedView === "venue-details") renderVenueDetails(currentVenueId);

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

    function renderVenueDetails(venueId) {
        const venue = venues[venueId] || venues.fis;
        document.getElementById("venue-detail-heading").textContent = venue.name;
        document.getElementById("venue-detail-name").textContent = venue.name;
        document.getElementById("venue-detail-rating").textContent = venue.rating;
        document.getElementById("venue-detail-description").textContent = venue.description;
    }

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
        const messageId = event.target.id.replace("-form", "") + "-message";
        const messageElement = document.getElementById(messageId);
        if (messageElement) messageElement.textContent = message;
    });

    // Handle navigation using the URL hash
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

    // Navigate when the URL hash changes
    window.addEventListener("hashchange", navigate);

    // Open the home page by default
    if (!window.location.hash) {
        window.location.hash = "#home";
    } else {
        navigate();
    }
});
