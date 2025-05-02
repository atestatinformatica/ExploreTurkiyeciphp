function submitReview(event) {
    event.preventDefault();
    if (!isLoggedIn || !currentUserEmail) {
        alert("Please log in to leave a review.");
        return;
    }

    const form = event.target;
    const city = form.querySelector("#city-select").value;
    const text = form.querySelector("textarea").value.trim();
    const rating = parseInt(form.querySelectorAll("select")[1].value);

    if (!city || !text) return;

    const key = `reviews_${city}`;
    let reviews = JSON.parse(localStorage.getItem(key)) || [];

    // Remove existing review from this user
    reviews = reviews.filter(r => r.email !== currentUserEmail);

    reviews.push({ email: currentUserEmail, text, rating });
    localStorage.setItem(key, JSON.stringify(reviews));

    form.reset();
    renderAllReviews();
}

function renderAllReviews() {
    const container = document.getElementById("all-reviews-container");
    if (!container) return;

    const cities = [
        "istanbul", "ankara", "izmir", "antalya", "bursa",
        "adana", "gaziantep", "konya", "trabzon", "kayseri",
        "mardin", "sanliurfa", "cappadocia", "pamukkale"
    ];

    container.innerHTML = "";

    cities.forEach(city => {
        const reviews = JSON.parse(localStorage.getItem(`reviews_${city}`)) || [];
        if (reviews.length > 0) {
            const cityBlock = document.createElement("div");
            cityBlock.className = "city-review-block";

            cityBlock.innerHTML = `<h3 style="margin-bottom:20px;">${city.charAt(0).toUpperCase() + city.slice(1)}</h3><div class="review-carousel">`;

            reviews.forEach(r => {
                const reviewHTML = `
                <div class="single-testimonial-box">
                    <div class="testimonial-description">
                        <div class="testimonial-info">
                            <div class="testimonial-img">
                                <img src="assets/images/users/user-placeholder.jpg" width="50" height="50" alt="User" style="border-radius:50%;">
                            </div>
                            <div class="testimonial-person">
                                <h2>${r.email}</h2>
                                <h4>${city}</h4>
                                <div class="testimonial-person-star">
                                    ${"★".repeat(r.rating)}${"☆".repeat(5 - r.rating)}
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-comment">
                            <p>${r.text}</p>
                        </div>
                        ${r.email === currentUserEmail ? `<button onclick="deleteReview('${city}', '${r.email}')" class="review-delete-btn">Delete</button>` : ""}
                    </div>
                </div>`;
                cityBlock.innerHTML += reviewHTML;
            });

            cityBlock.innerHTML += `</div>`;
            container.appendChild(cityBlock);
        }
    });
}


function deleteReview(city, email) {
    let reviews = JSON.parse(localStorage.getItem(`reviews_${city}`)) || [];
    reviews = reviews.filter(r => r.email !== email);
    localStorage.setItem(`reviews_${city}`, JSON.stringify(reviews));
    renderAllReviews();
}
function toggleReviewForm() {
    const wrapper = document.getElementById("review-form-wrapper");
    wrapper.classList.toggle("hidden");
}

