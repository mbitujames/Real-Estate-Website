# Real Estate Website

## Overview
This project is a Real Estate Website built using modern web technologies. It utilizes Supabase as the backend for database operations, replacing the previous PHP implementation. The website features various components including property listings, client reviews, and a contact form.

## Project Structure
```
Real-Estate-Website
├── public
│   ├── index.html          # Main HTML entry point
│   ├── assets
│   │   ├── css
│   │   │   └── style.css   # Styles for the website
│   │   └── js
│   │       └── script.js    # Custom JavaScript for client-side functionality
├── src
│   ├── supabaseClient.js    # Initializes Supabase client
│   ├── components
│   │   ├── header.js        # Header component
│   │   ├── hero.js          # Hero section component
│   │   ├── search.js        # Search functionality component
│   │   ├── about.js         # About section component
│   │   ├── services.js      # Services offered component
│   │   ├── properties.js     # Property listings component
│   │   ├── reviews.js       # Client reviews component
│   │   ├── contact.js       # Contact form component
│   │   └── footer.js        # Footer component
│   └── utils
│       └── api.js          # Utility functions for API calls to Supabase
├── package.json             # npm configuration file
└── README.md                # Project documentation
```

## Setup Instructions
1. **Create a Supabase Account**: Go to [Supabase](https://supabase.io/) and sign up for an account.
2. **Create a New Project**: After logging in, create a new project in Supabase.
3. **Configure Supabase Client**: Update the `src/supabaseClient.js` file with your Supabase project URL and API key.
4. **Install Dependencies**: Run `npm install` in the project root to install necessary packages.
5. **Run the Application**: Use a local server to serve the `public/index.html` file or set up a development server using tools like Live Server or similar.

## Usage Guidelines
- The website allows users to search for properties, view details, and submit inquiries through the contact form.
- Client reviews are fetched from the Supabase database and displayed on the website.
- Ensure that you handle user authentication and session management as required by your application.

## Additional Notes
- Replace any PHP database queries in your components with appropriate Supabase client calls in the `src/utils/api.js` file.
- Update your components to utilize the data fetched from Supabase instead of the previous PHP backend.
- Make sure to test all functionalities thoroughly after migrating to Supabase.