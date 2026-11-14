# Inventory Management

Monorepo mit Symfony-API (`apps/backend`) und Vue-Frontend (`apps/frontend`).
Setup und URLs stehen in der `README.md`.

## Befehle

Alles läuft in DDEV. Composer, PHP und npm nie direkt auf dem Host ausführen.
Das Arbeitsverzeichnis im Container ist `apps/backend`.

```bash
ddev composer <cmd>                                   # Backend-Abhängigkeiten
ddev exec bin/console <cmd>                           # Symfony-Konsole
ddev exec bin/console doctrine:migrations:diff        # Migration nach Entity-Änderung
ddev exec bin/console doctrine:migrations:migrate
ddev exec npm --prefix ../frontend <cmd>              # Frontend (install, run lint, ...)
ddev vite                                             # Vite-Dev-Server
ddev exec npm --prefix ../frontend run build          # Type-Check + Build
```

Nach Frontend-Änderungen `run build` (enthält `vue-tsc`) und `run lint` ausführen.
Automatisierte Tests gibt es noch nicht.

## Frontend

- Vue 3 mit `<script setup lang="ts">` und Composition API.
- **UI mit PrimeVue 4 (Theme Aura) bauen.** Gibt es für ein Element eine
  PrimeVue-Komponente (Button, InputText, Select, DataTable, Dialog, Drawer,
  DatePicker, FileUpload ...), wird sie verwendet. Keine nativen
  `<input>`/`<button>`/`<table>` und keine eigenen Nachbauten.
- Rückmeldungen über `useToast()`, Löschbestätigungen über `useConfirm()`.
- Tailwind nur für Layout und Abstände. Aussehen von PrimeVue-Komponenten über
  Props oder Theme-Tokens anpassen, `:deep(.p-…)`-Overrides nur als Ausnahme.
- Standard-Utility-Klassen von Tailwind verwenden (`p-4`, `gap-2`, `w-full`).
  Keine Arbitrary Values (`w-[137px]`, `bg-[#1a2b3c]`) und kein `@apply`,
  außer es gibt wirklich keine passende Standardklasse.
- UI-Texte über vue-i18n (`src/locales/de.ts`), nicht hart im Template.
- API-Zugriffe und Ladezustand liegen in `src/stores/*Store.ts` (einfache
  `ref`-Module, kein Pinia). Typen liegen in `src/types/`.

## Backend

- Symfony 7.3, Doctrine ORM 3, PHP 8.3, MariaDB.
- Controller in `src/Controller`, Entities in `src/Entity`, Logik in `src/Service`.
- Bestehende Migrationen nie ändern, sondern immer eine neue erzeugen.

## Konventionen

- Commits: `[FEATURE|TASK|FIX|REFACTOR] AUS-1234 Kurze Beschreibung` (Englisch).
- Branches: `AUS-1234-kurzbeschreibung`.
- Code-Kommentare und Doku auf Deutsch.
- `vendor/`, `node_modules/`, `dist/` und `var/` nicht anfassen.
