# Security Policy

## Supported scope

The PHPUnit Test Reporter is the WordPress plugin that receives results from the PHPUnit Test Runner and displays them on make.wordpress.org/hosting/test-results. This repository contains that plugin and its GitHub configuration.

This policy covers security-sensitive issues in the code and configuration of this repository. It does not define support for WordPress core, plugins, themes, hosting stacks, server packages, or hosting platforms.

## Reporting vulnerabilities

Because this plugin runs on WordPress.org, a vulnerability in it affects a WordPress.org site. Report it through the [WordPress HackerOne program](https://hackerone.com/wordpress), which covers WordPress.org sites, following the official [WordPress security reporting guidance](https://wordpress.org/about/security/).

Do not report exploitable security vulnerabilities in public GitHub issues or pull requests.

If the vulnerability is in a hosting platform, server package, or other third-party project, report it to that project or vendor through their security reporting process.

## Other issues

If you find a bug or an insecure default in the reporter that is not an exploitable vulnerability, open a public issue in this repository:

https://github.com/WordPress/phpunit-test-reporter/issues

Include the affected behaviour and any safer replacement you are suggesting. Do not include exploit details or private vulnerability information in public issues.
