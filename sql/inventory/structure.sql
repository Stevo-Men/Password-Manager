-- ##################################################################################################################
-- USERS
-- ##################################################################################################################

CREATE TABLE users (
                       id               SERIAL PRIMARY KEY,
                       firstname        TEXT NOT NULL,
                       lastname         TEXT NOT NULL,
                       username         TEXT NOT NULL UNIQUE,
                       email            TEXT NOT NULL,
                       salt             TEXT NOT NULL,
                       password_hash    TEXT NOT NULL,
                       email_hash       TEXT NOT NULL UNIQUE,
                       avatar_path      TEXT NULL,
                       created_at       TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
                       updated_at       TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
                       last_login       TIMESTAMP WITHOUT TIME ZONE
);


CREATE TABLE credentials (
                             id                 SERIAL PRIMARY KEY,
                             title              TEXT NOT NULL UNIQUE,
                             url                TEXT,
                             login         TEXT NOT NULL,
                             password_encrypted TEXT NOT NULL,
                             notes              TEXT,
                             created_at         TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
                             updated_at         TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
                             user_id            INTEGER NOT NULL
                                 REFERENCES users(id)
                                     ON DELETE CASCADE
                                     ON UPDATE CASCADE
);


CREATE TABLE audit_logs (
                            id           SERIAL PRIMARY KEY,
                            action       TEXT NOT NULL,
                            target_type  TEXT NOT NULL,
                            target_id    INTEGER,
                            ip_address   TEXT NOT NULL,
                            created_at   TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
                            user_id      INTEGER REFERENCES users(id)
                                ON DELETE SET NULL
                                ON UPDATE CASCADE
);


CREATE OR REPLACE FUNCTION trg_set_updated_at()
  RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at := now();
RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER users_set_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION trg_set_updated_at();

CREATE TRIGGER credentials_set_updated_at
    BEFORE UPDATE ON credentials
    FOR EACH ROW
    EXECUTE FUNCTION trg_set_updated_at();
