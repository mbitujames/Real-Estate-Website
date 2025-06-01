import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm';

const supabase = createClient(
  'https://kcluzpyfsejzzznmisqd.supabase.co',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImtjbHV6cHlmc2Vqenp6bm1pc3FkIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDg1NDQwMzksImV4cCI6MjA2NDEyMDAzOX0.aTrgRN-5zCgoOV6A8XcI0eUPmkoTmUlvRDPndCSW-Xk'
);

export async function initHeader() {
  const dropdownBtn = document.getElementById('dropdown-btn');
  const dropdownContent = document.getElementById('dropdown-content');
  const addListingBtn = document.querySelector('.header-top-btn');

  // Dropdown logic (use class for show/hide)
  if (dropdownBtn && dropdownContent) {
    dropdownBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      dropdownContent.classList.toggle('show');
    });
    // Prevent closing when clicking inside dropdown
    dropdownContent.addEventListener('click', function(e) {
      e.stopPropagation();
    });
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
      dropdownContent.classList.remove('show');
    });
  }

  // Supabase session logic
  const { data: { session } } = await supabase.auth.getSession();
  let userType = null;
  let userEmail = null;

  if (session && session.user) {
    userEmail = session.user.email;
    userType = session.user.user_metadata?.user_type || session.user.app_metadata?.user_type;

    if (dropdownContent) {
      dropdownContent.innerHTML = `
        <span style="display:block;padding:10px 18px;color:#22223b;font-weight:bold;">${userEmail}</span>
        <a href="dashboard.html">Dashboard</a>
        <a href="#" id="logout-link">Logout</a>
      `;
    }

    if ((userType === 'owner' || userType === 'admin') && addListingBtn) {
      addListingBtn.style.display = 'inline-block';
    } else if (addListingBtn) {
      addListingBtn.style.display = 'none';
    }

    // Logout logic
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
      logoutLink.addEventListener('click', async function(e) {
        e.preventDefault();
        await supabase.auth.signOut();
        window.location.href = 'login.html';
      });
    }
  } else {
    if (dropdownContent) {
      dropdownContent.innerHTML = `
        <a href="login.html">Login</a>
        <a href="signup.html">Signup</a>
      `;
    }
    if (addListingBtn) addListingBtn.style.display = 'none';
  }

  // Add Listing button logic
  if (addListingBtn) {
    addListingBtn.onclick = function() {
      if (session && userType === 'owner') {
        window.location.href = 'propowner_dashboard.php';
      } else {
        alert("Please log in as Property Owner");
        window.location.href = 'login.html';
      }
    };
  }
}