drop database IF EXISTS bank;
create database bank;
use bank;

CREATE TABLE bank_customers(
    acc_no INT PRIMARY KEY NOT NULL,
    balance INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE bank_keys(
    pub_exponent VARCHAR(255) NOT NULL,
    pub_modulus VARCHAR(255) NOT NULL,
    priv_exponent VARCHAR(255) NOT NULL,
    priv_modulus VARCHAR(255) NOT NULL,
    primary key (pub_exponent, pub_modulus, priv_exponent, priv_modulus)
);

insert into bank_customers values
(123456, 4000.00, 'Jeremy Leslie', SHA('jeremy')),
(987654, 0.00, 'Shop Vendor', SHA('shop1'));


drop database IF EXISTS voucher;
create database voucher;
use voucher;

CREATE TABLE spent_vouchers(
    voucher_id VARCHAR(255) PRIMARY KEY NOT NULL
);


drop database IF EXISTS user;
create database user;
use user;

CREATE TABLE vouchers(
    voucher_id VARCHAR(255) PRIMARY KEY NOT NULL,
    voucher_signature VARCHAR(255)
);


drop database IF EXISTS marketplace;
create database marketplace;
use marketplace;

CREATE TABLE products(
    prod_id INT PRIMARY KEY NOT NULL,
    prod_name VARCHAR(255) NOT NULL,
    prod_price INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    qty INT NOT NULL
);

insert into products values
(1, 'Apple', 2, 'Juicy red apple', 20),
(2, 'Game of Thrones S01', 25, 'Season 1 of award-winning HBO hit Game of Thrones', 5);