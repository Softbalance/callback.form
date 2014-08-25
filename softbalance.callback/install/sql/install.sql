CREATE TABLE softbalance_callback(
	ID int NOT NULL auto_increment,
	CREATED datetime NOT NULL,
	NAME varchar(50) NOT NULL,
	STATUS varchar(20) NOT NULL,
	PHONE varchar(20) NOT NULL,
	USER_COMMENT varchar(500) NULL,
	ADMIN_COMMENT varchar(500) NULL,
	SITE_ID varchar(10) NOT NULL,
	primary key (ID)
);