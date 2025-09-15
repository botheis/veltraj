-- Create user and database

CREATE USER if not exists "veltraj"@"localhost" identified by "veltraj";
grant all privileges on veltraj.* to "veltraj"@"localhost" identified by "veltraj";
flush privileges;

drop database if exists veltraj;
create database if not exists veltraj;
use veltraj;

drop table if exists versions;
create table if not exists versions(
    number int not null default 0, primary key(number)
);

insert into versions(number) values (0);
