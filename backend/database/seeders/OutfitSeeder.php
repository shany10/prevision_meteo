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
            ["name" => "T-Shirt rouge", "categorie" => "hot"],
            ["name" => "T-Shirt bleu", "categorie" => "hot"],
            ["name" => "T-Shirt vert", "categorie" => "hot"],
            ["name" => "pull rouge", "categorie" => "cold"],
            ["name" => "pull bleu", "categorie" => "cold"],
            ["name" => "pull vert", "categorie" => "cold"],
            ["name" => "sweat rouge", "categorie" => "lukewarm"],
            ["name" => "sweat bleu", "categorie" => "lukewarm"],
            ["name" => "sweat vert", "categorie" => "lukewarm"],
        ]);
    }
}
