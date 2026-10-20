# Grafica coordinata statica (Drupal 10)

Tema Drupal `ut_gc` che riproduce header e footer della grafica coordinata UniTS
**senza alcuna richiesta verso gc.units.it** (niente JSON, CSS, font o icone remoti)
e con i contenuti **modificabili da interfaccia**.

Differenza rispetto a `ut_grafica_coordinata` di onlineforms (units117): lì il JSON è
copiato nel tema e stampato con `|raw`, quindi per cambiare un'etichetta si edita il
JSON. Qui il markup è nei template Twig (stessi id e classi, quindi stessi CSS) e i
contenuti arrivano da Drupal.

## Cosa si modifica e dove

| Elemento | Dove |
|---|---|
| Logo header (immagine) | Aspetto › Impostazioni › UniTS Grafica coordinata statica › *Impostazioni del logo* |
| Dove porta il logo (home del sito, indirizzo a scelta, nessun link), title, alt e dimensioni | stessa pagina, scheda **Intestazione** |
| Etichetta "Info per", cambio lingua ITA/ENG | scheda **Intestazione** |
| Lente e finestra di ricerca (motore, parametro, placeholder, link sotto) | scheda **Ricerca** |
| Logo footer, indirizzo e recapiti (editor), riquadro ANVUR | scheda **Piè di pagina** |
| Voci "Info per" | Struttura › Menu › *Grafica coordinata: Info per* (`gc-info-per`) |
| Rubrica / ACCEDI | menu `gc-servizio` |
| Menu principale (Ateneo, Studiare…) | menu `main` |
| Social (header e footer) | menu `gc-social`; l'icona si sceglie dal dominio del link |
| Link del footer | menu `gc-contatti`, `gc-organizzazione`, `gc-riferimenti`, `gc-portale`, `gc-quicklinks` |

Le posizioni sono fisse. Un menu vuoto non viene mostrato. Classe, target e rel di ogni
link si impostano nella sezione *Attributi* della voce di menu (modulo `link_attributes`).

Social riconosciuti: Instagram, Facebook, LinkedIn, YouTube, X/Twitter, Threads, TikTok,
Telegram; un link con "feed" o "rss" prende l'icona RSS, gli altri un'icona generica.

## Variante Lite (`ut_gc_lite`)

Sottotema essenziale: fascia blu con il solo logo (72 px, 60 su mobile) e footer
compatto con logo e indirizzo. Niente Info per, ricerca, social, ANVUR e menu del
portale; il logo porta alla home del sito.

- **Impostazioni** (Aspetto › Impostazioni › UniTS Grafica coordinata Lite):
  logo e dove porta (home del sito, indirizzo a scelta, nessun link), logo e indirizzo del footer.
- **Contenuti liberi**: Struttura › Layout dei blocchi, regioni *Header (a destra del
  logo)* e *Footer (a destra di logo e indirizzo)*. Un menu messo lì viene mostrato
  in riga, con link bianchi.

Per usarla: `drush theme:enable ut_gc_lite` e impostarla come tema predefinito.
Richiede `ut_gc` (tema base); il modulo `ut_gc_setup` non serve.

## Struttura

- `web/themes/custom/ut_gc/` — il tema (base theme: `bootstrap_barrio`)
  - `templates/partials/ut-gc-header.html.twig`, `ut-gc-footer.html.twig` — markup della grafica
  - `ut_gc.theme` — costruisce header/footer da impostazioni e menu, con i cache tag giusti
  - `theme-settings.php` — il form in Aspetto
  - `css/gc/` — CSS di `units_base` (onlineforms, units117) con i percorsi riscritti in relativi
  - `libraries/bootstrap/` — Bootstrap 5.1.3, la stessa versione di www su units112
  - `fonts/` — Titillium Web in locale (prima veniva da portale.units.it → Google Fonts)
- `web/themes/custom/ut_gc_lite/` — variante Lite: template e `css/lite.css` suoi, il resto dal tema base
- `web/modules/custom/ut_gc_setup/` — crea i menu `gc-*` e li popola con i link attuali
  (una volta sola, all'installazione)

## Ambiente locale

```bash
cp example.env .env
```

```bash
docker compose up -d
```

```bash
docker compose exec php vendor/bin/drush site:install standard -y --locale=it --account-name=admin
```

```bash
docker compose exec php sh -c 'vendor/bin/drush en -y ut_gc_setup && vendor/bin/drush theme:enable ut_gc && vendor/bin/drush cset -y system.theme default ut_gc'
```

```bash
docker compose exec php vendor/bin/drush uli
```

Il sito risponde su http://localhost:8088.

## Su un sito esistente

1. `composer require drupal/bootstrap_barrio:^5.5 drupal/link_attributes:^2.1`
2. copiare `web/themes/custom/ut_gc` e `web/modules/custom/ut_gc_setup`
3. `drush en ut_gc_setup` (non tocca un menu che ha già dei link, es. un `main` esistente)
4. `drush theme:enable ut_gc` e impostarlo come predefinito

## Nota sui CSS

Drupal aggrega i CSS e l'URL dell'aggregato non cambia quando cambia il contenuto
di un file. Dopo aver modificato un CSS: cancellare `web/sites/default/files/css`,
`drush cr`, e ricaricare il browser forzando la cache (Cmd+Shift+R).
