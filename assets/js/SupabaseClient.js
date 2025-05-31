// assets/js/supabaseClient.js

import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm';

const supabaseUrl = 'https://kcluzpyfsejzzznmisqd.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImtjbHV6cHlmc2Vqenp6bm1pc3FkIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDg1NDQwMzksImV4cCI6MjA2NDEyMDAzOX0.aTrgRN-5zCgoOV6A8XcI0eUPmkoTmUlvRDPndCSW-Xk';

export const supabase = createClient(supabaseUrl, supabaseKey);
