<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use stdClass;


class TemperatureController extends Controller
{
    public function get_outfit(Request $request): JsonResponse
    {
        $request_data = $request->all();

        $response = $this->call_weather_api($request_data);
        if (!empty($response["error"])) return response()->json($response["error"], 500);

        $temperature = $this->get_temperature($response);
        if (!empty($temperature["error"])) return response()->json($temperature["error"], 500);

        $weather = $this->compare_temperature($temperature);
        if (!empty($weather["error"])) return response()->json($weather["error"], 500);

        $outfit = $this->find_outfit($weather);
        if (!empty($outfit["error"])) return response()->json($outfit["error"], 500);

        $finale_response = $this->data_constructor($request_data, $weather, $outfit);

        return response()->json($finale_response);
    }


    //METHOD

    // Récupérer les données provenant de l'api weather 
    private function call_weather_api($request_data)
    {
        $url = "http://api.weatherapi.com/v1/current.json?key=" .
            env('WEATHER_API_KEY') .
            "&q=" .
            $request_data["weather"]["city"];

        if (!empty($request_data["dt"])) {
            preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $request_data["dt"], $match);
            if (count($match) != 0) $url = $url . "&dt=" . $request_data["dt"];
            else return ["error" => [
                "code" => 500,
                "message" => "format de date invalide"
            ]];
        }

        $response = Http::get($url);
        $response = $response->json();

        return $response;
    }

    // Récupérer le temperature present dans la reponse de l'api weather
    private function get_temperature($response)
    {
        if (!is_array($response) && !is_object($response)) {
            return [
                "error" => [
                    "code" => 500,
                    "message" => "Format de donnée invalide"
                ]
            ];
        }
        if (count($response) == 0) {
            return [
                "error" => [
                    "code" => 500,
                    "message" => "Absence de temperature"
                ]
            ];
        }
        if (!empty($response["current"])) {
            return $response["current"]["temp_c"];
        } else {
            return [
                "error" => [
                    "code" => 500,
                    "message" => "Erreur indéterminée"
                ]
            ];
        }
    }

    // Comparer les températures à fin de savoir quel type de temps il s'agit (cold, lukewarm, hot)
    private function compare_temperature($temperature)
    {
        if (!is_int($temperature) && !is_float($temperature) && !is_string($temperature)) {
            return [
                "error" => [
                    "code" => 500,
                    "message" => "Format de temperature invalideeee"
                ]
            ];
        }
        preg_match('/^[\.0-9]*$/', $temperature, $match);
        if (!empty($match)) {
            if ($temperature < 10) return "cold";
            elseif ($temperature >= 10 && $temperature < 20) return "lukewarm";
            elseif ($temperature >= 20) return "hot";
        } else {
            return [
                "error" => [
                    "code" => 500,
                    "message" => "Format de temperature invalide"
                ]
            ];
        }
    }

    // Trouver les tenus approprié au temps
    private function find_outfit(string $weather)
    {
        if ($weather != "cold" && $weather != "lukewarm" && $weather != "hot") {
            return [
                "error" => [
                    "code" => 500,
                    "message" => "categorie inconnu"
                ]
            ];
        }

        return DB::table("outfits")
            ->where("categorie", $weather)
            ->select(["id", "name", "price"])
            ->get();
    }

    private function data_constructor($request_data, string $weather, $outfit)
    {
        if ($weather != "hot" && $weather != "cold" && $weather != "lukewarm") {
            return var_dump([
                "error" => "categorie inconnu",
                "code" => 500
            ]);
        }


        if (is_array($outfit) || is_object($outfit)) {
            if (count($outfit) > 0) {
                $object_data = new stdClass();
                $object_data->products = $outfit;
                $date = date("Y-m-d");
                if (!empty($request_data["dt"])) $date = $request_data["dt"];
                $object_data->weather = [
                    "city" => $request_data["weather"]["city"],
                    "is" => $weather,
                    "date" => $date
                ];

                return $object_data;
            }
        } else {
            return [
                "message" => "Article epuisé",
                "code" => 200
            ];
        }
    }
}
