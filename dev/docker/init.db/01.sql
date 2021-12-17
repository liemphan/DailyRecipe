# create test database
CREATE DATABASE IF NOT EXISTS `dailyrecipe-test`;

# grant rights
GRANT ALL PRIVILEGES ON `dailyrecipe-test`.* TO 'dailyrecipe-test'@'%';
