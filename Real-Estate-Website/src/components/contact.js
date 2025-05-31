import React, { useState } from 'react';
import { supabase } from '../supabaseClient';

const Contact = () => {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [number, setNumber] = useState('');
  const [message, setMessage] = useState('');
  const [status, setStatus] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    const { data, error } = await supabase
      .from('contacts')
      .insert([{ name, email, number, message }]);

    if (error) {
      setStatus('Error sending message. Please try again.');
    } else {
      setStatus('Message sent successfully!');
      setName('');
      setEmail('');
      setNumber('');
      setMessage('');
    }
  };

  return (
    <section className="contact" id="contact">
      <div className="container">
        <p className="section-subtitle">Contact Us</p>
        <form id="contact-form" onSubmit={handleSubmit}>
          <h3>Contact us</h3>
          <label htmlFor="name"> Name: </label>
          <input
            type="text"
            name="name"
            required
            maxLength="50"
            placeholder="Enter your name"
            className="box"
            value={name}
            onChange={(e) => setName(e.target.value)}
          />
          <label htmlFor="email">Email:</label>
          <input
            type="email"
            name="email"
            required
            maxLength="50"
            placeholder="Enter your email"
            className="box"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
          <label htmlFor="number">Phone Number:</label>
          <input
            type="text"
            name="number"
            required
            maxLength="10"
            placeholder="Enter your number"
            className="box"
            value={number}
            onChange={(e) => setNumber(e.target.value)}
          />
          <label htmlFor="message">Message:</label>
          <textarea
            name="message"
            placeholder="Enter your message here..."
            required
            maxLength="1000"
            cols="30"
            rows="10"
            className="box"
            value={message}
            onChange={(e) => setMessage(e.target.value)}
          ></textarea>
          <input type="submit" value="Send Message" className="btn" />
        </form>
        {status && <p>{status}</p>}
      </div>
    </section>
  );
};

export default Contact;