<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutfitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("outfits")->insert([
            ["name" => "T-Shirt rouge", "categorie" => "hot", "price" => 20],
            ["name" => "T-Shirt bleu", "categorie" => "hot", "price" => 20],
            ["name" => "T-Shirt vert", "categorie" => "hot", "price" => 20],
            ["name" => "pull rouge", "categorie" => "cold", "price" => 30],
            ["name" => "pull bleu", "categorie" => "cold", "price" => 30],
            ["name" => "pull vert", "categorie" => "cold", "price" => 30],
            ["name" => "sweat rouge", "categorie" => "lukewarm", "price" => 25],
            ["name" => "sweat bleu", "categorie" => "lukewarm", "price" => 25],
            ["name" => "sweat vert", "categorie" => "lukewarm", "price" => 25],
        ]);
    }
}
