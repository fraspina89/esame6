export interface IContatto {
  idContatto: number;
  nome: string | null;
  cognome: string | null;
  nomeCompleto: string | null;
  email: string | null;
  codiceFiscale: string | null;
  partitaIva: string | null;
  sesso: number | null;
  dataNascita: string | null;
  cittaNascita: string | null;
  idContattoStato?: number | null;
  ruoli?: IRuolo[];
  recapiti?: any[];
  indirizzi?: any[];
  crediti?: any[];
  created_at?: string | null;
  updated_at?: string | null;
}

export interface IRuolo {
  idContattoRuolo: number;
  nome: string;
}
