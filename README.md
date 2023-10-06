# Game of Life
Simple PHP-CLI implementation of Game of Life.

## Requirements
* Git
* Docker
* Base Linux Utilities (GNU Bash, GNU Core / Find Utilities, GNU Make, GNU Sed)

## Prepare
* Clone GIT repository
* Check `make help` - list of available commands
* Start container - `make up`
* Install composer dependecies - `make composer-install`

## Play
* Put `world.xml` init file into project directory (check `resources/world.xsd` and `resources/world.dist.xml` for required file structure)
* Start game - `make play`
