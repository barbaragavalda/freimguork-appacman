# freimguork-appacman

CMS admin panel module for Freimguork projects. A Composer package
(`optisistem/freimguork-appacman`) built on `freimguork-core`, installed as a vendor dependency and
mounted by a consuming app's `config/projects.php` as an `Appacman` sub-project — it has no entry
point of its own and is never run standalone.

Sibling packages in this family: `freimguork-core` (shared framework), `freimguork-webservice`,
`freimguork-jwt`.

## Requirements

- A consuming application already built on `freimguork-core`, with `optisistem/freimguork-core`
  installed
- `almasaeed2010/adminlte` (~2.4) for the admin UI

## Installation

```bash
composer require optisistem/freimguork-appacman
```

This is a private Bitbucket package. Composer needs to authenticate to `bitbucket.org` to fetch
it — Bitbucket app passwords are deprecated, so use an Atlassian API token with the
`read:repository:bitbucket` scope instead:

```bash
composer config --global http-basic.bitbucket.org "your-atlassian-account-email@example.com" "your-api-token"
```

## Architecture highlights

- **Routing and DI are fully migrated.** Every controller has a class-level `#[Route(...)]`
  attribute (no `config/*/routing.php`), and the whole `Appacman\Controller` tree's constructors
  resolve to exactly `(Config $config, CacheManager $modelCache, Session $session)`, autowired by
  `Core\Container\Container`. `tests/Controller/ConstructorContractTest.php` enforces this shape
  via reflection across every controller.
- **Form rendering** uses Twig templates rather than hand-built HTML strings.
- **Extension points**: a consuming app can define `Appacman\Model\ExtraUser` / `ExtraMenu` classes
  to extend appacman's user/menu behavior — discovered via `class_exists()`, not a formal interface.
  This is actively used in production (see `CLAUDE.md`).
- **Services layer**: business logic is being extracted out of controllers/models into `Service\*`
  classes, guarded by a constructor-contract test.

## Secrets management

This package has no credentials of its own — it always runs inside a consuming app, which owns
`config/dev/`/`config/prod/`. See `freimguork-core`'s README for the `.dist`-template + gitignore
convention those per-environment credential files should follow.

## Testing

PHP and Composer only exist inside this project's Docker container (see the top-level
`VM/docker-compose.yml`, service `php`). Run all commands through it:

```bash
docker exec php sh -c "cd /var/www/html/freimguork-appacman && composer install"
docker exec php sh -c "cd /var/www/html/freimguork-appacman && composer test"
docker exec php sh -c "cd /var/www/html/freimguork-appacman && composer phpstan"
```

## Versioning

Follows the same `v1.0` / `dev-master` split as `freimguork-core`: `v1.0` is the last
pre-modernization snapshot, still used by consuming apps that haven't migrated their controller
constructors yet. This package itself already tracks core's `dev-master`, since its routing and DI
are fully migrated.

## Status

PHPStan (level 5) is configured, with a baseline covering pre-existing issues (mostly
`Model/Form/*` type mismatches) that predate the tool being introduced. Broader test coverage is
ongoing alongside the services-layer extraction.

## More documentation

`CLAUDE.md` has the full architecture write-up: the DI migration details, the `ExtraUser`/`ExtraMenu`
extension mechanism, and testing conventions.
