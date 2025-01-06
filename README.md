# MyWebSiteDown

Check your website status and send email alert if HTTP Status change on Symfony 7.1.*

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

What things you need to install the software and how to install them

```
PHP >8.2
MySQL >8.0 | MariaDB >11.2
```

### Installing

First :

```
Git clone https://github.com/alexandrecorroy/mywebsitedown.git
```

Update ".env.sample" with your parameters and rename ".env"

Install Dependencies :

```
composer install
```

Create DB :

```
php bin/console doctrine:database:create
```

Install DB :

```
php bin/console doctrine:schema:create
```

## Authors

* **Corroy Alexandre** - *Initial work* - [CORROYAlexandre](https://github.com/alexandrecorroy)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Website

[MyWebSiteDown.com](https://www.mywebsitedown.com/)
