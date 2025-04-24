
INSERT INTO users (username, email, password_hash, created_at, updated_at, last_login) VALUES
('steve', 'steve@gmail.com', '$2y$12$98qLQ9nC0SwPQoKJNhFvp.LpgeZm1EMPwYQcH5VW0wAa11SEmc1ky', now(), now(), now()),
('bob', 'bob@gmail.com','$2y$10$uvwxyzabcdefghijklmn', now(), now(), now());

INSERT INTO credentials (user_id, title, url, login, password_encrypted, notes, created_at, updated_at) VALUES
(1, 'Gmail',      'https://mail.google.com',  'steve@gmail.com', 'enc_pwd_ABC123', 'Compte email perso',now(), now()),
(1, 'GitHub',     'https://github.com',       'steve','enc_pwd_DEF456', 'Hébergement de code',now(), now()),
(2, 'Twitter',    'https://twitter.com',      'bob_tweet','enc_pwd_GHI789', 'Réseau social',now(), now()),
(2, 'Ma Banque',  'https://bank.example.com', 'bob123', 'enc_pwd_JKL012', 'Compte courant',now(), now());
