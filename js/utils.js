/**
* JS Function showWeatherStats, shows current weather data for a city, 
* start and end date are optional parameters for limiting the time span of data
* foreacast is also passed on as an optional parameter,
* if no start/end dates are selected, current weather data is displayed
* @name: showWeatherStats
**/
function showWeatherStats(cityId, startDate=false, endDate=false, forecast=false)
{
	if(typeof cityId!="undefined" && cityId!=null && cityId>0)
	{
		try
		{
			$.ajax(
			{	
				url: baseUrl+"showWeatherStats", 
				data: { "cityId":cityId, "startDate":startDate, "endDate":endDate, "forecatst":forecast },
				type: "GET",
				success: function(result)
				{
					
				}
			});
		}			
		catch(Error) {}
	}
}