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

    // Keep track of views that have already been loaded
    const loadedViews = new Set();

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

    // Handle navigation using the URL hash
    function navigate() {
        const viewName = window.location.hash.substring(1);

        if (views[viewName]) {
            loadView(viewName);
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
