# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

`freimguork-appacman` is the CMS admin panel for "Freimguork" projects — a Composer package
(`optisistem/freimguork-appacman`) installed as a vendor dependency by consuming apps, never run
standalone (no local entry point of its own; a consuming app's `config/projects.php` mounts it as
an `Appacman` sub-project). It's built on `freimguork-core` and follows the same conventions:
single-action controllers with a shared `build()` dispatch + a `run()` hook, Twig views, PSR-7
responses via `Controller::template()`/`json()`/`redirect()`/etc.

Its controllers live at `src/Controller/` (namespace `Appacman\Controller`) rather than under a
consuming app's own `src/<App>/Controller/`, which is why `Core\Bootstrap::loadRoutes()` and
`Core\View\Response\HTML`/`Core\Utils\Language` special-case `$app === 'Appacman'` to resolve paths
via the `APPACMAN_DIR` constant (`freimguork-core/src/Utils/Config.php`) instead of the usual
per-app convention.

**Routing and DI are fully migrated** (as of the pass that added `#[Route]` attributes and required
constructor injection) — every controller has a class-level `#[Route(...)]` attribute (there is no
`config/*/routing.php` anymore), and the whole `Appacman\Controller` tree's constructors resolve to
exactly `(Config $config, CacheManager $modelCache, Session $session)`, autowired by
`Core\Container\Container`. `tests/Controller/ConstructorContractTest.php` enforces this via
reflection across every file in `src/Controller/`, the same way `freimguork-core`'s own DI tests do
— if you add a controller, its constructor (or an ancestor's) must match that exact shape or the
container won't build it.

This state is tagged `v2.0`, a checkpoint reference matching `freimguork-core`'s own `v2.0` (not a
pinning point — see README's "Versioning" section for the full convention).

## Commands

Same Docker-only setup as `freimguork-core` — no local PHP/Composer toolchain on the host:

```bash
docker exec php sh -c "cd /var/www/html/freimguork-appacman && composer install"
docker exec php sh -c "cd /var/www/html/freimguork-appacman && composer test"
docker exec php sh -c "cd /var/www/html/freimguork-appacman && composer phpstan"
docker exec php sh -c "cd /var/www/html/freimguork-appacman && php -l src/Controller/Home.php"
```

If a package requirement changes, use `composer update <package>` (not a bare `composer install`),
same reasoning as core: this repo pulls `optisistem/freimguork-core` and `optisistem/php-ref` as VCS
dependencies from Bitbucket, and a full re-lock is slower and noisier than it needs to be.

**Static analysis**: PHPStan level 5, `phpstan.neon`, scanning `src/` only. `phpstan-baseline.neon`
snapshots 44 pre-existing issues (mostly `Model/Form/*` type mismatches and a couple of unused
methods in `Model/PushCronJob.php`) that predate the tool being added here — same policy as core's
own baseline: don't grow it for new code, it exists to unblock adopting PHPStan on old code, not as
a general suppression list.

## Dependency injection: Controller layer fixed, Model layer deliberately not

All 4 `::getInstance()` call sites in the **Controller** layer were removed, because controllers
are always built through the container: `AppacmanController` now takes `Session $session` as a
required constructor param (alongside `Config`/`CacheManager` from `Core\Controller\Controller`),
stored as `protected Session $session`. `Bootstrap` already registers `Session::class` as a
container singleton, so this needed no core-side change. `BaseContentForm` uses the already-injected
`$this->config`/`$this->session` instead of calling `Config::getInstance()`/`Session::getInstance()`
itself.

One Controller-layer `::getInstance()` call is **deliberately left in place**:
`AppacmanController::build()`'s `User::getInstance()`. `User` is appacman's own singleton (current
logged-in user, resolved from `Session`), not something `Core\Bootstrap` knows about or registers —
there's no appacman-specific hook in the container's composition root to inject it through, so
making it a constructor param isn't possible without either changing core (out of scope, and not
core's concern) or losing the shared-instance semantics.

The remaining 13 `::getInstance()` sites are all in the **Model** layer (`Model/Content.php`,
`Model/Menu.php`, `Model/Form/*`, `Model/User.php`, `Model/LoggedOut/UserForm.php`) and are
**intentionally untouched** — every one of them is reached via a bare `new X(...)` deep inside a
method body, never through the container. This mirrors `Core\Model\Model`'s own deliberate
optional-injection design (see `freimguork-core/CLAUDE.md`'s Dependency Injection section): forcing
these to take required constructor params would mean rewriting every call site just to keep
*appacman itself* working, without the container ever being involved. This isn't debt to clean up
later — it's the same pattern core chose for itself, for the same reason.

## The `ExtraUser`/`ExtraMenu` extension point is real, not dead code

`Controller/LoggedOut/Forgot.php`, `Controller/LoggedOut/SignIn.php`, `Model/Menu.php`, and
`Model/Form/SelectDeepLink.php` all do a stringly-typed `class_exists('Appacman\Model\ExtraUser')`
(or `ExtraMenu`) check to let a consuming app extend appacman's behavior without a formal
interface/contract. This is **actively used in production** —
`fedesk-local/src/Appacman/Model/ExtraUser.php` exists and is discovered exactly this way. Changing
this mechanism (e.g. to an interface-based extension point) would need to also update every
consuming app that defines one of these classes, not just appacman — treat it as a cross-repo
migration if it's ever tackled, not a same-PR appacman cleanup.
