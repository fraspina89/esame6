// Interfaccia che rappresenta un singolo episodio di una serie TV
export interface IEpisodio {
  idEpisodio: number;
  idSerie?: number;
  titolo?: string;
  descrizione?: string;
  numeroStagione?: number;
  numeroEpisodio?: number;
  durata?: number;
  anno?: number;
  idImmagine?: number | null;
  idFilmato?: number | null;
}
