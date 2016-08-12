# cakephp-monga

A plugin for accessing MongoDB NoSQL data stores in CakePHP 3.x.

## Requirements

* Composer
* CakePHP 3.x
* PHP 5.4+
* MongoDB

## What is cakephp-monga?

This plugin is a wrapper for the popular Monga library provided by The League of Extraordinary packages.  In it's current form, this plugin is intended to get you quickly set up and running with access to a MongoDB instance so that you can access your data in your application.  This plugin provides all of the same functionality that the Monga library provides in terms of building queries and retrieving your data.

## What is cakephp-monga not?

This plugin is not currently intended as a drop in replacement for the SQL ORM provided by CakePHP core.  While you could theoretically build an entire application using cakephp-monga as the data layer, this plugin does not have the kind of application level integration (Events, Logging, etc) that the current ORM does.  Additionally, there is not abstraction layer for Database level, Collection level, or Entity level objects (EG - Defining methods on a supposed UserCollection.php, or creating a Mongo Entity at User.php), although this is on the roadmap for a future version very soon.
