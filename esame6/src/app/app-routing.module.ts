/*
  AppRoutingModule
  - Definisce le rotte principali dell'applicazione.
  - Contiene la rotta per la `PrimaPagina`, il `Login` e la pagina di `TestAuth`.
*/
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './_servizi/auth.guard';

// Elenco delle rotte pubbliche (non richiedono autenticazione). Usato come "whitelist"
export const PUBLIC_ROUTES: string[] = [
  '/',
  '/login'
]

const routes: Routes = [
  { path: '', loadChildren: () => import('./_pagine/prima-pagina/prima-pagina.module').then(m => m.PrimaPaginaModule) },
  { path: 'login', loadChildren: () => import('./_pagine/login/login.module').then(m => m.LoginModule) },
  { path: 'catalogo', canLoad: [AuthGuard], canActivate: [AuthGuard], loadChildren: () => import('./_pagine/catalogo/catalogo.module').then(m => m.CatalogoModule) },
  { path: 'catalogo/categoria/:idCategoria', canLoad: [AuthGuard], canActivate: [AuthGuard], loadChildren: () => import('./_pagine/catalogo-categoria/catalogo-categoria.module').then(m => m.CatalogoCategoriaModule) },
  { path: 'catalogo/:id', canLoad: [AuthGuard], canActivate: [AuthGuard], loadChildren: () => import('./_pagine/film-dettaglio/film-dettaglio.module').then(m => m.FilmDettaglioModule) },
  { path: 'serie-tv/:id', canLoad: [AuthGuard], canActivate: [AuthGuard], loadChildren: () => import('./_pagine/serie-tv-dettaglio/serie-tv-dettaglio.module').then(m => m.SerieTvDettaglioModule) },
  { path: 'profilo', canLoad: [AuthGuard], canActivate: [AuthGuard], loadChildren: () => import('./_pagine/profilo/profilo.module').then(m => m.ProfiloModule) },
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
