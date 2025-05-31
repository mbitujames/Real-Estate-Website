import React, { useEffect, useState } from 'react';
import { supabase } from '../supabaseClient';

const Reviews = () => {
    const [reviews, setReviews] = useState([]);

    useEffect(() => {
        const fetchReviews = async () => {
            const { data, error } = await supabase
                .from('Testimonials')
                .select('username, user_image_url, rating, review')
                .order('rating', { ascending: false })
                .limit(6);

            if (error) {
                console.error('Error fetching reviews:', error);
            } else {
                setReviews(data);
            }
        };

        fetchReviews();
    }, []);

    return (
        <section className="reviews" id="reviews">
            <div className="container">
                <p className="section-subtitle">Reviews</p>
                <h2 className="h2 section-title">Latest Client's Reviews</h2>
                <div className="box-container">
                    {reviews.length > 0 ? (
                        reviews.map((review, index) => (
                            <div className="box" key={index}>
                                <div className="user">
                                    <img src={review.user_image_url} alt={review.username} />
                                    <div>
                                        <h3>{review.username}</h3>
                                        <div className="stars">
                                            {Array.from({ length: Math.floor(review.rating) }, (_, i) => (
                                                <ion-icon key={i} name="star-outline"></ion-icon>
                                            ))}
                                            {review.rating % 1 >= 0.5 && (
                                                <ion-icon name="star-half-outline"></ion-icon>
                                            )}
                                            {Array.from({ length: 5 - Math.ceil(review.rating) }, (_, i) => (
                                                <ion-icon key={i} name="star-outline"></ion-icon>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                                <p>{review.review}</p>
                            </div>
                        ))
                    ) : (
                        <p>No reviews available.</p>
                    )}
                </div>
            </div>
        </section>
    );
};

export default Reviews;