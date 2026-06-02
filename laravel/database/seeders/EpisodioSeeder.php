<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Episodio;

class EpisodioSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Episodio::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $episodi = [

            // ===== idSerie=1: La Casa dei Segreti (Horror) - Stagione 1 =====
            ['idSerie'=>1,'titolo'=>'Il Primo Segno',     'descrizione'=>"La famiglia Ferretti arriva alla villa. Nella prima notte, strani rumori svegliano tutti.",           'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>48,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>1,'titolo'=>'La Porta Proibita',  'descrizione'=>"Nel seminterrato viene scoperta una porta murata. Nessuno sa cosa ci sia dall'altra parte.",          'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>51,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>1,'titolo'=>'Voci dal Buio',      'descrizione'=>"La figlia minore inizia a parlare con una presenza invisibile. I genitori sono preoccupati.",         'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>46,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>1,'titolo'=>'La Verita Nascosta', 'descrizione'=>"Documenti trovati in soffitta rivelano la storia oscura della villa e dei suoi precedenti abitanti.", 'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>52,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>1,'titolo'=>'Il Ritorno',         'descrizione'=>"Un ex abitante della casa torna dopo vent anni con risposte e nuove domande.",                        'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>49,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=2: Notte Senza Fine (Horror) - Stagione 1 =====
            ['idSerie'=>2,'titolo'=>'Il Buio Scende',    'descrizione'=>"Il sole non sorge piu. Gli abitanti di Valterra capiscono che qualcosa di soprannaturale e cominciato.", 'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>47,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>2,'titolo'=>'Luci nella Notte',  'descrizione'=>"Le luci delle case iniziano a spegnersi una dopo l altra. Chi resta nell oscurita non torna piu.",    'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>49,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>2,'titolo'=>'La Creatura',       'descrizione'=>"Due ragazzi avvistano qualcosa tra gli alberi. Nessuno crede alla loro storia, ma la creatura torna.", 'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>51,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>2,'titolo'=>'Sacrificio',        'descrizione'=>"Un antico rituale potrebbe fermare l oscurita. Qualcuno deve compiere la scelta impossibile.",         'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>53,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>2,'titolo'=>"L Alba",            'descrizione'=>"Il paese affronta la creatura nel suo covo. Il sole tornera, ma a quale prezzo?",                     'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>55,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=3: Il Culto (Horror) - Stagione 1 =====
            ['idSerie'=>3,'titolo'=>'Infiltrata',        'descrizione'=>"La giornalista Carla si avvicina alla setta fingendosi una nuova adepte. I rituali la turbano subito.", 'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>46,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>3,'titolo'=>'Il Cerchio',        'descrizione'=>"Il primo rito completo rivela simboli che Carla ha gia visto in foto di crimini irrisolti.",            'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>48,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>3,'titolo'=>'Il Maestro',        'descrizione'=>"Carla incontra il leader della setta. Carismatico e pericolosissimo, sembra sapere chi e davvero.",   'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>50,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>3,'titolo'=>'La Notte dei Riti', 'descrizione'=>"La cerimonia centrale si avvicina. Carla non puo andarsene senza rischiare la vita.",                 'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>52,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>3,'titolo'=>'Esposizione',       'descrizione'=>"Carla trova le prove ma viene scoperta. La fuga diventa una corsa contro il tempo e il terrore.",      'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>54,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=4: Tutto in Famiglia (Commedia) - Stagione 1 =====
            ['idSerie'=>4,'titolo'=>'Benvenuti nel Caos',          'descrizione'=>"La famiglia Ricci si riunisce per le vacanze. Come sempre, tutto va storto fin dall inizio.",    'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>27,'anno'=>2022,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>'Il Pranzo della Domenica',    'descrizione'=>"Il pranzo settimanale diventa un campo di battaglia tra suocera e nuora.",                       'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>25,'anno'=>2022,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>'Nessuno Tocca il Telecomando','descrizione'=>"La guerra per il telecomando divide la famiglia in fazioni contrapposte.",                     'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>26,'anno'=>2022,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>'La Cena dei Disastri',        'descrizione'=>"Paolo invita il capo a cena. La famiglia fa di tutto per impressionarlo nel modo sbagliato.",    'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>28,'anno'=>2022,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>'Il Cane di Nonna',            'descrizione'=>"Nonna porta a casa un cane enorme. Caos garantito per tutti gli abitanti.",                      'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>24,'anno'=>2022,'idImmagine'=>null,'idFilmato'=>null],
            // ===== idSerie=4: Tutto in Famiglia (Commedia) - Stagione 2 =====
            ['idSerie'=>4,'titolo'=>'Nuovo Anno, Stessa Famiglia', 'descrizione'=>"Capodanno in casa Ricci: brindisi, litigi, e promesse che nessuno mantersa.",                  'numeroStagione'=>2,'numeroEpisodio'=>1,'durata'=>26,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>'Il Collega Invadente',        'descrizione'=>"Il collega di Paolo si trasferisce per due settimane. Diventa presto l ospite indesiderato.",   'numeroStagione'=>2,'numeroEpisodio'=>2,'durata'=>27,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>"La Dieta di Papa",            'descrizione'=>"Paolo decide di mettersi a dieta. La famiglia soffre le conseguenze del suo cattivo umore.",    'numeroStagione'=>2,'numeroEpisodio'=>3,'durata'=>25,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>'Sorpresa!',                   'descrizione'=>"La festa a sorpresa per Elena finisce con sorprese per tutti, anche per chi l ha organizzata.", 'numeroStagione'=>2,'numeroEpisodio'=>4,'durata'=>28,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>4,'titolo'=>'Finalmente Insieme',          'descrizione'=>"Tutti i nodi vengono al pettine in modo esplosivo e commovente.",                                'numeroStagione'=>2,'numeroEpisodio'=>5,'durata'=>32,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=5: Ufficio Disastri (Commedia) - Stagione 1 =====
            ['idSerie'=>5,'titolo'=>'Prima Giornata',          'descrizione'=>"Andrea e il nuovo assunto. Scopre subito che in quest ufficio regna il caos piu totale.",         'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>25,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>5,'titolo'=>'La Stampante Maledetta',  'descrizione'=>"La stampante dell ufficio ha una vita propria. Nessun tecnico riesce a domarla.",                 'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>24,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>5,'titolo'=>'Riunione Infinita',       'descrizione'=>"Una riunione che doveva durare 30 minuti si trasforma in un odissea di 8 ore.",                   'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>26,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>5,'titolo'=>'Il Bonus',                'descrizione'=>"La voce di un bonus straordinario mette tutti i colleghi l uno contro l altro.",                  'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>25,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>5,'titolo'=>'Festa Aziendale',         'descrizione'=>"La festa di Natale aziendale raggiunge livelli epici di imbarazzo e disastro.",                   'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>28,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=6: Chef per Caso (Commedia) - Stagione 1 =====
            ['idSerie'=>6,'titolo'=>"L Eredita Inaspettata", 'descrizione'=>"Filippo, ingegnere, eredita uno stellato ristorante dallo zio. Non sa nemmeno bollire l acqua.",  'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>27,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>6,'titolo'=>'Il Critico Gastronomico','descrizione'=>"Un famoso critico gastronomico prenota un tavolo. Filippo e nel panico totale.",                   'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>25,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>6,'titolo'=>'Assunzioni Improbabili', 'descrizione'=>"Cercando personale, Filippo assume figure tutt altro che adatte alla cucina stellata.",            'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>26,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>6,'titolo'=>'La Ricetta Segreta',     'descrizione'=>"Esiste una ricetta segreta dello zio che potrebbe salvare il ristorante. Dove trovare gli ingredienti?", 'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>29,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>6,'titolo'=>'Prima Stella',           'descrizione'=>"Il ristorante viene rivalutato dalla Michelin. Filippo scopre di aver imparato molto piu di quanto credeva.", 'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>30,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=7: Operazione Tempesta (Azione) - Stagione 1 =====
            ['idSerie'=>7,'titolo'=>'Sotto Copertura',    'descrizione'=>"L agente Bruno viene infiltrato in una rete criminale internazionale. Il rischio e altissimo.",    'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>53,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>7,'titolo'=>'Fuoco Incrociato',   'descrizione'=>"Un operazione di routine si trasforma in uno scontro a fuoco nel cuore della citta.",              'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>51,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>7,'titolo'=>'Il Traditore',       'descrizione'=>"Qualcuno all interno del team sta passando informazioni al nemico. Chi e?",                         'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>55,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>7,'titolo'=>"Caccia all Uomo",    'descrizione'=>"Bruno scopre l identita del terrorista e deve catturarlo prima che colpisca.",                      'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>52,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>7,'titolo'=>"L Ultimo Assalto",   'descrizione'=>"Il team si riorganizza per l operazione finale. Nessuno e al sicuro.",                             'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>56,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=8: Unita Zero (Azione) - Stagione 1 =====
            ['idSerie'=>8,'titolo'=>'Off the Grid',       'descrizione'=>"L Unita Zero viene attivata per la prima missione: neutralizzare una minaccia biologica segreta.",  'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>52,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>8,'titolo'=>'Alleanze Fragili',   'descrizione'=>"Per completare la missione, la squadra deve fidarsi di una fonte doppia. Scelta rischiosa.",         'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>54,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>8,'titolo'=>'Punto di Non Ritorno','descrizione'=>"La missione si complica. Un membro della squadra viene catturato. L orologio scorre.",              'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>56,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>8,'titolo'=>'Fuoco Amico',        'descrizione'=>"La squadra scopre che un agenzia alleata li ha usati come pedine. Ora sono bersagli.",              'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>55,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>8,'titolo'=>'Zero Day',           'descrizione'=>"L Unita si gioca tutto in un unica operazione notturna su suolo nemico.",                           'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>58,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=9: Frontiera (Azione) - Stagione 1 =====
            ['idSerie'=>9,'titolo'=>'Confine',            'descrizione'=>"L agente Marco insospettisce su una serie di passaggi al confine troppo silenziosi per essere casuali.", 'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>50,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>9,'titolo'=>'Il Corriere',        'descrizione'=>"Un giovane corriere viene fermato. Tra la sua merce c e qualcosa che non dovrebbe esistere.",        'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>53,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>9,'titolo'=>'Il Mandante',        'descrizione'=>"La pista porta a un nome insospettabile. Marco non sa di chi fidarsi all interno del suo stesso team.", 'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>52,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>9,'titolo'=>'Caccia Aperta',      'descrizione'=>"Marco viene messo fuori servizio, ma continua l indagine in proprio. Il rischio e mortale.",         'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>54,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>9,'titolo'=>'La Rete Cade',       'descrizione'=>"Operazione finale: smantellare il traffico dall interno. Non tutti torneranno.",                     'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>57,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=10: Codice Oscuro (Thriller) - Stagione 1 =====
            ['idSerie'=>10,'titolo'=>'Il Messaggio Cifrato','descrizione'=>"Un omicidio in apparenza casuale cela un codice che collega vittime lontane tra loro.",            'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>50,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>10,'titolo'=>'Il Primo Sospetto',  'descrizione'=>"La detective Russo identifica il primo indiziato. Ma qualcosa non torna.",                         'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>48,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>10,'titolo'=>'Tracce nel Buio',    'descrizione'=>"Una vecchia foto trovata sulla scena del crimine apre scenari inattesi.",                          'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>51,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>10,'titolo'=>"L Agente Doppio",    'descrizione'=>"Un membro della squadra investigativa risulta compromesso. La fiducia crolla.",                    'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>53,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>10,'titolo'=>'Verita Rivelata',    'descrizione'=>"Il movente viene svelato. La verita e piu oscura di quanto chiunque potesse immaginare.",           'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>59,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=11: Doppio Gioco (Thriller) - Stagione 1 =====
            ['idSerie'=>11,'titolo'=>'Il Cliente',         'descrizione'=>"L avvocato Giulia accetta un caso che sembra semplice. Il cliente e innocente, ma qualcuno lo vuole in galera.", 'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>49,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>11,'titolo'=>'Le Prove Sparite',   'descrizione'=>"Prove fondamentali scompaiono dal tribunale. Giulia capisce che c e corruzione ai massimi livelli.", 'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>51,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>11,'titolo'=>'Il Testimone',       'descrizione'=>"Un testimone chiave viene trovato morto. Giulia e sola contro un sistema che la vuole silenziare.",  'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>53,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>11,'titolo'=>'La Trappola Legale', 'descrizione'=>"Giulia viene accusata di ostruzione alla giustizia. Difendere il cliente diventa difendere se stessa.", 'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>55,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>11,'titolo'=>'Verdetto',           'descrizione'=>"L udienza finale. Giulia ha una sola possibilita per smascherare il vero responsabile.",             'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>57,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=12: La Testimone (Thriller) - Stagione 1 =====
            ['idSerie'=>12,'titolo'=>'Nella Trappola Sbagliata','descrizione'=>"Elena assiste per caso a un omicidio. Prima di poter denunciare, capisce che i killer la sanno gia.", 'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>48,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>12,'titolo'=>'Nessuno da Chiamare', 'descrizione'=>"La polizia non le crede o peggio: e infiltrata. Elena deve sopravvivere da sola.",                   'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>50,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>12,'titolo'=>'Il Rifugio',          'descrizione'=>"Elena trova aiuto in uno sconosciuto. Ma fidarsi di lui e un rischio che potrebbe costarle la vita.", 'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>52,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>12,'titolo'=>'Identita Nascosta',   'descrizione'=>"Per sfuggire ai cacciatori, Elena e costretta a cancellare ogni traccia di se stessa.",              'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>54,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>12,'titolo'=>'La Verita',           'descrizione'=>"Elena decide di smettere di scappare. E ora di combattere e portare i killer davanti alla giustizia.", 'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>56,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=13: Oltre il Confine (Drammatico) - Stagione 1 =====
            ['idSerie'=>13,'titolo'=>'La Partenza',        'descrizione'=>"Amara e la sua famiglia lasciano tutto cio che conoscono. Il viaggio comincia.",                   'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>52,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>13,'titolo'=>'Terra Straniera',    'descrizione'=>"Il primo impatto con l Europa e duro. Lingua, usanze, freddo: tutto e diverso.",                   'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>50,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>13,'titolo'=>'Radici',             'descrizione'=>"I ricordi del paese d origine si scontrano con la realta quotidiana del presente.",                 'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>53,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>13,'titolo'=>'La Scelta',          'descrizione'=>"Amara deve scegliere tra restare fedele alle proprie origini o integrarsi nel nuovo mondo.",        'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>55,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>13,'titolo'=>'Il Ritorno a Casa',  'descrizione'=>"La famiglia riflette sul percorso fatto. Casa non e piu un luogo, ma un sentimento.",               'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>60,'anno'=>2024,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=14: Il Tempo Ritrovato (Drammatico) - Stagione 1 =====
            ['idSerie'=>14,'titolo'=>'Ritorno',            'descrizione'=>"Carlo torna al paese natale dopo trent anni. Non sa ancora quanto il passato lo stia aspettando.",  'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>52,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>14,'titolo'=>'Le Ferite Antiche',  'descrizione'=>"Il faccia a faccia con la madre anziana riapre ferite mai davvero rimarginate.",                    'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>54,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>14,'titolo'=>'La Sorella',         'descrizione'=>"Carlo scopre l esistenza di una sorella che non sapeva di avere. I segreti di famiglia si moltiplicano.", 'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>51,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>14,'titolo'=>'Lettera dal Passato','descrizione'=>"Una lettera scritta dal padre decenni fa cambia tutto cio che Carlo credeva di sapere.",            'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>55,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>14,'titolo'=>'Riconciliazione',    'descrizione'=>"Tre generazioni si siedono allo stesso tavolo per la prima volta. Il silenzio si rompe finalmente.", 'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>58,'anno'=>2023,'idImmagine'=>null,'idFilmato'=>null],

            // ===== idSerie=15: Voci Lontane (Drammatico) - Stagione 1 =====
            ['idSerie'=>15,'titolo'=>'Il Primo Suono',     'descrizione'=>"Luca sente la propria voce per la prima volta. L emozione e travolgente e destabilizzante insieme.", 'numeroStagione'=>1,'numeroEpisodio'=>1,'durata'=>50,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>15,'titolo'=>'Il Rumore del Mondo','descrizione'=>"I suoni quotidiani diventano insopportabili. Luca lotta per trovare un equilibrio tra silenzio e suono.", 'numeroStagione'=>1,'numeroEpisodio'=>2,'durata'=>52,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>15,'titolo'=>'La Musica',          'descrizione'=>"Luca scopre la musica. Per lui e una lingua nuova che parla direttamente all anima.",               'numeroStagione'=>1,'numeroEpisodio'=>3,'durata'=>53,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>15,'titolo'=>'La Comunita',        'descrizione'=>"Luca si allontana dalla comunita dei non udenti che lo ha cresciuto. Il dolore e enorme.",           'numeroStagione'=>1,'numeroEpisodio'=>4,'durata'=>54,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
            ['idSerie'=>15,'titolo'=>'Due Mondi',          'descrizione'=>"Luca capisce che non deve scegliere tra i due mondi: puo appartenere a entrambi.",                  'numeroStagione'=>1,'numeroEpisodio'=>5,'durata'=>56,'anno'=>2025,'idImmagine'=>null,'idFilmato'=>null],
        ];

        foreach ($episodi as $episodio) {
            Episodio::create($episodio);
        }
    }
}