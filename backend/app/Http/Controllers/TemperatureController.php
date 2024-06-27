<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TemperatureController extends Controller
{

    public function get_outfit(Request $request): JsonResponse
    {
        $request_data = $request->all();
    
        $response = Http::get("http://api.weatherapi.com/v1/current.json?key=" .
            env('WEATHER_API_KEY') .
            "&q=" . $request_data["weather"]["city"]);

        $data = $response->json();
        $temperature = $this->get_temperature($data);
        $weather = $this->compare_temperature($temperature);
        $outfit = $this->find_outfit($weather);
        $final_respons = $this->data_constructor($request_data["weather"]["city"], $weather, $outfit);
        return response()->json($final_respons);
    }


    //METHOD

    private function get_temperature($data)
    {
        if (!empty($data["error"])) return "ERROR => cuntry not found";
        return $data["current"]["temp_c"];
    }

    private function compare_temperature($temperature)
    {
        if ($temperature < 10) return "cold";
        elseif ($temperature >= 10 && $temperature < 20) return "lukewarm";
        elseif ($temperature >= 20) return "hot";
    }

    private function find_outfit($weather)
    {
        return DB::table("outfits")
            ->where("categorie", $weather)
            ->select(["id", "name", "categorie"])
            ->get();
    }

    private function data_constructor($city, $weather, $outfit)
    {
        $arr_data = [
            "products" => $outfit,
            "weather" => [
                "city" => $city,
                "waether" => $weather,
                "date" => ""
            ]
        ];

        return $arr_data;
    }
}
