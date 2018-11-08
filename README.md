OpenKudo
========

An coworker recognition system. Thank Your coworkers for the great work they deliver!

## Installation and running

```
docker-compose run php composer install
docker-compose run php bin/console doc:mig:mig
docker-compose run php bin/console event-store:event-stream:create
docker-compose up -d
```

Enjoy!

## Domain
### Aggregates & Events

* ThankYou
  * ThankYouWasPosted
* Person
  * PersonWasRegistered

### Commands
* PostThankYou
* RegisterUser

### Projections
* ThankYouProjector
* PersonProjector

## Contribution guide

(To be written)

* Coding standards (PSR-1, PSR-2)
* Unit Tests

