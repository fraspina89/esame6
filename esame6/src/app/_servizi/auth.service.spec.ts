import { TestBed } from '@angular/core/testing'
import { AuthService } from './auth.service'
import { Auth } from '../_type/auth.type'

describe('AuthService', () => {
  let service: AuthService

  beforeEach(() => {
    localStorage.clear()
    TestBed.configureTestingModule({})
    service = TestBed.inject(AuthService)
  })

  it('should be created', () => {
    expect(service).toBeTruthy()
  })

  it('should write and read auth from localStorage', () => {
    const auth: Auth = {
      idLingua: 1,
      tk: 'FAKE_TOKEN',
      nome: 'Mario',
      idRuolo: 2,
      idStato: 1,
      abilita: null,
      idUtente: 123
    }

    service.scriviAuthSuLocalStorage(auth)
    const read = service.leggiAuthDaLocalStorage()
    expect(read.tk).toBe('FAKE_TOKEN')
    expect(read.nome).toBe('Mario')
  })

  it('should update observable when settaObsAuth is called', (done) => {
    const auth: Auth = {
      idLingua: 1,
      tk: 'TOKEN2',
      nome: 'Luigi',
      idRuolo: 3,
      idStato: 1,
      abilita: null,
      idUtente: 99
    }

    service.leggiObsAuth().subscribe(value => {
      if (value.tk === 'TOKEN2') {
        expect(value.nome).toBe('Luigi')
        done()
      }
    })

    service.settaObsAuth(auth)
  })
})
