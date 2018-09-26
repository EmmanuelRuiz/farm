CREATE DATABASE IF NOT EXISTS plat_ranchos;
USE plat_ranchos;

CREATE TABLE users(
id			int(255) auto_increment not null,
role		varchar(20),
name		varchar(20),
lastname	varchar(50),
email		varchar(50),
password	varchar(255),
image		varchar(255),
created_at	datetime DEFAULT NULL,
updated_at	datetime DEFAULT NULL,
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE categories(
id			int(255) auto_increment not null,
name		varchar(20),
description	varchar(500),
created_at	datetime DEFAULT NULL,
CONSTRAINT pk_category PRIMARY KEY(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE products(
id				int(255) auto_increment not null,
user_id			int(255) not null,
category_id		int(255) not null,
product_name	varchar(20),
description		varchar(1000),
image			varchar(255),
available		varchar(5),
price 			decimal(9,2),
created_at		datetime DEFAULT NULL,
updated_at		datetime DEFAULT NULL,
CONSTRAINT pk_products PRIMARY KEY(id),
CONSTRAINT fk_products_users foreign key(user_id) references users(id),
CONSTRAINT fk_products_categories foreign key (category_id) references categories(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8;