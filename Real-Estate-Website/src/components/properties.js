import React, { useEffect, useState } from 'react';
import { supabase } from '../supabaseClient';

const Properties = () => {
    const [properties, setProperties] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchProperties = async () => {
            const { data, error } = await supabase
                .from('Properties')
                .select('*');

            if (error) {
                console.error('Error fetching properties:', error);
            } else {
                setProperties(data);
            }
            setLoading(false);
        };

        fetchProperties();
    }, []);

    if (loading) {
        return <div>Loading...</div>;
    }

    return (
        <section className="properties">
            <div className="container">
                <h2 className="h2 section-title">Property Listings</h2>
                <div className="properties-list">
                    {properties.map(property => (
                        <div key={property.id} className="property-card">
                            <img src={property.image_url} alt={property.title} />
                            <h3 className="property-title">{property.title}</h3>
                            <p className="property-description">{property.description}</p>
                            <p className="property-price">${property.price}</p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default Properties;