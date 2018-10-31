/**
* JS Function showWeatherStats, shows current weather data for a city, 
* start and end date are optional parameters for limiting the time span of data
* foreacast is also passed on as an optional parameter,
* if no start/end dates are selected, current weather data is displayed
* if data is found, it is displayed using Chart.js
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
				data: { "cityId":cityId, "startDate":startDate, "endDate":endDate, "forecast":forecast },
				type: "GET",
				dataType : "JSON",
				success: function(result)
				{
					if(typeof result!="undefined" && $.isArray(result) && result!=null && result.length>0)
					{
						var currentTimestamp = 0;
						var actualTemps = [];
						var forecastTemps = [];
						
						$.each(result, function(key,value) 
						{	
							if(value.type == 0)
								actualTemps.push({ x:value.parsed_timestamp, y:value.current_temp });
							else
								forecastTemps.push({ x:value.parsed_timestamp, y:value.current_temp });
						});
						
						if(actualTemps.length>0 || forecastTemps.length>0)
						{
							if($("#weatherResultContainer").length)
							{
							
								var dataSets = [];
								if(actualTemps.length>0)
									dataSets.push({
											label: actualTempsLbl,
											backgroundColor: "#ff9999",
											borderColor: "#ff4d4d",
											fill: false,
											data: actualTemps,
										});
								
								if(forecastTemps.length>0)
									dataSets.push({
											label: predictedTemps,
											backgroundColor: "#99c2ff",
											borderColor: "#4d94ff",
											fill: false,
											data: forecastTemps
										});
							
							
								var config = {
								type: 'line',
								data: {
									datasets: dataSets
								},
								options: {
									responsive: true,
									title: {
										display: true,
									},
									scales: {
										xAxes: [{
											type: 'time',
											time: {
												 displayFormats: {
														'millisecond': 'DD.MM.YY HH:00',
													   'second': 'DD.MM.YY HH:00',
													   'minute': 'DD.MM.YY HH:00',
													   'hour': 'DD.MM.YY HH:00',
													   'day': 'DD.MM.YY HH:00',
													   'week': 'DD.MM.YY HH:00',
													   'month': 'DD.MM.YY HH:00',
													   'quarter': 'DD.MM.YY HH:00',
													   'year': 'DD.MM.YY HH:00',
												}
											},
											display: true,
											scaleLabel: {
												display: true,
												labelString: xLabel
											}
										}],
										yAxes: [{
											display: true,
											scaleLabel: {
												display: true,
												labelString: yLabel
											}
										}]
									}
								}
								};
							
								var ctx = document.getElementById('canvas').getContext('2d');
								window.myLine = new Chart(ctx, config);
							}
						}
						else
							$("#weatherResultContainer").text(noData);
					}
				}
			});
		}			
		catch(Error) { }
	}
}