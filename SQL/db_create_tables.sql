CREATE TABLE IF NOT EXISTS `users` 
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	email VARCHAR(255) NOT NULL,
	name TEXT NOT NULL,
	user_image TEXT,
	is_superuser TINYINT(1) NOT NULL,
	is_active TINYINT(1) NOT NULL,
	reg_time DATETIME NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_cities` 
(
	user_id INTEGER NOT NULL,
	city_id INTEGER NOT NULL,
	KEY (user_id),
	KEY (city_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cities` 
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	city_name VARCHAR(255),
	lat DOUBLE,
	lng DOUBLE,
	openweather_id INT NOT NULL,
	PRIMARY KEY (id),
	KEY (openweather_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;