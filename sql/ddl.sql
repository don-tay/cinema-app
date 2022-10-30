CREATE TABLE IF NOT EXISTS movies (
    movie_id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    year INTEGER NOT NULL,
    director VARCHAR(255) NOT NULL,
    rating INTEGER NOT NULL,
    genre VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS showings (
    showing_id SERIAL PRIMARY KEY,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    movie_id INTEGER NOT NULL REFERENCES movies(movie_id),
    CONSTRAINT showing_time_constraint CHECK (start_time < end_time)
);

CREATE TABLE IF NOT EXISTS seats (
    seat_id SERIAL PRIMARY KEY,
    seat_num INTEGER NOT NULL,
    showing_id INTEGER NOT NULL REFERENCES showings(showing_id),
    CONSTRAINT seat_constraint UNIQUE (seat_num, showing_id)
);

CREATE TABLE IF NOT EXISTS tickets (
    ticket_id SERIAL PRIMARY KEY,
    ticket_uid VARCHAR(255) UNIQUE NOT NULL,
    seat_id INTEGER UNIQUE NOT NULL REFERENCES seats(seat_id),
    email VARCHAR(255) NOT NULL,
    is_ticketed BOOLEAN NOT NULL DEFAULT FALSE
);
