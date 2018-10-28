CREATE TABLE IF NOT EXISTS `users` 
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	email VARCHAR(255) NOT NULL,
	name TEXT NOT NULL,
	user_image TEXT,
	is_superuser TINYINT(1) NOT NULL,
	is_active TINYINT(1) NOT NULL,
	reg_time INT NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_cities` 
(
	user_id INTEGER NOT NULL,
	city_id INTEGER NOT NULL,
	UNIQUE KEY (user_id, city_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cities` 
(
	id INTEGER NOT NULL AUTO_INCREMENT,
	city_name VARCHAR(255) NOT NULL,
	country_name VARCHAR(5) NOT NULL,
	lat DOUBLE NOT NULL,
	lng DOUBLE NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cities_weather` 
(
	city_id INTEGER NOT NULL,
	timestamp INT NOT NULL,
	current_temp FLOAT NOT NULL,
	temp_min FLOAT NOT NULL,
	temp_max FLOAT NOT NULL,
	humidity FLOAT NOT NULL,
	weather_speed FLOAT NOT NULL,
	type TINYINT(1) NOT NULL,
	UNIQUE KEY (city_id,timestamp,type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;