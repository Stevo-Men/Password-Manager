-- 1. Mock data pour la table users
INSERT INTO users (username, email, password_hash, created_at, updated_at, last_login) VALUES
('steve', 'steve@gmail.com', '$2y$10$abcdefghijklmnopqrstuv', now(), now(), now()),
('bob', 'bob@gmail.com','$2y$10$uvwxyzabcdefghijklmn', now(), now(), now());

-- 2. Mock data pour la table credentials
INSERT INTO credentials (user_id, title, url, login, password_encrypted, notes, created_at, updated_at) VALUES
(1, 'Gmail',      'https://mail.google.com',  'steve@gmail.com', 'enc_pwd_ABC123', 'Compte email perso',now(), now()),
(1, 'GitHub',     'https://github.com',       'steve','enc_pwd_DEF456', 'Hébergement de code',now(), now()),
(2, 'Twitter',    'https://twitter.com',      'bob_tweet','enc_pwd_GHI789', 'Réseau social',now(), now()),
(2, 'Ma Banque',  'https://bank.example.com', 'bob123', 'enc_pwd_JKL012', 'Compte courant',now(), now());
