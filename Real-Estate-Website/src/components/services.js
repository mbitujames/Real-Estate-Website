import React, { useEffect, useState } from 'react';
import { supabase } from '../supabaseClient';

const Services = () => {
  const [services, setServices] = useState([]);

  useEffect(() => {
    const fetchServices = async () => {
      const { data, error } = await supabase
        .from('Services')
        .select('*');

      if (error) {
        console.error('Error fetching services:', error);
      } else {
        setServices(data);
      }
    };

    fetchServices();
  }, []);

  return (
    <section className="services">
      <div className="container">
        <p className="section-subtitle">Our Services</p>
        <h2 className="h2 section-title">What We Offer</h2>
        <ul className="services-list">
          {services.map((service) => (
            <li key={service.id} className="service-item">
              <h3 className="service-title">{service.title}</h3>
              <p className="service-description">{service.description}</p>
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
};

export default Services;