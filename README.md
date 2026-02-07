# TaskManager

Aplikacja do zarządzania zadaniami z GraphQL/REST API, oparta na Domain-Driven Design (DDD) z Event Sourcing.

Zadanie rekrutacyjne dla Programa Software House.

## Opis zadania

Aplikacja pozwala użytkownikom tworzyć, przypisywać i śledzić status zadań. Użytkownicy są pobierani z zewnętrznego API (JSONPlaceholder). Każda operacja na zadaniu generuje zdarzenie zapisywane w Event Store.

### Funkcjonalności

**User:**
- Synchronizacja użytkowników z JSONPlaceholder API
- Logowanie (form login + JSON API)
- Pobieranie danych zalogowanego użytkownika

**Task:**
- Tworzenie zadania (nazwa, opis, status, przypisany użytkownik)
- Zmiana statusu (TODO → IN_PROGRESS → DONE)
- Lista zadań użytkownika / wszystkich (admin)
- Historia zmian zadania (Event Sourcing)

### Wzorce projektowe
- **Factory Pattern** — tworzenie agregatów User i Task
- **Strategy Pattern** — walidacja przejść statusów zadań

## Stack technologiczny

- PHP 8.3 / Symfony 7.2
- Doctrine ORM (MySQL 8.0)
- GraphQL (OverblogGraphQLBundle)
- Symfony Messenger (Event Sourcing)
- Docker (PHP-FPM, Nginx, MySQL)
- PHPUnit

## Założenia

- Wszyscy użytkownicy synchronizowani z JSONPlaceholder mają hasło: `Program@2026`
- Użytkownik z `externalId=1` (Bret) ma rolę ADMIN, pozostali MEMBER
- Event Sourcing jest uproszczony (append + query, bez event replay)
- GraphQL to główne API, REST jako alternatywny interfejs

## Uruchomienie

```bash
# Uruchom kontenery
docker compose up -d

# Uruchom migracje
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# Zsynchronizuj użytkowników z JSONPlaceholder
docker compose exec app php bin/console app:sync-users
```

Aplikacja dostępna pod `http://localhost:8080/`.

## Testowanie

- **Przeglądarka:** `http://localhost:8080/` — ekran logowania, po zalogowaniu panel z listą użytkowników
- **GraphiQL:** `http://localhost:8080/graphiql/` — playground GraphQL
- **Postman:** kolekcja w `docs/TaskManager.postman_collection.json`
- **Login:** username `Bret`, password `Program@2026`

## Architektura (DDD)

```
src/
├── Domain/              # Agregaty, value objects, eventy, interfejsy repozytoriów
│   ├── User/
│   └── Task/
├── Application/         # Komendy, handlery, factory
│   ├── User/
│   └── Task/
└── Infrastructure/      # Doctrine, API client, kontrolery, GraphQL resolvers
    ├── Persistence/
    ├── Api/
    ├── Controller/
    └── GraphQL/
```
