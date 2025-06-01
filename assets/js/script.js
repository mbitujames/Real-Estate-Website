'use strict';

/* element toggle function */
const elemToggleFunc = function (elem) { elem.classList.toggle("active"); }

/* Navbar toggle logic for all screens */
export function initNavbarMenu() {
  const navbar = document.querySelector("[data-navbar]");
  const overlay = document.querySelector("[data-overlay]");
  const navCloseBtn = document.querySelector("[data-nav-close-btn]");
  const navOpenBtn = document.querySelector("[data-nav-open-btn]");
  const navbarLinks = document.querySelectorAll("[data-nav-link]");

  // Open menu
  if (navOpenBtn && navbar) {
    navOpenBtn.addEventListener('click', function() {
      navbar.classList.add('active');
      if (overlay) overlay.classList.add('active');
    });
  }
  // Close menu
  if (navCloseBtn && navbar) {
    navCloseBtn.addEventListener('click', function() {
      navbar.classList.remove('active');
      if (overlay) overlay.classList.remove('active');
    });
  }
  // Close menu when clicking overlay
  if (overlay && navbar) {
    overlay.addEventListener('click', function() {
      navbar.classList.remove('active');
      overlay.classList.remove('active');
    });
  }
  // Close menu when clicking any nav link (for mobile)
  navbarLinks.forEach(link => {
    link.addEventListener('click', function() {
      navbar.classList.remove('active');
      if (overlay) overlay.classList.remove('active');
    });
  });
}

/* header active state */
const header = document.querySelector("[data-header]");
window.addEventListener("scroll", function () {
  window.scrollY >= 400 ? header.classList.add("active")
    : header.classList.remove("active");
});

/* code for filter functionality */
function filterFunction() {
  const sortBy = document.getElementById("sortby").value;
  const propertyCards = document.querySelectorAll(".property-card");

  propertyCards.forEach((card) => {
    if (sortBy === "property-type") {
      card.style.display = "block";
    } else {
      if (card.dataset.propertyType === sortBy) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    }
  });
}

/* js for validating the contact us form */
function validateForm() {
  var name = document.getElementById("name").value;
  var email = document.getElementById("email").value;
  var number = document.getElementById("number").value;

  // Validate Name
  var namePattern = /^[a-zA-Z\s]+$/;
  if (!namePattern.test(name)) {
      alert('Full name can only contain letters and spaces.');
      return false;
  }

  // Validate Email
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
      alert('Please enter a valid email address');
      return false;
  }

  // Validate Phone Number
  var numberPattern = /^[0-9]{10}$/;
  if (!numberPattern.test(number)) {
      alert('Phone number must be exactly 10 digits and contain only numbers.');
      return false;
  }
  // All validations passed
  return true;
}