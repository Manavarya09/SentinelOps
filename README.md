# SentinelOps - Production-Grade SaaS Monitoring Platform

A scalable, enterprise-ready monitoring platform built with Laravel 11 and PHP 8.3.

## Features

- **Multi-tenant SaaS Architecture**: Organizations, users, and role-based access
- **HTTP Monitoring**: Periodic checks with configurable intervals and thresholds
- **Incident Management**: Automated incident creation and lifecycle management
- **Alerting System**: Pluggable notification channels (Email, Webhooks, Slack)
- **Public Status Page**: Real-time status with caching
- **Security First**: Token-based API auth, rate limiting, encrypted secrets
- **Performance Optimized**: Redis queues, Horizon, database indexing
- **Docker Ready**: Complete containerization setup

## Requirements

- PHP 8.3+
- PostgreSQL or SQLite
- Redis
- Composer
- Node.js & NPM (for assets)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/sentinelops.git
   cd sentinelops
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

## Docker Setup

```bash
docker-compose up -d
```

## Usage

### API Authentication

Use Sanctum for API authentication:

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -d '{"email":"admin@acme.com","password":"password"}'

# Use token for authenticated requests
curl -H "Authorization: Bearer {token}" http://localhost:8000/api/monitors
```

### Monitoring Engine

- Monitors are checked via queued jobs
- Run checks manually: `php artisan monitors:check`
- Scheduled every minute via Laravel scheduler

### Horizon Dashboard

Access at `/horizon` for queue monitoring.

## Architecture

### Database Schema

- **Organizations**: Multi-tenant isolation
- **Users**: Role-based access (admin/user)
- **Monitors**: HTTP endpoints to monitor
- **Checks**: Individual check results
- **Incidents**: Failure incidents with timeline
- **Alert Channels**: Notification configurations
- **Notifications**: Sent alert records
- **Audit Logs**: Security audit trail

### Key Components

- **Jobs**: `CheckMonitorJob` for HTTP checks
- **Services**: `IncidentService` for alert management
- **Policies**: Authorization for multi-tenant access
- **Middleware**: Rate limiting and security

## Security

- Encrypted alert channel configurations
- Full audit logging
- Rate limiting on API endpoints
- Soft deletes for data recovery
- UUIDs for public resources

## Scaling

- Horizontal scaling with Redis queues
- Database indexing on critical queries
- Cached status pages
- Background job processing

## Testing

```bash
php artisan test
```

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Configure Redis and PostgreSQL
- [ ] Set up SSL certificates
- [ ] Configure supervisor for queue workers
- [ ] Set up monitoring (Laravel Telescope/Pulse)
- [ ] Enable OPcache and JIT

### Supervisor Configuration

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
directory=/path/to/project
autostart=true
autorestart=true
numprocs=2
```

## Contributing

1. Fork the repository
2. Create feature branch
3. Add tests
4. Submit PR

## License

MIT License

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# SentinelOps
# modelagencywebsite
