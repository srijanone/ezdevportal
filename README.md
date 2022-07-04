# EzDevPortal

**EzDevPortal** is a product framework to build custom developer portals.
Whether you're a SaaS company looking to publish APIs for App developers or a
Financial institution with a sprawling OpenBanking API program,
you can use EzDevPortal to engineer a bespoke developer experience.

Logo Image

## Some of EzDevPortal's most unique features:
- API Product Management with access control
- Fully Customizable Developer Experience
- Support and Forum features inbuilt
- Connector based architecture (to API gateways & integration systems)

## Use cases

- **Manage API Products**
  Get a unified platform that handles product catalog,
  program mangement and governance, while ensuring complete program autonomy.

- Provide Best in Class **Developer Experience** with easy API discovery,
  curated and interactive documentation and resources,
  enterprise and community support and more.

- **Institutionalize Dev Rel** & Developer Marketing.
  Deliver personalized experiences & contextual recommendations based on a
  unified 360 degree dev profile, and increase community
  engagement and developer retention.

- **Monetize, Measure & Monitor** your API program.
  Set & track API Product & Program OKRs/KPIs, including revenue,
  developer engagement & usage goals. Also define rate plans and billing
  models and integrate with existing billing systems.

---

## Architecture Key Highlights

- Plugin based API connector architecture which can be easily extended to
  create new connectors : Gateways, Billing, Workflow Extensions etc
- Open Source: Built on top of Drupal CMS and supports full
  Product Management API lifecycle
- Fully customizable UI support via Drupal Layout builder,
  rich media library, custom themes, text editors etc.
- Any infrastructure: SaaS, PaaS, Public Cloud
- Support for separate HTML or Javascript based frontend
  utilizing the Portal Management Rest APIs

---

## Installation

### Prerequisite ###

```
- PHP >= 7.3
- MySQL >= 5.7
- Composer = 1.*
```

EzDevPortal utilizes composer to manage its dependencies. 
So, before using EzDevPortal, make sure you have 
Composer installed on your machine.

## Installation from source ##
```
git clone git@github.com:srijanone/odplite-project.git
cd odplite-project
composer install
```

## Installation via Composer Create-Project ##

- Choose a name for your project, like “MY_PROJECT”
- Use the given command to create the project
- The command will download Drupal core along with necessary modules,
  EzDevPortal profile, and all other dependencies necessary for the project

```bash
composer create-project srijanone/odplite-project MY_PROJECT --no-interaction
```

In case you come across any memory issues, run this command -

```bash
php -d memory_limit=-1 /path/to/composer.phar create-project
srijanone/odplite-project:1.0 MY_PROJECT --no-interaction
```

Installation using drush -

```bash
time php -d memory_limit=-1 ./vendor/bin/drush si odplite  
--db-url='mysql://drupal_user:drupal_password@localhost/drupal_db' 
--site-name='EzDevPortal' --account-name='Srijan' 
--account-pass='Admin@123'  --account-mail='admin@example.com' -y
```

---


## Features
- Productisation of APIs using various features
  - Guides(Pages)
  - Use Cases
  - FAQs
  - Tutorials
  - Media
  - Blogs
  - Issues
- Product & API Categorisation
- NPS
- Social Sharing
- Tagging Feature
- Multiple view supported for Open API Specifications
  - Rapidoc
  - Swagger
  - Redoc
- Async API Support
- GraphQL Support
- SDK download Support
- User registration with email/Github id/Gmail id
- User Dashboards
- SAML/Github/Gmail login feature supported
- Google Captcha for Authentication
- Forum(For Community Support)
- Issues (Organisational Support)
- Notification(Email & Alerts)
- Search
- Custom pages for branding/marketing
- Custom Connectors

---

[Request for Demo](https://srijan.net/contact)

## Credits

- Srijan Team (https://srijan.net)

---
