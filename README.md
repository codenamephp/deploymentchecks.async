# template.php.library

![Lines of code](https://img.shields.io/tokei/lines/github/codenamephp/template.php.library)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/codenamephp/template.php.library)
![GitHub](https://img.shields.io/github/license/codenamephp/template.php.library)

Template repository to quickstart library development.

## Prerequisites
- Docker and Docker Compose

Everything else runs in the docker container.

## Usage

This template uses an installer to setup all the files that need replacing of placeholders but most files
are just kept as they are.

TODOs:
1. Create the new repository on GitHub using this repository as template
2. Add protection rules for master and release branches since they are not copied from the template :(
3. Clone the new repository to local
4. Create the .env file from .env.dist in the project root and update the settings accordingly
5. Start the container using docker compose with the docker-compose.yml in the project root
6. Run the installer by executing `composer run-installer` in the container
7. Add and commit your files and push them to remote
8. Submit the new package to packagist
9. Get to work! ;)
