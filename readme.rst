###################
What is WeatherApp
###################

WeatherApp is a small exercise task, whose main goal was to study OAuth2
and usage of one of the free weather API services. In this case, OpenWeatherMap`s API was used.


*******************
Server Requirements
*******************

PHP version 5.6 or newer is recommended.

MySQL database or equivalent is required in order to store the weather data. 

CRON jobs are required to run periodic retrieval of weather data.

************
Installation
************

1.) Set up your Google+ Oauth credentials <https://console.cloud.google.com/apis/dashboard> 
and create an authorized redirect URL for it, pointing to you server to function googleAuth in welcome controller.
Once you have your credentials set up, save them as JSON file in the credentials folder in the root of the project.

2.) Set up your database and run query scripts in SQL folder in the root of the project. 
Note: db_insert_cities.sql right now only has Slovenian cities imported.

3.) Register at OpenWeatherMap and get your API key and URL for requests to their API

3.) Update your config/config.php file 
- set up the base url
- fill out the $config['owp_api_key'] and $config['owp_api_url'] variable according to your OWP API credentials

***************
Acknowledgements
***************

- Codeigniter 3.1.9 <https://codeigniter.com/>
- ChartJS <https://www.chartjs.org/>
- Datepicker <https://jqueryui.com/datepicker/>
- OpenWeatherMap <https://openweathermap.org/>
- Google+ API for OAuth 
