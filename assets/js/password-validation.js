function validatePassword() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("cpassword").value;
    var email = document.getElementById("email").value;
    var fullName = document.getElementById('full_name').value;
    var phone = document.getElementById('phone').value;
    var requirements = document.getElementById("password-requirements");

    // Regular expression to enforce password complexity
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&#]{8,}$/;

    // Debugging logs
    console.log("Validating password");
    if (!passwordRegex.test(password)) {
        requirements.innerText = "Password must contain at least 8 characters, Uppercase letter,  lowercase letter, a number and a special character.";
        requirements.style.display = "block";
        return false;
    }
    requirements.style.display = "none"; // Hide requirements message if password is valid

    // Check if passwords match
    console.log("Checking if passwords match");
    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return false;
    }

    // validate email
    console.log("Validating email");
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    // Validate Full Name
    console.log("Validating full name");
    var fullNamePattern = /^[a-zA-Z\s]+$/;
    if (!fullNamePattern.test(fullName)) {
        alert('Full name can only contain letters and spaces.');
        return false;
    }

    // Validate Phone Number
    console.log("Validating phone number");
    var phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        alert('Phone number must be exactly 10 digits and contain only numbers.');
        return false;
    }

    console.log("All validations passed");
    // If all validations pass, return true
    return true;
}
