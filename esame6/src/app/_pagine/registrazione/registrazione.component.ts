import { Component, Input, OnDestroy, OnInit, AfterViewInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Subject, takeUntil, debounceTime, distinctUntilChanged, map, Observable, of } from 'rxjs';
import { Router, NavigationStart } from '@angular/router';
import { filter } from 'rxjs/operators';
import { ApiService } from '../../_servizi/api.service';
import { IRrispostaServer } from '../../_interfacce/IRispostaServer.interface';
import { NgbDropdown } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-registrazione',
  templateUrl: './registrazione.component.html',
  styleUrls: ['./registrazione.component.scss']
})
export class RegistrazioneComponent implements OnInit, AfterViewInit, OnDestroy {

  @Input() emailIniziale: string = ''

  form: FormGroup
  busy: boolean = false
  msg: string | null = null
  submitted: boolean = false

  private destroy$ = new Subject<void>()
  comuniAll: any[] = []
  comuniSuggeriti: any[] = []
  capSuggeriti: string[] = []
  nazioniAll: any[] = []
  nazioniSuggerite: any[] = []
  // opzioni per ng-select (sesso)
  genderOptions = [
    { value: 'M', label: 'M - Maschile' },
    { value: 'F', label: 'F - Femminile' }
  ]
  selectedGenderLabel: string | null = null

  selectGender(value: string, label: string) {
    this.form.get('sesso')?.setValue(value)
    this.selectedGenderLabel = label
  }
  comuniIndirizzoSuggeriti: any[] = []
  capIndirizzoSuggeriti: string[] = []
  selectedCap: string | null = null
  @ViewChild('ddCap') ddCapRef!: NgbDropdown

  // Tre select per data di nascita
  readonly giorni: number[] = Array.from({ length: 31 }, (_, i) => i + 1);
  readonly mesi: { num: number; nome: string }[] = [
    { num: 1, nome: 'Gennaio' }, { num: 2, nome: 'Febbraio' }, { num: 3, nome: 'Marzo' },
    { num: 4, nome: 'Aprile' }, { num: 5, nome: 'Maggio' }, { num: 6, nome: 'Giugno' },
    { num: 7, nome: 'Luglio' }, { num: 8, nome: 'Agosto' }, { num: 9, nome: 'Settembre' },
    { num: 10, nome: 'Ottobre' }, { num: 11, nome: 'Novembre' }, { num: 12, nome: 'Dicembre' }
  ];
  readonly anni: number[] = Array.from({ length: 100 }, (_, i) => new Date().getFullYear() - 16 - i);
  selectedGiorno: number | null = null;
  selectedMese: number | null = null;
  selectedAnno: number | null = null;
  // model bound to the ngbDatepicker input (removed - using three select boxes)

  constructor(private fb: FormBuilder, private api: ApiService, private router: Router) {
    this.form = this.fb.group({
      nome: ['', [Validators.required, Validators.minLength(2)]],
      cognome: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      dataNascita: ['', [Validators.required]],
      cittaNascita: ['', [Validators.required, Validators.minLength(2)]],
      provinciaNascita: ['', [Validators.required, Validators.minLength(1)]],
      capNascita: ['', []],
      sesso: ['', [Validators.required]],
      codiceFiscale: ['', [Validators.required]],
      nazionalita: ['', [Validators.required, Validators.minLength(2)]],
      cittadinanza: ['', [Validators.required, Validators.minLength(2)]],
      telefono: [''],
      indirizzo: this.fb.group({
        street: ['', [Validators.required, Validators.minLength(3)]],
        civico: ['', [Validators.required]],
        city: ['', [Validators.required, Validators.minLength(2)]],
        zip: ['', []],
        province: ['', []]
      }),
      password: ['', [Validators.required, Validators.minLength(8), Validators.pattern(/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).+$/)]],
      confermaPassword: ['', [Validators.required]],
      accettaTermini: [false, [Validators.requiredTrue]]
    })
  }

 



  ngAfterViewInit(): void {
    // Calcolo automatico del codice fiscale quando cambiano i dati rilevanti
    const controlsToWatch = ['nome', 'cognome', 'dataNascita', 'cittaNascita', 'provinciaNascita', 'sesso']
    controlsToWatch.forEach(name => {
      const ctrl = this.form.get(name)
      if (!ctrl) return
      ctrl.valueChanges.pipe(takeUntil(this.destroy$)).subscribe(() => this.aggiornaCodiceFiscale())
    })

    // Carica liste statiche/di riferimento una sola volta
    this.api.getComuniItaliani().pipe(takeUntil(this.destroy$)).subscribe({
      next: (res: IRrispostaServer) => {
        const payload = res && !res.error ? res.data : null
        // Normalizza: alcuni endpoint possono restituire { data: [...], meta: {...} }
        if (payload && Array.isArray(payload)) this.comuniAll = payload
        else if (payload && payload.data && Array.isArray(payload.data)) this.comuniAll = payload.data
        else this.comuniAll = []
        console.log('RegistrazioneComponent: comuni caricati', this.comuniAll.length)
      },
      error: (err) => {
        console.warn('RegistrazioneComponent: errore caricamento comuni', err)
        this.comuniAll = []
      }
    })

    this.api.getNazioni().pipe(takeUntil(this.destroy$)).subscribe({
      next: (res: IRrispostaServer) => {
        const npayload = res && !res.error ? res.data : null
        if (npayload && Array.isArray(npayload)) this.nazioniAll = npayload
        else if (npayload && npayload.data && Array.isArray(npayload.data)) this.nazioniAll = npayload.data
        else this.nazioniAll = []
        console.log('RegistrazioneComponent: nazioni caricate', this.nazioniAll.length)
      },
      error: (err) => {
        console.warn('RegistrazioneComponent: errore caricamento nazioni', err)
        this.nazioniAll = []
      }
    })

    // Autocomplete: filtra localmente con debounce
    const cittaCtrl = this.form.get('cittaNascita')
    if (cittaCtrl) {
      cittaCtrl.valueChanges.pipe(
        debounceTime(300),
        distinctUntilChanged(),
        map((term: string) => (term || '').toString().trim().toLowerCase()),
        takeUntil(this.destroy$)
      ).subscribe((term: string) => {
        if (!term) { this.comuniSuggeriti = []; this._codiceCatastale = ''; return }
        const matches = this.comuniAll.filter(c => {
          const nome = (c.comune || c.nome || '').toString().toLowerCase()
          const prov = (c.sigla_provincia || c.provincia || '').toString().toLowerCase()
          return nome.includes(term) || prov.includes(term)
        })
        // Se c'è una sola corrispondenza, auto-compila città/provincia/CAP
        if (matches.length === 1) {
          const m = matches[0]
          const nomeVal = m.comune ?? m.nome ?? ''
          const provVal = m.sigla_provincia ?? m.provincia ?? ''
          this.form.get('cittaNascita')?.setValue(nomeVal, { emitEvent: false })
          this.form.get('provinciaNascita')?.setValue(provVal, { emitEvent: false })

          // Salva codice catastale per il CF
          this._codiceCatastale = (m.codice_catastale ?? m.codice_istat ?? '').toString()

          // Controlla PRIMA il range CAP (priorità su cap singolo)
          const startRange = parseInt((m.cap_iniziale ?? '').toString(), 10)
          const endRange = parseInt((m.cap_finale ?? '').toString(), 10)
          const hasRange = !isNaN(startRange) && !isNaN(endRange) && startRange > 0 && endRange > 0 && startRange !== endRange

          if (hasRange) {
            this.capSuggeriti = this.generateCapRange(startRange, endRange)
            this.form.get('capNascita')?.setValue('', { emitEvent: false })
          } else {
            const capCandidate = (m.cap ?? m.cap_principale ?? m.postal_code ?? m.zip ?? '').toString()
            if (capCandidate) {
              this.form.get('capNascita')?.setValue(capCandidate, { emitEvent: false })
            } else {
              console.log('RegistrazioneComponent: nessun CAP trovato per', nomeVal)
            }
            this.capSuggeriti = []
          }

          this.comuniSuggeriti = []
          this.aggiornaCodiceFiscale()
          console.log('RegistrazioneComponent: ricerca comune (unica) trovati 1')
          return
        }

        this.comuniSuggeriti = matches.slice(0, 8)
        console.log('RegistrazioneComponent: ricerca comune', term, 'trovati', matches.length, 'suggeriti', this.comuniSuggeriti.length)
      })
    }

    const nazCtrl = this.form.get('nazionalita')
    if (nazCtrl) {
      nazCtrl.valueChanges.pipe(
        debounceTime(300),
        distinctUntilChanged(),
        map((term: string) => (term || '').toString().trim().toLowerCase()),
        takeUntil(this.destroy$)
      ).subscribe((term: string) => {
        if (!term) { this.nazioniSuggerite = []; return }
        this.nazioniSuggerite = this.nazioniAll.filter(n => (n.nome || '').toLowerCase().includes(term)).slice(0, 8)
      })
    }

    // Autocomplete indirizzo: filtra localmente su city
    const cityCtrl = this.form.get('indirizzo.city')
    if (cityCtrl) {
      cityCtrl.valueChanges.pipe(
        debounceTime(300),
        distinctUntilChanged(),
        map((term: string) => (term || '').toString().trim().toLowerCase()),
        takeUntil(this.destroy$)
      ).subscribe((term: string) => {
        if (!term) { this.comuniIndirizzoSuggeriti = []; return }
        this.comuniIndirizzoSuggeriti = this.comuniAll
          .filter(c => (c.comune || '').toString().toLowerCase().includes(term))
          .slice(0, 8)
      })
    }

    // Lookup via CAP indirizzo
    const zipCtrl = this.form.get('indirizzo.zip')
    if (zipCtrl) {
      zipCtrl.valueChanges.pipe(
        debounceTime(300),
        distinctUntilChanged(),
        map((v: string) => (v || '').toString().trim()),
        takeUntil(this.destroy$)
      ).subscribe((cap: string) => {
        if (!cap) { this.comuniIndirizzoSuggeriti = []; return }
        const capNum = parseInt(cap.replace(/\D/g, ''), 10)
        if (isNaN(capNum)) { this.comuniIndirizzoSuggeriti = []; return }
        const matches = this.comuniAll.filter(c => {
          if ((c.cap ?? '').toString() === cap) return true
          const s = c.cap_iniziale ? parseInt(c.cap_iniziale as any, 10) : NaN
          const e = c.cap_finale ? parseInt(c.cap_finale as any, 10) : NaN
          return !isNaN(s) && !isNaN(e) && capNum >= s && capNum <= e
        })
        if (matches.length === 1) {
          const m = matches[0]
          this.form.get('indirizzo.city')?.setValue(m.comune ?? m.nome ?? '', { emitEvent: false })
          this.form.get('indirizzo.province')?.setValue(m.sigla_provincia ?? m.provincia ?? '', { emitEvent: false })
          this.comuniIndirizzoSuggeriti = []
          this.capIndirizzoSuggeriti = []
        } else if (matches.length > 1) {
          this.comuniIndirizzoSuggeriti = matches.slice(0, 8)
        } else {
          this.comuniIndirizzoSuggeriti = []
        }
      })
    }

    // Autocomplete / lookup via CAP per la città di nascita
    const capCtrl = this.form.get('capNascita')
    if (capCtrl) {
      capCtrl.valueChanges.pipe(
        debounceTime(300),
        distinctUntilChanged(),
        map((v: string) => (v || '').toString().trim()),
        takeUntil(this.destroy$)
      ).subscribe((cap: string) => {
        if (!cap) { this.comuniSuggeriti = []; return }
        const capNum = parseInt(cap.replace(/\D/g, ''), 10)
        if (isNaN(capNum)) { this.comuniSuggeriti = []; return }

        // Cerca corrispondenze: cap esatto oppure in range cap_iniziale-cap_finale
        const matches = this.comuniAll.filter(c => {
          const ccap = (c.cap ?? c.cap_iniziale ?? '').toString()
          if (ccap === cap) return true
          const start = c.cap_iniziale ? parseInt(c.cap_iniziale as any, 10) : NaN
          const end = c.cap_finale ? parseInt(c.cap_finale as any, 10) : NaN
          if (!isNaN(start) && !isNaN(end)) {
            return capNum >= start && capNum <= end
          }
          return false
        })

        if (matches.length === 1) {
          // Riempie automaticamente città e provincia
          const m = matches[0]
          this.form.get('cittaNascita')?.setValue(m.comune ?? m.nome ?? '', { emitEvent: true })
          this.form.get('provinciaNascita')?.setValue(m.sigla_provincia ?? m.provincia ?? '', { emitEvent: true })
          // Imposta anche il CAP se presente nel record (non riattivare il listener del CAP)
          const capVal = (m.cap ?? m.cap_iniziale ?? '')
          if (capVal) this.form.get('capNascita')?.setValue(capVal, { emitEvent: false })
          this.comuniSuggeriti = []
          this.aggiornaCodiceFiscale()
        } else if (matches.length > 1) {
          // Mostra suggerimenti (limita a 8)
          this.comuniSuggeriti = matches.slice(0, 8)
        } else {
          this.comuniSuggeriti = []
        }
      })
    }
  }

  ngOnInit(): void {
    // Pre-compila email se passata dalla home page
    if (this.emailIniziale) {
      this.form.get('email')?.setValue(this.emailIniziale)
    }
    // Log delle navigazioni per diagnosticare redirect involontari
    this.router.events.pipe(filter(e => e instanceof NavigationStart), takeUntil(this.destroy$)).subscribe((e: any) => {
      console.log('RegistrazioneComponent: NavigationStart detected', e.url, e);
    })
  }

  onDatePartChange(dd?: any): void {
    if (this.selectedGiorno && this.selectedMese && this.selectedAnno) {
      const iso = `${this.selectedAnno}-${String(this.selectedMese).padStart(2, '0')}-${String(this.selectedGiorno).padStart(2, '0')}`;
      this.form.get('dataNascita')?.setValue(iso);
      this.form.get('dataNascita')?.markAsTouched();
      try { if (dd) dd.close() } catch (e) { }
    } else {
      this.form.get('dataNascita')?.setValue(null);
    }
  }

  /** Ritorna una label leggibile per il bottone dropdown della data */
  get selectedDateLabel(): string {
    if (this.selectedGiorno && this.selectedMese && this.selectedAnno) {
      const gg = String(this.selectedGiorno).padStart(2, '0')
      const mm = String(this.selectedMese).padStart(2, '0')
      return `${gg}/${mm}/${this.selectedAnno}`
    }
    return 'Data di nascita *'
  }

  aggiornaCodiceFiscale(): void {
    const v = this.form.value
    const isoData: string = v.dataNascita ?? ''
    console.log('aggiornaCodiceFiscale: valori correnti', {
      nome: v.nome, cognome: v.cognome, dataNascita: isoData, sesso: v.sesso,
      cittaNascita: v.cittaNascita, provinciaNascita: v.provinciaNascita, codiceCatastale: this._codiceCatastale
    })
    if (!v.nome || !v.cognome || !isoData || !v.sesso) return
    try {
      const luogo = `${v.cittaNascita ?? ''}${v.provinciaNascita ? ' ' + v.provinciaNascita : ''}`.trim()
      const cf = this.calcolaCodiceFiscale(v.cognome, v.nome, isoData, v.sesso, luogo)
      console.log('aggiornaCodiceFiscale: calcolato CF', cf)
      this.form.get('codiceFiscale')?.setValue(cf, { emitEvent: false })
    } catch (e) { console.error('aggiornaCodiceFiscale: errore calcolo CF', e) }
  }

  // Generatore  codice fiscale (include il carattere di controllo).

  calcolaCodiceFiscale(cognome: string, nome: string, dataNascita: string, sesso: string, luogo: string): string {
    const s = (str: string) => (str || '').toUpperCase().replace(/[^A-Z]/g, '')
    const cons = (str: string) => s(str).replace(/[AEIOU]/g, '')
    const vocali = (str: string) => s(str).replace(/[^AEIOU]/g, '')

    const codifica3 = (str: string, isName = false) => {
      const c = cons(str)
      if (isName && c.length > 3) return c[0] + c[2] + c[3]
      let out = c.slice(0, 3)
      if (out.length === 3) return out
      const v = vocali(str)
      out += (v + 'XXX').slice(0, 3 - out.length)
      while (out.length < 3) out += 'X'
      return out
    }

    const cogn = codifica3(cognome)
    const nom = codifica3(nome, true)

    const d = new Date(dataNascita)
    if (isNaN(d.getTime())) throw new Error('data invalida')
    const year = String(d.getFullYear()).slice(-2)
    const monthMap: any = { 0: 'A', 1: 'B', 2: 'C', 3: 'D', 4: 'E', 5: 'H', 6: 'L', 7: 'M', 8: 'P', 9: 'R', 10: 'S', 11: 'T' }
    const month = monthMap[d.getMonth()]
    let day = d.getDate()
    if ((sesso || '').toUpperCase() === 'F' || (sesso || '').toUpperCase() === 'FEMMINA') day += 40
    const dayStr = ('0' + day).slice(-2)

    // Codice luogo segnaposto: usa 'Z000' se non fornito
    const placeCode = this.getPlaceCode(luogo)

    const partial = cogn + nom + year + month + dayStr + placeCode
    const control = this.codiceControllo(partial)
    const result = (partial + control).toUpperCase()
    console.log('calcolaCodiceFiscale:', { partial, placeCode, control, result })
    return result
  }

  // Restituisce il codice catastale (belfiore) del comune selezionato.
  // Viene popolato da selectComune() quando l'utente sceglie un comune dalla lista.
  private _codiceCatastale: string = ''
  private _idNazioneNascita: number | null = null

  getPlaceCode(luogo: string): string {
    if (this._codiceCatastale) return this._codiceCatastale.toUpperCase().slice(0, 4)
    return 'Z000'
  }

  codiceControllo(cf15: string): string {
    const oddMap: any = {
      '0': 1, '1': 0, '2': 5, '3': 7, '4': 9, '5': 13, '6': 15, '7': 17, '8': 19, '9': 21,
      'A': 1, 'B': 0, 'C': 5, 'D': 7, 'E': 9, 'F': 13, 'G': 15, 'H': 17, 'I': 19, 'J': 21,
      'K': 2, 'L': 4, 'M': 18, 'N': 20, 'O': 11, 'P': 3, 'Q': 6, 'R': 8, 'S': 12, 'T': 14,
      'U': 16, 'V': 10, 'W': 22, 'X': 25, 'Y': 24, 'Z': 23
    }
    const evenMap: any = {
      '0': 0, '1': 1, '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9,
      'A': 0, 'B': 1, 'C': 2, 'D': 3, 'E': 4, 'F': 5, 'G': 6, 'H': 7, 'I': 8, 'J': 9,
      'K': 10, 'L': 11, 'M': 12, 'N': 13, 'O': 14, 'P': 15, 'Q': 16, 'R': 17, 'S': 18, 'T': 19,
      'U': 20, 'V': 21, 'W': 22, 'X': 23, 'Y': 24, 'Z': 25
    }
    const table = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    let sum = 0
    for (let i = 0; i < cf15.length; i++) {
      const ch = cf15[i]
      if (i % 2 === 0) sum += oddMap[ch] ?? 0
      else sum += evenMap[ch] ?? 0
    }
    const idx = sum % 26
    return table[idx]
  }
  submit(): void {
    console.log('RegistrazioneComponent.submit invoked', { valid: this.form.valid, value: this.form.value });
    this.submitted = true
    if (this.form.invalid) {
      this.form.markAllAsTouched()
      return
    }
    this.busy = true
    this.msg = null

    const formValue = this.form.value
    // Mappa M→1, F→2 per il backend (campo sesso integer)
    const sessoMap: Record<string, number> = { 'M': 1, 'F': 2 }
    const payload = {
      nome: formValue.nome,
      cognome: formValue.cognome,
      user: formValue.email,
      password: formValue.password,
      sesso: sessoMap[formValue.sesso] ?? null,
      codiceFiscale: formValue.codiceFiscale ?? null,
      cittadinanza: formValue.cittadinanza ?? null,
      cittaNascita: (typeof formValue.cittaNascita === 'object' && formValue.cittaNascita) ? (formValue.cittaNascita.comune ?? formValue.cittaNascita.nome ?? null) : (formValue.cittaNascita ?? null),
      provinciaNascita: (typeof formValue.provinciaNascita === 'object' && formValue.provinciaNascita) ? (formValue.provinciaNascita.sigla_provincia ?? formValue.provinciaNascita.provincia ?? null) : (formValue.provinciaNascita ?? null),
      dataNascita: formValue.dataNascita ?? null,
      idNazioneNascita: this._idNazioneNascita ?? null,
      telefono: formValue.telefono || null,
      via: (formValue.indirizzo?.street || '').trim() || null,
      civico: (formValue.indirizzo?.civico || '').trim() || null,
      cittaResidenza: formValue.indirizzo?.city || null,
      capResidenza: formValue.indirizzo?.zip || null
    }

    this.api.registra(payload)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (res: IRrispostaServer) => {
          if (res && !res.error) {
            this.msg = 'Registrazione inviata, controlla la tua email.'
            try {
              const toastEl = document.getElementById('registerSuccessToast')
              if (toastEl) {
                // @ts-ignore
                const bsToast = new (window as any).bootstrap.Toast(toastEl, { delay: 3500 })
                bsToast.show()
              }
            } catch (e) { }
          } else {
            this.msg = 'Errore: ' + (res.error ?? 'unknown')
          }
          this.busy = false
        },
        error: (err) => {
          this.msg = 'Errore di connessione'
          this.busy = false
        }
      })
  }

  selectComune(item: any): void {
    if (!item) return
    const nome = item.comune ?? item.nome ?? ''
    const prov = item.sigla_provincia ?? item.provincia ?? ''
    this.form.get('cittaNascita')?.setValue(nome)
    this.form.get('provinciaNascita')?.setValue(prov)

    // Controlla PRIMA se esiste un range CAP valido 
    // Salva il codice catastale per il calcolo del CF
    this._codiceCatastale = (item.codice_catastale ?? item.codice_istat ?? '').toString()

    const start = parseInt((item.cap_iniziale ?? '').toString(), 10)
    const end = parseInt((item.cap_finale ?? '').toString(), 10)
    const hasRange = !isNaN(start) && !isNaN(end) && start > 0 && end > 0 && start !== end

    if (hasRange) {
      // Mostra la lista di CAP selezionabili, non impostare automaticamente nessuno
      this.capSuggeriti = this.generateCapRange(start, end)
      this.form.get('capNascita')?.setValue('', { emitEvent: false })
      this.comuniSuggeriti = []
      this.aggiornaCodiceFiscale()
      return
    }

    // Nessun range: cerca il CAP univoco
    let capVal = (item.cap ?? item.cap_principale ?? item.postal_code ?? item.zip ?? '').toString()

    // Se non presente direttamente, cerca nel dataset completo
    if (!capVal) {
      const found = this.comuniAll.find((c: any) => {
        const cname = (c.comune ?? c.nome ?? '').toString().toLowerCase()
        const cprov = (c.sigla_provincia ?? c.provincia ?? '').toString().toLowerCase()
        return cname === nome.toString().toLowerCase() && cprov === prov.toString().toLowerCase() && (c.cap || c.cap_principale || c.postal_code || c.zip)
      })
      if (found) capVal = found.cap ?? found.cap_principale ?? found.postal_code ?? found.zip ?? ''
    }

    if (capVal) {
      this.form.get('capNascita')?.setValue(capVal, { emitEvent: false })
    } else {
      console.log('RegistrazioneComponent: nessun CAP trovato per selezione', nome, prov)
    }
    this.capSuggeriti = []
    this.comuniSuggeriti = []
    this.aggiornaCodiceFiscale()
  }

  selectNazione(item: any): void {
    if (!item) return
    const nome = item.nome ?? item.nazione ?? ''
    this.form.get('nazionalita')?.setValue(nome)
    this.nazioniSuggerite = []
  }

  generateCapRange(start: number, end: number): string[] {
    const a = Math.min(start, end)
    const b = Math.max(start, end)
    const arr: string[] = []
    // produce in ordine decrescente come richiesto (es. 10156..10121)
    for (let v = b; v >= a; v--) arr.push(String(v))
    return arr
  }

  selectComuneIndirizzo(item: any): void {
    if (!item) return
    const nome = item.comune ?? item.nome ?? ''
    const prov = item.sigla_provincia ?? item.provincia ?? ''
    this.form.get('indirizzo.city')?.setValue(nome)
    this.form.get('indirizzo.province')?.setValue(prov)
    const start = parseInt((item.cap_iniziale ?? '').toString(), 10)
    const end = parseInt((item.cap_finale ?? '').toString(), 10)
    const hasRange = !isNaN(start) && !isNaN(end) && start > 0 && end > 0 && start !== end
    if (hasRange) {
      this.capIndirizzoSuggeriti = this.generateCapRange(start, end)
      this.selectedCap = null
      this.form.get('indirizzo.zip')?.setValue('', { emitEvent: false })
      setTimeout(() => { try { this.ddCapRef?.open() } catch (e) { } }, 50)
    } else {
      const capVal = (item.cap ?? item.cap_principale ?? item.postal_code ?? '').toString()
      if (capVal) {
        this.form.get('indirizzo.zip')?.setValue(capVal, { emitEvent: false })
        this.selectedCap = capVal
      }
      this.capIndirizzoSuggeriti = []
    }
    this.comuniIndirizzoSuggeriti = []
  }

  selectCapIndirizzo(cap: string): void {
    this.form.get('indirizzo.zip')?.setValue(cap, { emitEvent: false })
    this.capIndirizzoSuggeriti = []
  }

  selectCap(cap: string): void {
    this.form.get('capNascita')?.setValue(cap, { emitEvent: false })
    this.capSuggeriti = []
  }

  ngOnDestroy(): void {
    this.destroy$.next()
    this.destroy$.complete()
  }

  togglePwd(): void {
    const pwd = document.getElementById('password') as HTMLInputElement | null
    if (!pwd) return
    pwd.type = pwd.type === 'password' ? 'text' : 'password'
  }

  apriCalendario(): void { /* rimosso: gestito da NgbInputDatepicker */ }

  dateFocused: boolean = false

  /** Restituisce true se il campo è invalido e il form è stato toccato/inviato */
  isInvalid(field: string): boolean {
    const ctrl = this.form.get(field)
    return !!(ctrl && ctrl.invalid && (ctrl.touched || this.submitted))
  }

  onDateFocus(): void { this.dateFocused = true }

  onDateBlur(): void { this.dateFocused = false }

  // ===== NgbTypeahead search functions =====

  // Typeahead per città di nascita
  searchCitta = (text$: Observable<string>): Observable<any[]> =>
    text$.pipe(
      debounceTime(200),
      distinctUntilChanged(),
      map(term => {
        if (!term || term.length < 2) return []
        const t = term.toLowerCase()
        return this.comuniAll
          .filter((c: any) => (c.comune || c.nome || '').toLowerCase().includes(t))
          .slice(0, 8)
      })
    )

  formatterCitta = (item: any): string => {
    if (typeof item === 'string') return item
    return item?.comune ?? item?.nome ?? ''
  }

  onSelectCitta(event: any): void {
    event.preventDefault() // impedisce a ngbTypeahead di scrivere l'oggetto nel form control
    const item = event.item
    if (!item) return
    this.selectComune(item)
  }

  // Typeahead per nazionalità
  searchNazione = (text$: Observable<string>): Observable<any[]> =>
    text$.pipe(
      debounceTime(200),
      distinctUntilChanged(),
      map(term => {
        if (!term || term.length < 2) return []
        const t = term.toLowerCase()
        return this.nazioniAll
          .filter((n: any) => (n.nome || '').toLowerCase().includes(t))
          .slice(0, 8)
      })
    )

  formatterNazione = (item: any): string => {
    if (typeof item === 'string') return item
    return item?.nome ?? ''
  }

  onSelectNazione(event: any): void {
    event.preventDefault() // impedisce a ngbTypeahead di scrivere l'oggetto nel form control
    const item = event.item
    if (!item) return
    this.form.get('nazionalita')?.setValue(item.nome ?? '', { emitEvent: false })
    this._idNazioneNascita = item.idNazione ?? null
    this.nazioniSuggerite = []
  }

  onCapChange(dd: any): void {
    if (this.selectedCap) {
      this.form.get('indirizzo.zip')?.setValue(this.selectedCap, { emitEvent: false })
      this.form.get('indirizzo.zip')?.markAsTouched()
      try { dd.close() } catch (e) { }
    }
  }

  // Typeahead per città di residenza (indirizzo)
  searchCittaIndirizzo = (text$: Observable<string>): Observable<any[]> =>
    text$.pipe(
      debounceTime(200),
      distinctUntilChanged(),
      map(term => {
        if (!term || term.length < 2) return []
        const t = term.toLowerCase()
        return this.comuniAll
          .filter((c: any) => (c.comune || c.nome || '').toLowerCase().includes(t))
          .slice(0, 8)
      })
    )

  formatterCittaIndirizzo = (item: any): string => {
    if (typeof item === 'string') return item
    return item?.comune ?? item?.nome ?? ''
  }

  onSelectCittaIndirizzo(event: any): void {
    event.preventDefault() // impedisce a ngbTypeahead di scrivere l'oggetto nel form control
    const item = event.item
    if (!item) return
    this.selectComuneIndirizzo(item)
  }
}
