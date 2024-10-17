<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
//use GuzzleHttp\Client;

class WeatherController extends Controller
{

    // Fetch weather data by city name 
    public function getWeatherByCity(Request $request)
    {

        // Validate city input 
        $request->validate(['city' => 'required|string']);
        $city = $request->input('city');
        $apiKey = env('OPENWEATHER_API_KEY');
        //error_log(print_r($apiKey, TRUE));        
        //$client = new Client();
        try {
            error_log(print_r($city, TRUE));
            // Send request to OpenWeather API 
            //$response = $client->get("https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric");
            $response = Http::withoutVerifying()->get("https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric");
            $weatherData = json_decode($response->getBody()->getContents(), true);
            // Check if the city has valid weather data 
            if (isset($weatherData['weather'])) {
                return response()->json(['success' => true, 'data' => $weatherData]);
            } else {
                return response()->json(['success' => false, 'message' => 'No weather data found for the specified city.']);
            }
        } catch (\Exception $e) {
            // Handle errors (e.g., city not found) 
            return response()->json(['success' => false, 'message' => 'Error retrieving weather data. Please try again later.',], 500);
        }
    }

    public function searchCities(Request $request)
    {
        $query = $request->input('query');
        $apiKey = env('OPENWEATHER_API_KEY');
        //$client = new Client();
        try {
            // Send request to the OpenWeather Geocoding API to search cities 
            $response = Http::withoutVerifying()->get("http://api.openweathermap.org/geo/1.0/direct?q={$query}&limit=5&appid={$apiKey}");
            $cities = json_decode($response->getBody()->getContents(), true);
            // Filter out cities that don't have weather data 
            if (!empty($cities)) {
                return response()->json(['success' => true, 'data' => $cities]);
            } else {
                return response()->json(['success' => false, 'message' => 'No matching cities found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving city data. Please try again later.',], 500);
        }
    }
}
