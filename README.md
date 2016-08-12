# cakephp-monga

A plugin for accessing MongoDB NoSQL data stores in CakePHP 3.x.

## Requirements

* Composer
* CakePHP 3.x
* PHP 5.4+
* MongoDB

## Installation

In your CakePHP root directory: run the following command:

```
composer require LeWestopher/cakephp-monga
```

Then in your config/bootstrap.php in your project root, add the following snippet:

```
Plugin::load('CakeMonga');
```

## Usage

First, we define a new Datasource in our config/app.php file with our namespaced Connection class name:

```
    'Datasources' => [
    
        'default' => [
            // ... Default SQL Datasource
        ],

        'mongo_db' => [
            'className' => 'CakeMonga\Database\MongoConnection',
        ]
    ],
```

Then we can instantiate our MongoDB connection anywhere that we need in the application via the ConnectionManager class:

```
class ExampleController extends Controller
{
    public function index()
    {
        $cake_monga = ConnectionManager::get('mongo_db');
    }
}
```

Then from there we can get our Monga instance by using the `connect()` method on the returned connection:

```
$cake_monga = ConnectionManager::get('monga_db');
$mongodb = $cake_monga->connect(); // An instance of the Monga Connection object
$database_list = $mongodb->listDatabases(); // We can call all of the methods on that Monga object provided by their API
```

## Configuration

cakephp-monga accepts all of the same options in the Datasource configuration that can be passed into the MongoClient() object in PHP.  Documentation for these options is defined [here](http://php.net/manual/en/mongoclient.construct.php).

```
    'Datasources' => [
    
        'default' => [
            // ... Default SQL Datasource
        ],

        'mongo_db' => [
            'className' => 'CakeMonga\Database\MongoConnection',
            'authMechanism' => null,
            'authSource' => null,
            'connect' => true,
            'connectTimeoutMS' => 60000,
            'db' => null,
            'fsync' => null,
            'journal' => null,
            'gssapiServiceName' => 'mongodb',
            'username' => null,
            'password' => null,
            'readPreference' => null,
            'readPreferenceTags' => null,
            'replicaSet' => null,
            'secondaryAcceptableLatencyMS' => 15,
            'socketTimeoutMS' => 30000,
            'ssl' => false,
            'w' => 1,
            'wTimeoutMS' => 10000
        ]
    ],
```

## What is cakephp-monga?

This plugin is a wrapper for the popular [Monga](https://github.com/thephpleague/monga) library provided by [The League of Extraordinary packages.](https://thephpleague.com/)  In it's current form, this plugin is intended to get you quickly set up and running with access to a MongoDB instance so that you can access your data in your application.  This plugin provides all of the same functionality that the Monga library provides in terms of building queries and retrieving your data.

## What is cakephp-monga not?

This plugin is not currently intended as a drop in replacement for the SQL ORM provided by CakePHP core.  While you could theoretically build an entire application using cakephp-monga as the data layer, this plugin does not have the kind of application level integration (Events, Logging, etc) that the current ORM does.  Additionally, there is not abstraction layer for Database level, Collection level, or Entity level objects (EG - Defining methods on a supposed UserCollection.php, or creating a Mongo Entity at User.php), although this is on the roadmap for a future version very soon.
