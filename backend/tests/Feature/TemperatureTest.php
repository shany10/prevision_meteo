<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\TemperatureController;
use ReflectionClass;
use Tests\TestCase;

class TemperatureTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function test_weather_api_has_well_connected()
    {
        $temperatureController = new TemperatureController();
        $method = $this->temperatureClassinstanceMethod("call_weather_api");
        $result = $method->invoke($temperatureController, ["weather" => ["city" => "Paris"]]);
        $this->assertIsArray($result);
    }

    public function test_get_temperature_method_returns_a_float_or_int_data()
    {
        $temperatureController = new TemperatureController();
        $method = $this->temperatureClassinstanceMethod("get_temperature");
        $result = $method->invoke($temperatureController, ["current" => ["temp_c" => 15]]);
        $this->assertIsNotString($result);
        $this->assertIsNotObject($result);
        $this->assertIsNotArray($result);
        $this->assertIsNotBool($result);
    }

    public function test_compare_temperature_method_accepte_only_int_or_float()
    {
        $temperatureController = new TemperatureController();
        $method = $this->temperatureClassinstanceMethod("compare_temperature");
        $result1 = $method->invoke($temperatureController, "qddsds");
        $result2 = $method->invoke($temperatureController, []);

        $this->assertIsArray($result1);
        $this->assertIsArray($result2);

    }

    public function test_compare_temperature_method_lower_10_degree_return_cold()
    {
        $temperatureController = new TemperatureController();
        $method = $this->temperatureClassinstanceMethod("compare_temperature");
        $result = $method->invoke($temperatureController, "9");
        $this->assertEquals("cold", $result);
    }

    public function test_compare_temperature_method_between_10_and_19_degree_return_lukewarm()
    {
        $temperatureController = new TemperatureController();
        $method = $this->temperatureClassinstanceMethod("compare_temperature");
        $result = $method->invoke($temperatureController, 15);
        $this->assertEquals("lukewarm", $result);
    }

    public function test_compare_temperature_method_superior_or_equal_20_degree_return_hot()
    {
        $temperatureController = new TemperatureController();
        $method = $this->temperatureClassinstanceMethod("compare_temperature");
        $result = $method->invoke($temperatureController, 20);
        $this->assertEquals("hot", $result);
    }

    public function test_find_outfit_method_accepte_only_value_cold_lukewarm_hot()
    {
        $temperatureController = new TemperatureController();
        $method = $this->temperatureClassinstanceMethod("find_outfit");
        $result1 = $method->invoke($temperatureController, "cold");
        $result2 = $method->invoke($temperatureController, "lukewarm");
        $result3 = $method->invoke($temperatureController, "hot");
        $result4 = $method->invoke($temperatureController, "sunny"); // erreur


        $this->assertArrayNotHasKey("error", $result1);
        $this->assertArrayNotHasKey("error", $result2);
        $this->assertArrayNotHasKey("error", $result3);
        $this->assertArrayHasKey("error", $result4); //erreur

    }

    public function test_the_outfit_api_returns_a_successful_response()
    {
        $response = $this->postJson('/api/getOutfit', [
            "weather" => [
                "city" => "Paris"
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonIsArray();
        // $response->assertJsonIsObject();
    }

    //method 

    private function temperatureClassinstanceMethod($method) {
        $temperatureController = new TemperatureController();
        $reflection = new ReflectionClass($temperatureController);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method;
    }
}
