CREATE TABLE url (
    id      INTEGER     PRIMARY KEY AUTOINCREMENT,
    url     text        NOT NULL
);

CREATE TABLE clicks (
    id      INTEGER     PRIMARY KEY AUTOINCREMENT,
    date    INTEGER     NOT NULL,
    ip      VARCHAR(39) NOT NULL,
    url_id  INTEGER     NOT NULL,
    FOREIGN KEY(url_id) REFERENCES url(id)
);
