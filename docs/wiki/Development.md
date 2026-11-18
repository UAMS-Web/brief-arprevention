<!-- cspell:ignore UAMS arprevention phpstan uams -->

Written against v1.0.1.

The theme is developed in [UAMS-Web/brief-arprevention](https://github.com/UAMS-Web/brief-arprevention). Problems are reported as [issues](https://github.com/UAMS-Web/brief-arprevention/issues), and changes arrive through pull requests to `main`, as [`CONTRIBUTING.md`](https://github.com/UAMS-Web/brief-arprevention/blob/main/CONTRIBUTING.md) describes.

## Local checks

With Composer installed, `composer install` in the theme folder brings in the development tools. Each check has a Composer script:

| Command | What it does |
| --- | --- |
| `composer test` | the Pest unit suite, which runs without WordPress or a database (Brain Monkey stubs the WordPress functions) |
| `composer stan` | PHPStan at the level `phpstan.neon.dist` names, with the WordPress extension |
| `composer test:pint` | Pint formatting check |
| `composer test:refactor` | Rector dry run |
| `composer mutate` | Pest mutation testing over `functions.php`, `lib`, `config` and `page-templates` |

## Continuous integration

Every pull request runs the shared check chain from `UAMS-Web/uams-ci-workflows`, configured in [`.github/workflows/ci.yml`](https://github.com/UAMS-Web/brief-arprevention/blob/main/.github/workflows/ci.yml): the checks above, a PHP 8.2 lint of every shipped file, and a check that the GitHub download holds only what the theme runs.

## What the download holds

[`.gitattributes`](https://github.com/UAMS-Web/brief-arprevention/blob/main/.gitattributes) keeps the GitHub download to the runtime files: `config`, `functions.php`, `images`, `languages`, `lib`, `page-templates`, `README.txt`, `screenshot.png`, `style.css` and `xml`. A new runtime folder needs its own line there.

## Releases

Releases are tagged on `main` and listed under [Releases](https://github.com/UAMS-Web/brief-arprevention/releases). The theme version is the `Version` line in `style.css`.

## This wiki

The pages are kept in [`docs/wiki/`](https://github.com/UAMS-Web/brief-arprevention/tree/main/docs/wiki) and published by the **Publish wiki** workflow ([`.github/workflows/publish-wiki.yml`](https://github.com/UAMS-Web/brief-arprevention/blob/main/.github/workflows/publish-wiki.yml)) when a release is published, or by hand. Each publish replaces the whole wiki with that folder, so change a page through a pull request, never in the wiki's editor. Link between pages with relative links that carry `.md`.
