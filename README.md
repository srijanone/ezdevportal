# Contents Of This File

- About EzDevPortal
- Features
- Architectural Highlights
- Drupal 9 Installation
- Credits

# About EzDevPortal

[EzDevPortal](https://ezdevportal.srijan.net) is a Drupal install profile
for setting up fully customizable developer portal. Whether you're a SaaS
company looking to publish APIs for App developers or a Financial institution
with a sprawling OpenBanking API program you can use EzDevPortal to engineer
a bespoke developer experience.

# Features

## Key Features

- **Product & API Categorization:**

  Organize APIs and products that have similar attributes into categories
  and catalogs for quick navigation and discoverability.

- **Self-service Capabilities:**

  Comes with custom workflows that streamlines admin processes like content
  & access control and simplifies the onboarding process for both upstream
  and downstream user journeys.

- **Analytics:**

  Integrate with Google Analytics to track and view reports like session
  information, top pages, keywords etc. In addition, app analytics like
  API usage can be tracked*.

- **Custom APIM Connectors:**

  Through preconfigured connectors, integrate the developer portal with
  your APIMs, to manage access requests for your developers and much more.

- **API Documentation:**

  Supports generating API documentation from multiple languages including
  OAS (swagger), Rapidoc, and code snippets.

- **Enhanced Site Search:**

  Advanced search capabilities to facilitate browsing and searching in API
  catalogs.

- **SDK download Support:**

  Auto generate client-side SDKs to create apps that can consume subscribed
  APIs.

- **Content Management:**

  Build API ecosystem by creating and managing pages, blogs, media, use cases,
  forums and FAQs.

## Detailed Features List
- Productization of APIs using various features
  - Guides(Pages)
  - Use Cases
  - FAQs
  - Tutorials
  - Media
  - Blogs
- Product categorization
- Social Sharing
- Multiple view supported for Open API Specifications
  - Rapidoc
  - Swagger
  - Redoc
- Async API Support
- GraphQL Support
- SDK download support
- User registration/login with email/Github/Gmail
- User Dashboard
- Forum(For Community Support)
- Issues (Organisational Support)
- Notification(Email & Alerts)
- Search
- Siteadmin functionality for content management
- Custom pages creation for branding/marketing
- Optional demo content for quickstart
- Pluggable Connectors to various API gateways
  like Apigee, Kong, AWS Api Gateway, Azure API Gateway

# Architectural Highlights

- Open Source: Built on top of Drupal CMS and supports full
  Product Management API lifecycle.
- Fully customizable UI support via Drupal Layout builder,
  rich media library, custom themes, text editors etc.
- Plugin based API connector architecture which can be easily extended to
  create new connectors.
- Hosting on any infrastructure: PaaS, Public Cloud
- Support for separate HTML or Javascript based frontend
  utilizing the Portal Management Rest APIs

# Drupal 9 Installation

### Prerequisites

```
- PHP >= 8.0
- MariaDB 10.3.7+
- MySQL 5.7.8+
- Composer = 2.*
```

EzDevPortal utilizes composer to manage its dependencies.
So, before using EzDevPortal, make sure you have
composer installed on your machine.

### Installation from source
```
git clone git@github.com:srijanone/ezdevportal-project.git
cd ezdevportal-project
composer install
```

### Installation via composer

- Choose a name for your project, like “MY_PROJECT”
- Use the given command to create the project
- The command will download Drupal core along with necessary modules,
  EzDevPortal profile, and all other dependencies necessary for the project

```bash
composer create-project srijanone/ezdevportal-project
MY_PROJECT --no-interaction
```

In case you come across any memory issues, run this command -

```bash
php -d memory_limit=-1 /path/to/composer.phar create-project
srijanone/ezdevportal-project MY_PROJECT --no-interaction
```

**You can install the site either through drush or using GUI method.**

### Drush method

Navigate to the project root through terminal and execute the following command:

```bash
./vendor/bin/drush si ezdevportal
--db-url='mysql://{mysql_user}:{mysql_password}@{mysql_host}/{db_name}'
--site-name='EzDevPortal' --account-name='Srijan'
--account-pass='Admin@123'  --account-mail='admin@example.com' -y
```
### GUI Method

Setup a local environment using either docker(ddev, lando etc.) or LAMP stack.

Run the normal drupal GUI installation process.
You can choose to install the demo module during installion or you can install
it later from extend page or using drush.

Clear the drupal cache after installing the demo module.

# Credits

- Srijan (https://srijan.net)
