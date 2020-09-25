== Contributing Guidelines ==

Thank you for considering contributing to the [WordPress PHPUnit Test Reporter](https://make.wordpress.org/hosting/test-results)! If you're unsure of anything, know that you're 💯 welcome to [submit an issue](https://github.com/phpunit-test-reporter/issues) or [pull request](https://github.com/phpunit-test-reporter/pulls) on any topic. The worst that can happen is that you'll be politely directed to the best location to ask your question or to change something in your pull request. We appreciate any sort of contribution and don't want a wall of rules to get in the way of that.

As with all WordPress projects, we want to ensure a welcoming environment for everyone. With that in mind, all contributors are expected to follow our [Code of Conduct](/CODE_OF_CONDUCT.md).

This project is licensed [GPLv3](/LICENSE), and all contributions to this project will be released under the GPLv3 license. You maintain copyright over any contribution you make, and by submitting a pull request, you are agreeing to release that contribution under the GPLv3 license.

This document covers the technical details around setup, and submitting your contribution.


## Docker Environment
There’s a Docker environment with several tools built in for testing.
To configure it, run `make` and it will automatically run `docker-compose`.
After that, to view the test environment, visit http://localhost:8080.

Usage:
- `make` or `make start`:  Builds a Docker environment for testing.
- `make stop`: Stops Docker test environment.
- `make shell`: SSH to Docker test environment.
- `make test`: Runs `phpunit` and `phpcs` in the Docker test environment.


## How to build `README.md`
There is also a [Grunt](https://gruntjs.com/) command for updating the [`README.md`](/README.md) file for Github
after updating [`readme.txt`](/readme.txt).

Usage:
- `npm install`: Installs necessary dependencies.
- `grunt readme`: Generates `README.md` with
  [`grunt-wp-readme-to-markdown`](https://github.com/stephenharris/wp-readme-to-markdown).
  
## Coding Standards
This project follows the [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards), and automatic checking is built into the automated tests.

This means you can check locally with `make test` before submitting a PR, or on your fork/branch of the GitHub repo with TravisCI.

