import React, { useState, useEffect } from 'react';
import { supabase } from '../supabaseClient';

const Search = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [properties, setProperties] = useState([]);

  const handleSearch = async (e) => {
    e.preventDefault();
    const { data, error } = await supabase
      .from('Properties')
      .select('*')
      .ilike('name', `%${searchTerm}%`);

    if (error) {
      console.error('Error fetching properties:', error);
    } else {
      setProperties(data);
    }
  };

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
    };

    fetchProperties();
  }, []);

  return (
    <section className="search">
      <form onSubmit={handleSearch}>
        <input
          type="text"
          placeholder="Search for properties..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          required
        />
        <button type="submit">Search</button>
      </form>
      <div className="search-results">
        {properties.map((property) => (
          <div key={property.id} className="property-card">
            <h3>{property.name}</h3>
            <p>{property.description}</p>
            <p>Price: ${property.price}</p>
          </div>
        ))}
      </div>
    </section>
  );
};

export default Search;