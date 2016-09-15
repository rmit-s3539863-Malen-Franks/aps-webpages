create database IF NOT EXISTS bank;
use bank;

CREATE TABLE bank_customers(
    acc_no INT PRIMARY KEY NOT NULL,
    balance DECIMAL(10,2) NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE bank_keys(
    bank_id INT PRIMARY KEY NOT NULL,
    pub_exponent VARCHAR(255) NOT NULL,
    pub_modulus VARCHAR(255) NOT NULL,
    priv_exponent VARCHAR(255) NOT NULL,
    priv_modulus VARCHAR(255) NOT NULL
);

insert into bank_customers values
(123456, 4000.00, 'Jeremy Leslie', SHA('jeremy'));

create database IF NOT EXISTS website;
use website;

CREATE TABLE website_customers(
    user_id VARCHAR(255) PRIMARY KEY NOT NULL,
    password VARCHAR(255) NOT NULL
);

insert into website_customers values
('jeremyles', SHA('jeremy'));

create database IF NOT EXISTS voucher;
use voucher;

CREATE TABLE vouchers(
    user_id VARCHAR(255) NOT NULL REFERENCES website_customers(user_id),
    voucher_id VARCHAR(255) NOT NULL,
    voucher_signature VARCHAR(255)
);

CREATE TABLE spent_vouchers(
    voucher_id VARCHAR(255) NOT NULL
);