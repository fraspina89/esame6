import { IEpisodio } from './IEpisodio';

export interface ICategoriaSerie {
  id: number;
  nome: string;
  descrizione?: string;
}

// Interfaccia che rappresenta una serie TV nel catalogo (allineata con SerieTvResource)
export interface ISerie {
  idSerie: number;
  nome: string;
  descrizione?: string;
  totaleStagioni?: number;
  numeroEpisodio?: number;
  regista?: string;
  attori?: string;
  annoInizio?: number;
  annoFine?: number | null;
  locandina?: string | null;
  carousel?: string | null;
  video?: string | null;
  is_in_corso?: boolean;
  categoria?: ICategoriaSerie;
  episodi?: IEpisodio[];
  episodi_count?: number;
}
