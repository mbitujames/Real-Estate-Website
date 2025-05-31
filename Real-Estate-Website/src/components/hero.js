import React, { useEffect, useState } from 'react';
import { supabase } from '../supabaseClient';

const Hero = () => {
    const [heroData, setHeroData] = useState(null);

    useEffect(() => {
        const fetchHeroData = async () => {
            const { data, error } = await supabase
                .from('HeroSection')
                .select('*')
                .single();

            if (error) {
                console.error('Error fetching hero data:', error);
            } else {
                setHeroData(data);
            }
        };

        fetchHeroData();
    }, []);

    if (!heroData) {
        return <div>Loading...</div>;
    }

    return (
        <section className="hero">
            <div className="container">
                <h1>{heroData.title}</h1>
                <p>{heroData.subtitle}</p>
                <a href="#allproperty" className="btn">Explore Properties</a>
            </div>
        </section>
    );
};

export default Hero;