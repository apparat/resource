# Ports

All available ports use the PHP namespace `Apparat\Kernel\Resource`.

## Facades

### `Resource`

Provides factory methods for the supported file types:

* text files (`text`),
* YAML files (`yaml`),
* JSON files (`json`),
* CommonMark files (`commonMark`),
* text files with CommonMark front matter (`frontMark`).

### `Tools`

Utility methods for dealing with resources:

* copy a resource (`copy`),
* move a resource (`move`),
* delete a resource (`delete`),
* create a reader instance (`reader`),
* create a writer instance (`writer`).

## Exceptions

### `InvalidArgumentException`

Exception thrown if a reader or writer stream wrapper is invalid.
