const wrapper = document.querySelector('.wrapper');
const registerLink = document.querySelector('.register-link');
const loginLink = document.querySelector('.login-link');
const sendOtpBtn = document.getElementById('sendOtpBtn');
const otpInputBox = document.getElementById('otpBox');
const registerBtn = document.getElementById('registerBtn');
const registerFormSection = document.querySelector('.register_form');
const registerOtpSection = document.querySelector('.register_otp');

// Switch between login and registration forms
registerLink.onclick = () => {
    wrapper.classList.add('active');
};

loginLink.onclick = () => {
    wrapper.classList.remove('active');
};

// Handle Send OTP button click
sendOtpBtn.addEventListener('click', function (e) {
    e.preventDefault(); // Prevent form submission

    // Get form data
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Check if name, email, and password are provided
    if (name && email && password) {
        // Create a FormData object to send data to the server
        let formData = new FormData();
        formData.append('send_otp', true);
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);

        // Send AJAX request
        fetch('send_otp.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Show success or error message
            
            // Check if OTP was sent successfully
            if (data.includes('OTP has been sent')) {
                // Hide the register form section
                registerFormSection.style.display = 'none';
                
                // Show the OTP input box and "Sign Up" button
                otpInputBox.style.display = 'block';
                registerBtn.style.display = 'block';
                registerOtpSection.style.display = 'block'; // Show OTP section
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    } else {
        alert('Please fill all fields before sending OTP.');
    }
});
