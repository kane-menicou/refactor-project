## Introduction

Here is some 5-year-old code, the client has asked us to introduce four-weekly handling for loans. The estimate is 90 mins.

You must:

- Implement the requirements.
- Ensure tests pass.
- Make any optional improvements you feel are important.
- Spin out larger improvements as new technical tasks.
- Leave enough time at the end code review and suggestions.

> Spend 5 minutes attempting to setup.

With requirement rationale and details.

You have 90 minutes to implement a feature on some 5-year-old code, introduce four weekly into calculations. 

Make some partial improvements and future tickets for improvements.


### Setup

`docker compose up -d`

`docker compose exec php touch var/batch/loanApplication.txt`
`docker compose exec database psql -U app -c 'CREATE DATABASE app_test`
`docker compose exec php bin/console d:m:migrate --env=test`


### Useful Commands

`docker compose exec php vendor/bin/phpunit -c phpunit.xml tests`


