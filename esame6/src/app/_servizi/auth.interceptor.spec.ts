import { TestBed } from '@angular/core/testing'
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing'
import { HTTP_INTERCEPTORS, HttpClient } from '@angular/common/http'
import { AuthInterceptor } from './auth.interceptor'
import { AuthService } from './auth.service'
import { Router } from '@angular/router'

describe('AuthInterceptor', () => {
  let httpMock: HttpTestingController
  let http: HttpClient
  let authService: AuthService

  beforeEach(() => {
    const routerStub = { url: '/', navigate: jasmine.createSpy('navigate') }

    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [
        { provide: Router, useValue: routerStub },
        { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true },
        AuthService
      ]
    })

    httpMock = TestBed.inject(HttpTestingController)
    http = TestBed.inject(HttpClient)
    authService = TestBed.inject(AuthService)
    localStorage.clear()
  })

  afterEach(() => {
    httpMock.verify()
    localStorage.clear()
  })

  it('should add Authorization header when token present', () => {
    // prepara auth con token
    authService.settaObsAuth({ idLingua:1, tk: 'ABC123', nome: 'T', idRuolo:1, idStato:1, abilita:null, idUtente:1 })

    http.get('/api/prova').subscribe()

    const req = httpMock.expectOne('/api/prova')
    expect(req.request.headers.get('Authorization')).toBe('Bearer ABC123')
    req.flush({})
  })
})
