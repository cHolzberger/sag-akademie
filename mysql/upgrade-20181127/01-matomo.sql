CREATE SCHEMA `matomo` ;

CREATE USER 'matomo'@'%'
  IDENTIFIED BY 'matomo1234';

GRANT ALL PRIVILEGES ON matomo.* TO 'matomo'@'%' IDENTIFIED BY 'matomo1234';
