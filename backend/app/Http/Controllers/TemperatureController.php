<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TemperatureController extends Controller
{
    public function get_outfit(Request $request): JsonResponse
    {
        $request_data = $request->all();
        try {
            $response = Http::get("http://api.weatherapi.com/v1/current.json?key=" .
                env('WEATHER_API_KEY') .
                "&q=" . $request_data["weather"]["city"]);
        } catch (Exception $e) {
            echo "Problème survenu lors de l'appelle de l'api Weather : $e";
            die;
        }

        $data = $response->json();
        $temperature = $this->get_temperature($data);
        $weather = $this->compare_temperature($temperature);
        $outfit = $this->find_outfit($weather);
        $final_response = $this->data_constructor($request_data["weather"]["city"], $weather, $outfit);
        return response()->json($final_response);
    }


    //TEST

    //method POST
    //exemple => http://localhost:8000/api/test_temperature
    public function test_temperature(Request $request)
    {
        $request = $request->all();
        $temperature = $this->get_temperature($request);
        var_dump($temperature);
        die;
    }

    //method GET
    //exemple => http://localhost:8000/api/test_compare_temperature/15  
    public function test_compare_temperature($temperature)
    {
        $weather = $this->compare_temperature($temperature);
        var_dump($weather);
    }

    //method GET
    //exemple => http://localhost:8000/api/test_outfit/hot 
    public function test_outfit($weather)
    {
        $outfit = $this->find_outfit($weather);
        var_dump($outfit);
    }

    //METHOD

    private function get_temperature($data)
    {
        if (!is_array($data) && ! is_object($data)) {
            var_dump("ERROR => Format de donnée invalide");
            die;
        }
        if (count($data) == 0) {
            var_dump(("ERROR => Absence de donnée"));
            die;
        }
        if (!empty($data["current"])) return $data["current"]["temp_c"];
        else if (!empty($data["error"])) {
            var_dump($data["error"]);
            die;
        } else {
            var_dump("ERROR => Erreur indéterminée");
            die;
        }
    }

    private function compare_temperature($temperature)
    {
        preg_match('/[a-z A-Z]/', $temperature, $match);
        if (!empty($match)) {
            var_dump("ERROR => Format de temperature invalide");
            die;
        }
        if ($temperature < 10) return "cold";
        elseif ($temperature >= 10 && $temperature < 20) return "lukewarm";
        elseif ($temperature >= 20) return "hot";
    }

    private function find_outfit(string $weather)
    {
        if ($weather != "cold" && $weather != "lukewarm" && $weather != "hot") {
            var_dump("ERROR => categorie inconnu");
            die;
        }

        return DB::table("outfits")
            ->where("categorie", $weather)
            ->select(["id", "name"])
            ->get();
    }

    private function data_constructor(string $city, string $weather, $outfit)
    {
        if ($weather != "hot" && $weather != "cold" && $weather != "lukewarm") {
            var_dump("ERROR => categorie inconnu");
            die;
        }
        if (is_array($outfit) || is_object($outfit)) {
            if (count($outfit) > 0) {

                $arr_data = [
                    "products" => $outfit,
                    "weather" => [
                        "city" => $city,
                        "waether" => $weather,
                        "date" => "today"
                    ]
                ];

                return $arr_data;
            }
        } else {
            var_dump("Navré il n'y a plus d'article en stock");
            die;
        }
    }
}
