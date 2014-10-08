
DROP TABLE IF EXISTS tasks ;

CREATE TABLE tasks (
	id int PRIMARY KEY AUTO_INCREMENT,
	title varchar(255) NOT NULL,
	content text,
	status enum('open', 'closed') NOT NULL DEFAULT 'open'
) ENGINE innodb;

DROP TABLE IF EXISTS taskTime;

CREATE TABLE taskTime (
	id int PRIMARY KEY AUTO_INCREMENT,
	taskId int NOT NULL REFERENCES tasks(id),
	date varchar(255),
	hours int NOT NULL
) ENGINE innodb;
