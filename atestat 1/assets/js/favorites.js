let isLoggedIn = false;
let favorites = [];
let currentUserEmail = "";
let userProfileImage = "assets/images/users/user-placeholder.jpg";

function login(email) {
    isLoggedIn = true;
    currentUserEmail = email;
    localStorage.setItem("isLoggedIn", "true");
    localStorage.setItem("loggedInEmail", email);
    favorites = JSON.parse(localStorage.getItem(`favorites_${email}`)) || [];
    updateNavbar();
    showMessage("You are now logged in!");
    const scrollBack = localStorage.getItem("preLoginScroll");
	// Colorează inimile după login, fără refresh
favorites.forEach(name => {
    const likeAnchors = Array.from(document.querySelectorAll('a[onclick*="toggleLike"]'))
        .filter(a => a.getAttribute('onclick').includes(`'${name}'`));

    likeAnchors.forEach(anchor => {
        const icon = anchor.querySelector('i');
        if (icon) {
            icon.classList.remove("fa-heart-o");
            icon.classList.add("fa-heart");
            icon.style.color = "red";
        }
    });
});

    if (scrollBack) {
        setTimeout(() => {
            window.scrollTo({ top: parseInt(scrollBack), behavior: 'smooth' });
            localStorage.removeItem("preLoginScroll");
        }, 300);
    }
}

function logout() {
    localStorage.removeItem("isLoggedIn");
    localStorage.removeItem("loggedInEmail");
    isLoggedIn = false;
    favorites = [];
    currentUserEmail = "";
    location.href = "index.html";
}

function toggleLike(name, iconElement) {
    if (!isLoggedIn) {
        redirectToCreateAccount();
        return;
    }
    const index = favorites.indexOf(name);
    if (index === -1) {
        favorites.push(name);
        iconElement.classList.remove("fa-heart-o");
        iconElement.classList.add("fa-heart");
        iconElement.style.color = "red";
        showMessage(`${name} added to favorites!`);
    } else {
        favorites.splice(index, 1);
        iconElement.classList.remove("fa-heart");
        iconElement.classList.add("fa-heart-o");
        iconElement.style.color = "";
        showMessage(`${name} removed from favorites.`);
    }
    localStorage.setItem(`favorites_${currentUserEmail}`, JSON.stringify(favorites));
    if (document.getElementById("favorites-panel")?.classList.contains("open")) {
        refreshFavoritesPanel();
    }
}

function toggleFavoritesPanel() {
    const panel = document.getElementById("favorites-panel");
    panel.classList.toggle("open");
    if (panel.classList.contains("open")) {
        refreshFavoritesPanel();
    }
}

function refreshFavoritesPanel() {
    const panel = document.getElementById("favorites-panel");
    const container = panel.querySelector(".favorites-list");
    container.innerHTML = "";

    const favoritesNames = favorites;

    if (favoritesNames.length === 0) {
        container.innerHTML = "<p>No favorites yet.</p>";
    } else {
        favoritesNames.forEach(name => {
            const item = locationsData.find(loc => loc.name === name);
            if (item) {
                container.innerHTML += `
                    <div class="single-explore-item">
                        <div class="single-explore-img">
                            <img src="${item.image}" alt="${item.name}">
                            <div class="single-explore-img-info">
                                <button>${item.tag}</button>
                                <div class="single-explore-image-icon-box"></div>
                            </div>
                        </div>
                        <div class="single-explore-txt ${item.themeClass}">
                            <h2><a href="#">${item.name}</a></h2>
                            <p class="explore-rating-price">
                                <span class="explore-rating">${item.rating}</span>
                                <a href="#">${item.ratingsCount}</a>
                                <span class="explore-price-box">price: <span class="explore-price">${item.price}</span></span>
                                <a href="#">${item.type}</a>
                            </p>
                            <div class="explore-person">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="explore-person-img">
                                            <a href="#"><img src="assets/images/users/user-placeholder.jpg" alt="User"></a>
                                        </div>
                                    </div>
                                    <div class="col-sm-10">
                                        <p>${item.description}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML += `<button onclick="clearFavorites()" class="clear-favorites-btn">Clear All</button>`;
    }
}

function clearFavorites() {
    if (confirm("Are you sure you want to clear all favorites?")) {
        favorites = [];
        localStorage.setItem(`favorites_${currentUserEmail}`, JSON.stringify(favorites));
        refreshFavoritesPanel();
        const allHeartIcons = document.querySelectorAll('a[onclick*="toggleLike"] i');
        allHeartIcons.forEach(icon => {
            icon.classList.remove("fa-heart");
            icon.classList.add("fa-heart-o");
            icon.style.color = "";
        });
        showMessage("Favorites cleared.");
    }
}

function updateNavbar() {
    const nav = document.querySelector(".nav.navbar-nav.navbar-right");
    if (!nav.querySelector('[onclick="toggleFavoritesPanel()"]')) {
        nav.innerHTML += `
            <li class="scroll"><a href="#" onclick="event.preventDefault(); toggleFavoritesPanel()">My Favorites</a></li>
            <li class="scroll"><a href="#" onclick="logout()">Logout</a></li>
            <li>
                <a href="#">
                    <img src="${userProfileImage}" alt="Profile" style="width:32px; height:32px; border-radius:50%;">
                </a>
            </li>
        `;
    }
}


function showMessage(text) {
    const box = document.getElementById("message-box");
    if (!box) return;
    box.textContent = text;
    box.classList.remove("hidden");
    box.classList.add("show");
    setTimeout(() => {
        box.classList.remove("show");
        box.classList.add("hidden");
    }, 2500);
}

function redirectToCreateAccount() {
    localStorage.setItem("preLoginScroll", window.scrollY);
    showMessage("Please log in with your email to like places.");
    document.querySelector("#contact")?.scrollIntoView({ behavior: 'smooth' });
}

function submitEmailLogin(event) {
    event.preventDefault();
    const email = document.querySelector(".subscription-input-form")?.value.trim();
    if (!email || !email.includes("@")) {
        showMessage("Please enter a valid email.");
        return;
    }

    if (isLoggedIn) {
        if (email === currentUserEmail) {
            showMessage("You are already logged in.");
        } else {
            showMessage("Please log out before logging in with another account.");
        }
    } else {
        login(email);
    }
}



document.addEventListener("DOMContentLoaded", () => {
    if (localStorage.getItem("isLoggedIn") === "true") {
        isLoggedIn = true;
        currentUserEmail = localStorage.getItem("loggedInEmail") || "";
        favorites = JSON.parse(localStorage.getItem(`favorites_${currentUserEmail}`)) || [];
        updateNavbar();

        setTimeout(() => {
            favorites.forEach(name => {
                const likeAnchors = Array.from(document.querySelectorAll('a[onclick*="toggleLike"]'))
                    .filter(a => a.getAttribute('onclick').includes(`'${name}'`));

                likeAnchors.forEach(anchor => {
                    const icon = anchor.querySelector('i');
                    if (icon) {
                        icon.classList.remove("fa-heart-o");
                        icon.classList.add("fa-heart");
                        icon.style.color = "red";
                    }
                });
            });
        }, 300); // poți mări la 500 ms dacă încă nu merge
    }
});

