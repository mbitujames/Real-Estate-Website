// This file contains utility functions for making API calls to the Supabase backend.

import { createClient } from '@supabase/supabase-js';

// Initialize Supabase client
const supabaseUrl = 'YOUR_SUPABASE_URL'; // Replace with your Supabase URL
const supabaseAnonKey = 'YOUR_SUPABASE_ANON_KEY'; // Replace with your Supabase Anon Key
const supabase = createClient(supabaseUrl, supabaseAnonKey);

// Function to fetch testimonials
export const fetchTestimonials = async () => {
    const { data, error } = await supabase
        .from('Testimonials')
        .select('username, user_image_url, rating, review')
        .order('rating', { ascending: false })
        .limit(6);

    if (error) {
        console.error('Error fetching testimonials:', error);
        return [];
    }
    return data;
};

// Function to send a contact message
export const sendContactMessage = async (name, email, number, message) => {
    const { data, error } = await supabase
        .from('ContactMessages')
        .insert([{ name, email, number, message }]);

    if (error) {
        console.error('Error sending contact message:', error);
        return { success: false, error };
    }
    return { success: true, data };
};