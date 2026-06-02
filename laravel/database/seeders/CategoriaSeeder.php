<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creiamo le categorie di esempio
        Categoria::create(['nome' => 'Horror']);
        Categoria::create(['nome' => 'Commedia']);
        Categoria::create(['nome' => 'Azione']);
        Categoria::create(['nome' => 'Thriller']);
        Categoria::create(['nome' => 'Drammatico']);
    }
}
