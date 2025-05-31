// This file contains custom JavaScript code for client-side functionality, such as event handling and DOM manipulation.

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Supabase client
    const { createClient } = supabase;
    const supabaseUrl = 'YOUR_SUPABASE_URL';
    const supabaseKey = 'YOUR_SUPABASE_ANON_KEY';
    const supabaseClient = createClient(supabaseUrl, supabaseKey);

    // Example function to fetch properties from Supabase
    async function fetchProperties() {
        const { data, error } = await supabaseClient
            .from('properties')
            .select('*');

        if (error) {
            console.error('Error fetching properties:', error);
            return;
        }

        // Process and display properties
        displayProperties(data);
    }

    function displayProperties(properties) {
        const resultsContainer = document.getElementById('search-results');
        resultsContainer.innerHTML = '';

        properties.forEach(property => {
            const propertyElement = document.createElement('div');
            propertyElement.classList.add('property-item');
            propertyElement.innerHTML = `
                <h3>${property.title}</h3>
                <p>${property.description}</p>
                <p>Price: ${property.price}</p>
            `;
            resultsContainer.appendChild(propertyElement);
        });
    }

    // Call fetchProperties on page load
    fetchProperties();
});