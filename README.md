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
- Zmiana statusu (TODO -> IN_PROGRESS -> DONE)
- Lista zadań użytkownika / wszystkich (admin)
- Historia zmian zadania (Event Sourcing)

## Stack technologiczny

- PHP 8.3 / Symfony 7.2
- Doctrine ORM (MySQL 8.0)
- GraphQL (OverblogGraphQLBundle)
- Symfony Messenger (Event Sourcing)
- Docker (PHP-FPM, Nginx, MySQL)
- PHPUnit

## Architektura (DDD)

Kod podzielony na trzy warstwy zgodnie z Domain-Driven Design:

```
src/
├── Domain/              # Agregaty, value objects, eventy, interfejsy repozytoriów
│   ├── User/            # Agregat User (synced z JSONPlaceholder)
│   └── Task/            # Agregat Task z cyklem życia statusów
├── Application/         # Komendy, handlery, factory
│   ├── User/
│   └── Task/
└── Infrastructure/      # Doctrine, API client, kontrolery, GraphQL resolvers
    ├── Persistence/     # Doctrine repositories, migracje
    ├── Api/             # JSONPlaceholder HTTP client
    ├── Controller/      # REST API + Web UI kontrolery
    ├── Messaging/       # Event Store (Doctrine DBAL)
    └── GraphQL/         # Resolvers
```

### Wzorce projektowe

**Factory Pattern** — tworzenie agregatów bez bezpośredniego użycia operatora `new`. Każdy agregat (User, Task) posiada interfejs fabryki i jego implementację:
- `UserFactoryInterface` / `UserFactory`
- `TaskFactoryInterface` / `TaskFactory`

**Strategy Pattern** — walidacja przejść statusów zadań. Każda dozwolona tranzycja ma osobną strategię, resolver iteruje po strategiach i sprawdza czy przejście jest dozwolone:
- `TodoToInProgressStrategy` — TODO -> IN_PROGRESS
- `InProgressToDoneStrategy` — IN_PROGRESS -> DONE
- `StatusTransitionResolver` — sprawdza wszystkie strategie

Bezpośrednie przejście TODO -> DONE nie jest dozwolone.

### Event Sourcing

Każda operacja na zadaniu generuje zdarzenie domenowe zapisywane w tabeli `event_store`:

| Zdarzenie | Kiedy | Payload |
|-----------|-------|---------|
| `TaskCreatedEvent` | Utworzenie zadania | title, description, status, assignedUserId |
| `TaskStatusUpdatedEvent` | Zmiana statusu | previousStatus, newStatus |

Event Store zapisuje: `aggregate_id`, `event_type`, `payload` (JSON), `occurred_at`. Historia zmian jest dostępna przez REST (`GET /api/tasks/{id}/history`) i GraphQL (`taskHistory`).

### Role i uprawnienia

| | Admin | Member |
|---|---|---|
| Widzi zadania | Wszystkie | Tylko swoje |
| Tworzy zadania | Przypisuje dowolnemu userowi | Przypisane do siebie |
| Zmienia status | Tak | Tak (swoich zadań) |
| Panel Users | Tak | Tak |
| Sync users | Tak | Tak |

Uprawnienia są egzekwowane na poziomie Web UI, REST API i GraphQL.

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

- **Przeglądarka:** `http://localhost:8080/` — ekran logowania, po zalogowaniu panel zadań
- **GraphiQL:** `http://localhost:8080/graphiql/` — playground GraphQL
- **Postman:** kolekcja w `docs/TaskManager.postman_collection.json`
- **Login:** username `Bret`, password `Program@2026` (admin)
- **Testy:** `docker compose exec app vendor/bin/phpunit`

## REST API

Wszystkie endpointy (poza `/api/login`) wymagają aktywnej sesji (cookie po logowaniu).

### Auth

| Metoda | Endpoint | Opis |
|--------|----------|------|
| POST | `/api/login` | Logowanie (JSON: `username`, `password`) |
| GET | `/api/me` | Dane zalogowanego użytkownika |

### Users

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/api/users` | Lista użytkowników |
| POST | `/api/users/sync` | Synchronizacja z JSONPlaceholder |

### Tasks

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/api/tasks` | Lista zadań (admin: wszystkie, member: swoje) |
| GET | `/api/tasks?userId={id}` | Lista zadań użytkownika |
| POST | `/api/tasks` | Utwórz zadanie (`title`, `description`, `assignedUserId`) |
| PATCH | `/api/tasks/{id}/status` | Zmień status (`status`: IN_PROGRESS lub DONE) |
| GET | `/api/tasks/{id}/history` | Historia zmian (Event Sourcing) |

## GraphQL API

Endpoint: `POST /graphql/`

### Queries

```graphql
# Zalogowany użytkownik
{ me { id name email username role } }

# Lista użytkowników
{ users { id name email username role } }

# Wszystkie zadania (admin)
{ allTasks { id title description status assignedUserId createdAt updatedAt } }

# Zadania użytkownika
{ tasks(userId: "uuid") { id title status createdAt updatedAt } }

# Historia zmian zadania
{ taskHistory(taskId: "uuid") { id aggregateId eventType payload occurredAt } }
```

### Mutations

```graphql
# Synchronizacja użytkowników
mutation { syncUsers }

# Utworzenie zadania
mutation {
  createTask(title: "Nazwa", description: "Opis", assignedUserId: "uuid") {
    id title status createdAt
  }
}

# Zmiana statusu
mutation {
  updateTaskStatus(taskId: "uuid", newStatus: "IN_PROGRESS") {
    id title status updatedAt
  }
}
```

## Założenia

- Wszyscy użytkownicy synchronizowani z JSONPlaceholder mają hasło: `Program@2026`
- Użytkownik z `externalId=1` (Bret) ma rolę ADMIN, pozostali MEMBER
- Event Sourcing jest uproszczony (append + query, bez event replay)
- Tranzycje statusów: TODO -> IN_PROGRESS -> DONE (bezpośrednie TODO -> DONE niedozwolone)
- Strefa czasowa: Europe/Warsaw
