/**
* JS Function showWeatherStats, shows current weather data for a city, 
* start and end date are optional parameters for limiting the time span of data
* foreacast is also passed on as an optional parameter,
* if no start/end dates are selected, current weather data is displayed
* if data is found, it is displayed using Chart.js
* a table with weather results is also constructed
**/
function showWeatherStats(cityId, startDate=false, endDate=false, forecast=false, show_table=true)
{
	if(typeof cityId!="undefined" && cityId!=null && cityId>0)
	{
		$("#resultsTable").remove();
		
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
						var actualTemps = [];
						var forecastTemps = [];
						
						$.each(result, function(key,value) 
						{	
							if(value.type == 0)
								actualTemps.push({ x:value.parsed_timestamp, y:value.current_temp, label:value.readable_date, timestamp:value.timestamp });
							else
								forecastTemps.push({ x:value.parsed_timestamp, y:value.current_temp, label:value.readable_date, timestamp:value.timestamp });
						});
						
						if(actualTemps.length>0 || forecastTemps.length>0)
						{
							/* create datasets with results for graph*/
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
											label: predictedTempsLbl,
											backgroundColor: "rgba(153, 194, 255, 0.2)",
											borderColor: "rgba(77, 148, 255, 0.2)",
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
							
							
							/* construct the table with results */
							if(show_table)
							{
								var currentTimestamp = 0;
								var table = '<table border="1" id="resultsTable">\r\n<tbody>\r\n<tr>\r\n';
								table += '<td><b>'+xLabel+'</b></td><td><b>'+actualTempsLbl+'</b></td>';
								
								if(forecastTemps.length>0)
									table += '<td><b>'+predictedTempsLbl+'</b></td>';
									
								table += '</tr>';	
								
								/* group both forecasts and actual temps by date
								*  and construct table rows*/
								
								for(var i=0;i<actualTemps.length;i++)
								{
									if(currentTimestamp!=actualTemps[i].timestamp)
									{
										currentTimestamp = actualTemps[i].timestamp;
										
										/* this is not our first row, so we finish the previous one */
										if(i>0)
											table += '</tr>';
										
										table += '\r\n<tr><td>'+actualTemps[i].label+'</td><td>'+actualTemps[i].y+'</td>';
										
										/* check if we have to add forecast data to table as well */
										if(forecastTemps.length>0)
										{
											/* we need to find the most appropriate result, because forecast is done in 3 hour predictions */
											/* basically what we do is we check if the difference between two dates is less than 3 hours so we find out if our curent temp is in this interval*/
											
											var minDifference = -1;
											var forecastTemp = -1;
											
											for(var j=0; j<forecastTemps.length; j++) 
											{
												var currDiff = -1;	
												/* select the most appropriate interval - the one with smallles tim difference */	
												if(forecastTemps[j].timestamp<currentTimestamp)
													currDiff = (currentTimestamp-forecastTemps[j].timestamp)/3600;
												else
													currDiff = (forecastTemps[j].timestamp-currentTimestamp)/3600;
												
												if(minDifference==-1 || (currDiff<minDifference && currDiff>=0 && currDiff<=3))
												{
													minDifference = currDiff;
													forecastTemp = forecastTemps[i].y;
												}
											}
											
											if(minDifference>=0)
												table += '<td>'+forecastTemp+'</td>';
											
										}
										table += '</tr>';
									}
								}
								
								table += '\r\n</tbody>\r\n</table>';
								$("#weatherResultContainer").append(table);
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