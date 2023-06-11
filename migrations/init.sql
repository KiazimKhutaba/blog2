create table if not exists posts
(
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at text default current_timestamp
);


CREATE INDEX idx_title ON posts2(title);


/**
FOR MYSQL
 */
create table if not exists users
(
    id         int auto_increment
        primary key,
    email      varchar(255)       not null,
    password   varchar(64)                        not null,
    created_at datetime default CURRENT_TIMESTAMP null
);
