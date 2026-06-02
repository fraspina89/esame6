<?php

namespace App\Providers;

use App\Models\ContattoRuolo;
use App\Models\ContattoAbilita;
use App\Models\Contatto;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // TODO: Riattivare quando tutti i model sono completi
        /*
        if (app()->environment() !== 'testing') {
            // gate basato su un ruolo
            // usa il FQCN per evitare ambiguità con il middleware omonimo
            \App\Models\ContattoRuolo::all()->each(
                function (\App\Models\ContattoRuolo $ruolo) {
                    Gate::define($ruolo->nome, function (Contatto $contatto) use ($ruolo) {
                        return $contatto->ruoli->contains('nome', $ruolo->nome);
                    });
                }
            );
        }
        */
    }
}
