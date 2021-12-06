const stripe = Stripe(stripePublicKey);
const elements = stripe.elements();
const card = elements.create("card");

card.mount("#cart-elements");
card.on("change", function (event) {
    document.querySelector("button").disabled = event.empty;
    document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
});

const form = document.getElementById("payment-form");

form.addEventListener("submit", function (event) {
    event.preventDefault();

    stripe
        .confirmationCardPayment(clientSecret, {
            paymentMethod: {
                card: card
            }
        })
        .then(function (result) {
            if (result.error) {
                console.log(result.error.message);
            } else {
                window.location.href = redirectAfterSuccessUrl;
            }
        });
});