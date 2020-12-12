LanguageServerPsalm
=====================

[![Build Status](https://travis-ci.org/phpactor/language-server-psalm-extension.svg?branch=master)](https://travis-ci.org/phpactor/language-server-psalm-extension)

Psalm Language Server and [Phpactor](https://github.com/phpactor/phpactor) Extension.

Provides [Language
Server](https://microsoft.github.io/language-server-protocol/specification) diagnostics from [Psalm](https://psalm.org/).

Usage
-----

### Phpactor Extension

If you are using the [Phpactor Language Server](https://phpactor.readthedocs.io/en/master/usage/language-server.html)

```
$ phpactor extension:install "phpactor/language-server-psalm-extension"
```

### Standalone

Manually install it:

```
$ git clone git@github.com:phpactor/language-server-psalm-extension some/path
$ cd language-server-psalm-extension
$ composer install
```

The process of enabling the server with your client will vary. If you are
using VIM and [CoC](https://github.com/neoclide/coc.nvim) it will look
something like (`:CocConfig`):

```
{
    "languageserver": {
        "psalm": {
            "enable": true,
            "command": "/some/path/bin/psalm-ls",
            "args": ["language-server"],
            "filetypes": ["php"]
        }
    }
}
```

PHPStan Configuration
---------------------

The extension depends on having a `psalm.neon` in your project root which
defines your projects `level` and analysis `paths` e.g.:

```
# psalm.neon
parameters:
    level: 7
    paths: [ src ]
```

Configuration
-------------

- `language_server_psalm.bin`: Relative or absolute path to Psalm. Default
  is `'%project_root%/vendor/bin/psalm'`
