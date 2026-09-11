# Inventory Management

Gemeinsames Repository für die Inventarverwaltung. Die Anwendung besteht aus
einer Symfony-API und einem Vue-Frontend.

## Struktur

```text
apps/
├── backend/   Symfony-API, Doctrine und Migrationen
└── frontend/  Vue, TypeScript und Vite
```

Die gemeinsame DDEV-Konfiguration liegt im Repository-Root. Das Backend ist
unter `https://inventory-management.ddev.site` erreichbar, der Vite-Server
unter `https://vite.inventory-management.ddev.site`.

## Lokal starten

```bash
ddev start
ddev composer install
ddev exec npm --prefix ../frontend install
ddev vite
```

Danach ist das Frontend normalerweise unter folgender Adresse erreichbar:

```text
https://vite.inventory-management.ddev.site
```

Falls DDEV einen eigenen Router-Port verwendet, wird dieser an die Adresse
angehängt, beispielsweise `:33001`. Die für die lokale Umgebung gültigen URLs
zeigt DDEV mit folgendem Befehl an:

```bash
ddev describe
```

Der Vite-Prozess muss während der Frontend-Entwicklung weiterlaufen. Nach einem
Neustart von DDEV wird er bei Bedarf erneut mit `ddev vite` gestartet.

Alternativ kann das Frontend vom Host gestartet werden:

```bash
cd apps/frontend
npm install
VITE_API_PROXY_TARGET=https://inventory-management.ddev.site npm run dev
```

## Prüfungen und Build

```bash
ddev exec bin/console lint:yaml config
ddev exec bin/console doctrine:schema:validate
ddev exec npm --prefix ../frontend run type-check
ddev exec npm --prefix ../frontend run lint
ddev exec npm --prefix ../frontend run build
```

Der Frontend-Build wird nach `apps/frontend/dist` geschrieben. Frontend und
Backend bleiben damit getrennt auslieferbar.

## Datenbank ändern

Neue oder geänderte Entities liegen unter `apps/backend/src/Entity`.

```bash
ddev exec bin/console make:entity
ddev exec bin/console make:migration
ddev exec bin/console doctrine:migrations:migrate
```

Controller für neue API-Endpunkte liegen unter
`apps/backend/src/Controller`.
