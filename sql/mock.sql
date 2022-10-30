-- mock data
INSERT INTO movies (title, year, director, rating, genre, description, image_url) VALUES
    ('The Shawshank Redemption', 1994, 'Frank Darabont', 9.3, 'Drama', 'Two imprisoned', 'https://upload.wikimedia.org/wikipedia/en/8/81/ShawshankRedemptionMoviePoster.jpg'),
    ('The Godfather', 1972, 'Francis Ford Coppola', 9.2, 'Crime', 'The aging patriarch', 'https://upload.wikimedia.org/wikipedia/en/1/1c/Godfather_ver1.jpg'),
    ('The Godfather: Part II', 1974, 'Francis Ford Coppola', 9.0, 'Crime', 'The early life', 'https://upload.wikimedia.org/wikipedia/en/0/03/Godfather_part_ii.jpg'),
    ('The Dark Knight', 2008, 'Christopher Nolan', 9.0, 'Action', 'When the menace', 'https://upload.wikimedia.org/wikipedia/en/1/1c/The_Dark_Knight_%282008_film%29.jpg'),
    ('12 Angry Men', 1957, 'Sidney Lumet', 8.9, 'Drama', 'A jury holdout', 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/12_Angry_Men_%281957_film_poster%29.jpg/1280px-12_Angry_Men_%281957_film_poster%29.jpg');

INSERT IGNORE INTO showings (start_time, end_time, movie_id)
    SELECT m2.start_time, m2.start_time + INTERVAL '2' HOUR, RAND() * 5 + 1
    FROM movies m
    CROSS JOIN  
    (SELECT movie_id, DATE_FORMAT(NOW() + INTERVAL '1' DAY + INTERVAL (RAND() * 24) HOUR, "%Y-%m-%d %H:00:00") AS start_time FROM movies) m2;

INSERT INTO seats (seat_num, showing_id)
    SELECT ROW_NUMBER() OVER (PARTITION BY s.showing_id ORDER BY s.showing_id), s.showing_id
    FROM showings s, showings s2;

INSERT INTO tickets (ticket_uid, seat_id, email) VALUES
    ('tix-612a34tfa2ab01', 1, 'john@example.com'),
    ('tix-612a34tfa2ab02', 2, 'contact@hellodon.dev'),
    ('tix-612a34tfa2ab03', 13, 'abc@example.com'),
    ('tix-612a34tfa2ab04', 14,'bcd@example.com'),
    ('tix-612a34tfa2ab05', 16, 'cde@example.com'),
    ('tix-612a34tfa2ab06', 20, 'def@example.com'),
    ('tix-612a34tfa2ab07', 25, 'efg@example.com'),
    ('tix-612a34tfa2ab08', 26, 'fgh@example.com'),
    ('tix-612a34tfa2ab09', 35, 'ghi@example.com'),
    ('tix-612a34tfa2ab10', 42, 'hij@example.com'),
    ('tix-612a34tfa2ab11', 24, 'efg@example.com'),
    ('tix-612a34tfa2ab12', 27, 'fgh@example.com'),
    ('tix-612a34tfa2ab13', 36, 'ghi@example.com'),
    ('tix-612a34tfa2ab14', 54, 'hij@example.com');
