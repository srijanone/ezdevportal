## Contents Of This File

  * Introduction
  * Requirements
  * Installation
  * Configuration
  * Maintainers

## Introduction

This module provides a field formatter for File field which renders the
referenced file using Async API UI, RapiDoc UI and GraphQL Voyager UI.

### Key Features:

  * It provides provides a field formatter for File field which renders the
  referenced file using Async API UI, RapiDoc UI and GraphQL Voyager UI.

## Requirements

The required dependencies have been managed in installation of the profile.

## Installation

Like any other drupal module, Go to extend page and enable module.

## Configuration

File fields:

    1. Navigate to Structure > Content types > TYPE > Manage fields where
       TYPE is the content type to which you want to add the new field, such as
       a Basic page.
    2. Click on the "Add field" button to add a new field.
    3. Set the field type to "File" and enter a label name.
    4. Click "Save and continue".
    5. On the "Edit" tab, in the "Allowed file extensions" field enter the
       following: json, yml/yaml.
    6. Click "Save settings".
    7. Click on the "Manage display" tab.
    8. Select "RapiDoc UI", "Voyager UI" or "Async API UI" in the Format
       drop-down for the new field and
       optionally configure formatter settings.
    9. Click "Save".
    10. Add a new TYPE type content and upload a valid Swagger json,
     Voyager schema json and Async api json/yaml file.
    11. A sample file available in ./assets folder of the ezdevportal_asyncapi
     and ezdevportal_voyager modules.

## Maintainers

Srijan Technologies Pvt. Ltd. (https://www.drupal.org/srijan-technologies)
