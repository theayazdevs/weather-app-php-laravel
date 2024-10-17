<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* Add some styling for the weather display */
        body {
            background-color: #e0f7fa;
            padding: 50px;
            font-family: Arial, sans-serif;
            /* For background image */
            /*background-image: url('/images/default.png');*/
            background: linear-gradient(0deg, rgba(31, 98, 127, 0.9), rgba(11, 72, 3, 0.9)), url('/images/default.png');
            background-size: cover;
            /* Cover the entire screen */
            background-repeat: repeat;
            /* Prevent repetition */
            background-attachment: fixed;
            /* Keep the background fixed while scrolling */
            animation: slide 10s linear infinite;
            /* Adjust duration and timing as needed */
            opacity: 0.9;
        }

        @keyframes slide {
            0% {
                background-position: 60px, -60px;
            }

            50% {
                background-position: -200px, -200px;
            }

            100% {
                background-position: 60px, -60px;
            }
        }

        .weather-container {
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            background: #ffffff;
            max-width: 500px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .weather-icon {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container weather-container">
        <h1>Weather App</h1>
        <!--
        <form id="weatherForm"> <input type="text" id="cityInput" class="form-control" placeholder="Enter city name"
                required> <button type="submit" class="btn btn-primary mt-3">Get Weather</button> </form>
        -->
        <form id="weatherForm"> <input type="text" id="cityInput" class="form-control" placeholder="Enter city name"
                required autocomplete="off">
            <div id="citySuggestions" class="list-group"></div>
            <!-- City suggestions  -->
            <button type="submit" class="btn btn-primary mt-3">Get Weather</button>
        </form>
        <div id="weatherData" class="mt-5"></div>
        <div id="errorMessage" class="error-message"></div>
    </div>

    <script>
        let weatherCondition;
        $(document).ready(function() {
            // Live search functionality for cities 
            $('#cityInput').on('input', function() {
                let query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: '/search-cities',
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(response) {
                            $('#citySuggestions').empty();
                            if (response.success) {
                                let cities = response.data;
                                cities.forEach(function(city) {
                                    $('#citySuggestions').append(
                                        ` <a href="#" class="list-group-item list-group-item-action" data-city="${city.name},${city.country}"> ${city.name}, ${city.country} </a> `
                                    );
                                });
                                // Click event for selecting a city from the list 
                                $('.list-group-item').on('click', function(e) {
                                    e.preventDefault();
                                    let city = $(this).data('city');
                                    $('#cityInput').val(city);
                                    $('#citySuggestions').empty();
                                    // Clear suggestions after selection 
                                });
                            } else {
                                $('#citySuggestions').html(
                                    `<p class="list-group-item text-danger">${response.message}</p>`
                                );
                            }
                        },
                        error: function() {
                            $('#citySuggestions').html(
                                '<p class="list-group-item text-danger">Error searching for cities.</p>'
                            );
                        }
                    });
                } else {
                    $('#citySuggestions').empty();
                    // Clear suggestions if query is too short 
                }
            });
            // Submit form to get weather data 
            $('#weatherForm').on('submit', function(e) {
                e.preventDefault();
                $('#errorMessage').empty();
                $('#weatherData').empty();
                let city = $('#cityInput').val();
                $.ajax({
                    url: '/weather',
                    method: 'GET',
                    data: {
                        city: city
                    },
                    success: function(response) {
                        if (response.success) {
                            const weather = response.data;
                            const iconUrl =
                                `http://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png`;
                            $('#weatherData').html(
                                ` <h3>${weather.name}, ${weather.sys.country}</h3> <img src="${iconUrl}" class="weather-icon" alt="Weather Icon"> <p>${weather.weather[0].description}</p> <p>Temperature: ${weather.main.temp}°C</p> `
                            );
                            weatherCondition = weather.weather[0].main;
                            //console.log(weather.weather[0].main);

                            // adding new code for images background START
                            //showing custom background images
                            // "Clear", "Clouds", "Rain", etc. 
                            // Example: Map weather conditions to custom images 
                            let backgroundImage = '';
                            switch (weatherCondition) {
                                case 'Clear':
                                    //backgroundImage = 'url(/images/sunny.jpg)';
                                    backgroundImage =
                                        'linear-gradient(0deg, rgba(35, 101, 35, 0.6), rgba(35, 101, 35, 0.6)), url(/images/sunny.jpg)';
                                    break;
                                case 'Clouds':
                                    backgroundImage =
                                        'linear-gradient(0deg, rgba(35, 101, 35, 0.4), rgba(35, 101, 35, 0.4)),  url(/images/cloudy.jpg)';
                                    break;
                                case 'Rain':
                                    backgroundImage =
                                        'linear-gradient(0deg, rgba(35, 101, 35, 0.4), rgba(35, 101, 35, 0.4)),  url(/images/rainy.jpg)';
                                    break;

                                default:
                                    backgroundImage =
                                        'linear-gradient(0deg, rgba(31, 98, 127, 0.9), rgba(11, 72, 3, 0.9)), url(/images/default.png)';
                            }
                            // Set the background image based on the weather condition 
                            //$('body').css('background-image', backgroundImage);
                            //background: linear-gradient(0deg, rgba(31, 98, 127, 0.9), rgba(11, 72, 3, 0.9)), url('/images/default.png');
                            $('body').css(
                                'background',
                                backgroundImage);
                            //END
                        } else {
                            $('#errorMessage').text(response.message);
                        }
                    },
                    error: function() {
                        $('#errorMessage').text('Error retrieving weather data.');
                    }
                });
            });
        });
    </script>

</body>

</html>
