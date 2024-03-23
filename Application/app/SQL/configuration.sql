create database if not exists matteo_franzolin_ecommerce;
create table if not exists matteo_franzolin_ecommerce.products
(
    id     int not null auto_increment primary key,
    nome   varchar(50),
    prezzo float,
    marca  varchar(50)
);
