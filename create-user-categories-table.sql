# create table structure
create table user_categories (
  id int(6) unsigned not null PRIMARY KEY AUTO_INCREMENT,
  name varchar(255) not null
) ENGINE = MYISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
# below insert common user categories names
INSERT INTO user_categories SET name = 'super admin';
INSERT INTO user_categories SET name = 'admin';
INSERT INTO user_categories SET name = 'moderator';
INSERT INTO user_categories SET name = 'editor';
INSERT INTO user_categories SET name = 'author';
INSERT INTO user_categories SET name = 'registered user';
INSERT INTO user_categories SET name = 'unregistered user';
INSERT INTO user_categories SET name = 'all users';
# push 1