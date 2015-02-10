Authenticator:
### add donations

###1.1
    add no more support

###1.2
    1. support issues e pr
    fixa la issue migrate:refresh di 1.2
###1.3
    sto copiando un pezzo alla volta, manca:
    config
    test
    rifare un giro di tutte le parti
    1. sposta tutti i file di configurazione in config e fixa l'accesso alla confir
    2. rinomina tutte le rotte come named e mettile in un file routes
    3. sposta tutti i file in modo da avere stesso namespace ma metti tutto in app
    4. fixa il resto del sw
    5. fixa i test
    6. fixa il setup
    7. quando funziona ristuttura il resto in modo da farlo più l5 like e aggiorna i test ecc
    8. fai i test applicazione
    9. pialla gulp e vedi se vuoi usare elixir

POI (1.3.0):
 
 - add ai test il check del messaggio quando fai il recupero password
 - codeception acceptance test(prosegui dal signup)
 - create nuovi builder come growing oop tdd book
 - non usare static validation rules ma instanced e fixa il discorso degli hook a event validating
 - view + fighe e temi css
 - vedi x ricerca usando un campo keywords e per compatibilità con mssql
 - Github.io page
 - xss protection
 - quando fai il signup di un'utente già attivo rimanda la mail ma setta i msg di errore e non dovrebbe
 - facilita estensione del codice come con syntara(il discorso views override e poi altro)
 - implementa oauth login e logout
 - add facebook e twitter login
 - permetti login by username anche
 - modifica il banning per farlo ad ore
 - better email template
 - template per tutto il pannello e per parti pubbliche
 - crea delle view pubbliche per l'utente e rinfresca la gui
 - refactor tutto con commandhandler e domain event, riorganizza interazione cn sentry tutto con interfaccie ed implementazioni
    riorganizza anche directory(fatti un fork del way e vedi se puoi auto aggiornarlo ecc)
 - online demo
 - usa google recaptcha cn laravel package
 - supporto a mongo usando il userlist class come in productlist di click e le varie
    implementazioni di filtri a seconda del dbms e via!
 - set permission con gui e crea un helper che mostra i link facendo check per i permessi ad accederli dall'utente loggato
 - messaggi personalizzati in file di config e traduzione di tutto con translate laravel
 - multilingua per messaggi di errore e template
 - widget vari utilizzabili


TEST APPLICAZIONE (da creare poi in acceptance tests):

1. signup utente con:
  conferma email attivata o no
  captcha attivati o no
2. login utente dai due lati con check se ha il permesso di vedere il pannello o no
4. recupero password
4. modifica profilo utente e add e rm perm e groups
5. creazione modifica e cancellazione utente lato admin
6. associazione gruppi
7. associazione permessi
8. filtro utenti e gruppi per la ricerca con ordinamento
9. cancellazione permessi e gruppi già associati
10. dashboard dati
11. modifica profilo utente avatar e custom field
12. setup script e check crea utenti vari
