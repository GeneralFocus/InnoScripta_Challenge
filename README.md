# News Aggregator System

A production-ready, enterprise-grade news aggregation platform that fetches, normalizes and serves articles from multiple news sources through a unified RESTful API.


## Features

-  **Multi-Source Integration** - NewsAPI, The Guardian, NY Times
-  **RESTful API** - Clean, well-documented endpoints
-  **Advanced Filtering** - Search, date range, category, source, author
-  **Full-Text Search** - MySQL optimized indexes
-  **Automated Fetching** - Scheduled updates every 6 hours
-  **Idempotency** - No duplicate articles
-  **Data Normalization** - Unified structure across all providers
-  **SOLID Principles** - Clean, maintainable architecture
-  **Type Safety** - Strict typing throughout


##  Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0+ or PostgreSQL 13+

### Installation

```bash
# 1. Clone the repository
git clone git@github.com:GeneralFocus/InnoScripta_Challenge.git
cd InnoScripta_Challenge

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Set up your API keys in .env
NEWSAPI_KEY=your_newsapi_key
GUARDIAN_API_KEY=your_guardian_key
NYTIMES_API_KEY=your_nytimes_key

# 5. Configure database
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 6. Run migrations
php artisan migrate

# 7. Fetch initial articles
php artisan news:fetch

# 8. Start the scheduler (in a separate terminal)
php artisan schedule:work

# 9. Serve the application
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`


### Core Principles

**SOLID Compliance**
-  **S**ingle Responsibility - Each class has one clear purpose
-  **O**pen/Closed - Extensible without modification
-  **L**iskov Substitution - Providers are interchangeable
-  **I**nterface Segregation - Focused contracts
-  **D**ependency Inversion - Depend on abstractions

**Additional Patterns**
-  **DRY** - Form Requests eliminate validation repetition
-  **KISS** - Simple, readable code
-  **Repository Pattern** - Data access abstraction
-  **Strategy Pattern** - Pluggable providers
-  **DTO Pattern** - Data normalization



#### Get Articles (Paginated with Filters)

```http
GET /api/articles
```

**Query Parameters:**

| Parameter   | Type   | Required | Description                              |
|-------------|--------|----------|------------------------------------------|
| `search`    | string | No       | Full-text search in title/content        |
| `source`    | string | No       | Filter by source slug                    |
| `category`  | string | No       | Filter by category slug                  |
| `author`    | string | No       | Filter by author name                    |
| `date_from` | date   | No       | Start date (YYYY-MM-DD)                  |
| `date_to`   | date   | No       | End date (YYYY-MM-DD)                    |
| `page`      | int    | No       | Page number (default: 1)                 |
| `per_page`  | int    | No       | Items per page (default: 15, max: 100)   |

**Example Request & Response**

This can be found in the **postman_screenshot** folder


### Scheduler Configuration

The news fetcher runs automatically every 6 hours. Configure in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('news:fetch')
        ->everySixHours()
        ->withoutOverlapping();
}
```

### Adding a New Provider

1. Create provider class implementing `NewsProviderInterface`
2. Register in `NewsServiceProvider`
3. Add configuration in `config/news.php`
4. Add API key to `.env`


## 🔧 Artisan Commands

### Fetch News Articles

```bash
php artisan news:fetch
```

Manually triggers article fetching from all configured providers.

### Other Useful Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Run migrations
php artisan migrate
php artisan migrate:fresh

# Run scheduler (development)
php artisan schedule:work

# Run tests
php artisan test
```

## 📊 Database Schema
### Articles Table

| Column        | Type      | Description                    |
|---------------|-----------|--------------------------------|
| id            | bigint    | Primary key                    |
| external_id   | varchar   | Unique provider ID             |
| source_id     | bigint    | Foreign key to sources         |
| category_id   | bigint    | Foreign key to categories      |
| title         | varchar   | Article title                  |
| description   | text      | Article summary                |
| content       | longtext  | Full article content           |
| author        | varchar   | Author name                    |
| url           | varchar   | Original article URL (unique)  |
| image_url     | varchar   | Featured image URL             |
| published_at  | timestamp | Publication date               |

**Indexes:**
- `external_id` (unique)
- `url` (unique)
- `published_at`
- `author`
- Full-text index on `title`, `description`, `content`

## Tech Stack
- **Framework:** Laravel 10+
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0+ / PostgreSQL 13+
- **Cache:** Redis
- **Testing:** PHPUnit
- **HTTP Client:** Guzzle
- **Code Style:** PSR-12

## Code Quality
-  **PSR-12 Compliant** - Standard PHP coding style
-  **Strict Typing** - `declare(strict_types=1)` in all files
-  **Type Hints** - Parameters and return types declared
-  **No Custom Traits** - Pure inheritance-based design
-  **Immutability** - Readonly properties in DTOs
-  **SOLID Principles** - Clean architecture

## Security
- Input validation via Form Requests
- SQL injection prevention (Eloquent ORM)
- XSS protection (Laravel escaping)
- CSRF protection
- Environment-based configuration
- No sensitive data in version control

## Author
**Oyinkansola Olabode**

## Acknowledgments
- [NewsAPI](https://newsapi.org/) - News aggregation service
- [The Guardian](https://open-platform.theguardian.com/) - Open platform API
- [New York Times](https://developer.nytimes.com/) - Developer API
