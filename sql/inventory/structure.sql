-- ##################################################################################################################
-- USERS
-- ##################################################################################################################

CREATE TABLE users (
                       id               SERIAL PRIMARY KEY,
                       username         VARCHAR(100) NOT NULL UNIQUE,
                       email            VARCHAR(255) NOT NULL UNIQUE,
                       password_hash    VARCHAR(255) NOT NULL,
                       created_at       TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
                       updated_at       TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
                       last_login       TIMESTAMP WITHOUT TIME ZONE
);


CREATE TABLE credentials (
                             id                 SERIAL PRIMARY KEY,
                             title              VARCHAR(150) NOT NULL,
                             url                TEXT,
                             login              VARCHAR(100),
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
                            action       VARCHAR(100) NOT NULL,
                            target_type  VARCHAR(50),
                            target_id    INTEGER,
                            ip_address   VARCHAR(45),
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
