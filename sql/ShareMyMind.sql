CREATE DATABASE IF NOT EXISTS smm DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE smm;

CREATE TABLE users
(
	user_id              INTEGER NOT NULL,
	user_name            VARCHAR(10) NOT NULL,
	password             TEXT NOT NULL,
	first_name           VARCHAR(20) NOT NULL,
	last_name            VARCHAR(20) NOT NULL,
	email                VARCHAR(50) NOT NULL,
	token                TEXT NULL,
	birthdate            DATE NOT NULL,
	share_color          VARCHAR(20) NULL
);

ALTER TABLE users ADD PRIMARY KEY (user_id);
ALTER TABLE users CHANGE COLUMN user_id user_id INTEGER NOT NULL AUTO_INCREMENT;
ALTER TABLE users ADD UNIQUE (user_name);
ALTER TABLE users ADD UNIQUE (email);

CREATE TABLE shares
(
	share_id             INTEGER NOT NULL,
	text                 VARCHAR(120) NOT NULL,
	created_at           TIMESTAMP NOT NULL,
	user_id              INTEGER NOT NULL
);

ALTER TABLE shares ADD PRIMARY KEY (share_id);
ALTER TABLE shares CHANGE COLUMN share_id share_id INTEGER NOT NULL AUTO_INCREMENT;

CREATE TABLE likes
(
	share_id             INTEGER NOT NULL,
	user_id              INTEGER NOT NULL
);

ALTER TABLE likes ADD PRIMARY KEY (share_id,user_id);

ALTER TABLE shares ADD FOREIGN KEY R_1 (user_id) REFERENCES users (user_id);

ALTER TABLE likes ADD FOREIGN KEY R_2 (share_id) REFERENCES shares (share_id);

ALTER TABLE likes ADD FOREIGN KEY R_3 (user_id) REFERENCES users (user_id);