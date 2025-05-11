document.addEventListener('DOMContentLoaded', function () {
    const payButton = document.getElementById('cpf-pay-button');
    if (!payButton) return;

    payButton.addEventListener('click', function () {
        const form = document.getElementById('cpf-form');
        const name = form.cpf_name.value;
        const email = form.cpf_email.value;
        const message = form.cpf_message.value;

        if (!name || !email || !message) {
            alert("Please fill in all fields.");
            return;
        }

        const options = {
            key: cpf_vars.key,
            amount: cpf_vars.amount,
            currency: "INR",
            name: "Contact Payment",
            description: "Pay before submitting form",
            handler: function (response) {
                document.getElementById('cpf-result').innerHTML = 
                    "<p>Payment ID: " + response.razorpay_payment_id + "</p><p>Thank you, " + name + "!</p>";
                form.reset();
            },
            prefill: {
                name: name,
                email: email
            },
            theme: {
                color: "#3399cc"
            }
        };
        const rzp = new Razorpay(options);
        rzp.open();
    });
});
