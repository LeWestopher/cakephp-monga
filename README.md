# cakephp-monga

[![Framework](https://img.shields.io/badge/Framework-CakePHP%203.x-orange.svg)]()
[![license](https://img.shields.io/github/license/LeWestopher/cakephp-monga.svg?maxAge=2592000)]()
[![Github All Releases](https://img.shields.io/github/downloads/LeWestopher/cakephp-monga/total.svg?maxAge=2592000)]()

A plugin for accessing MongoDB NoSQL data stores in CakePHP 3.x.

### Requirements

* Composer
* CakePHP 3.x
* PHP 5.4+
* MongoDB

### Installation

In your CakePHP root directory: run the following command:

```
composer require lewestopher/cakephp-monga
```

Then in your config/bootstrap.php in your project root, add the following snippet:

```php
// In project_root/config/bootstrap.php:

Plugin::load('CakeMonga');
```

or you can use the following shell command to enable to plugin in your bootstrap.php automatically:

```
bin/cake plugin load CakeMonga
```

### Usage

First, we define a new Datasource in our config/app.php file with our namespaced Connection class name:

```php
// In project_root/config/app.php:

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

```php
class ExampleController extends Controller
{
    public function index()
    {
        $cake_monga = ConnectionManager::get('mongo_db');
    }
}
```

Then from there we can get our Monga instance by using the `connect()` method on the returned connection:

```php
$cake_monga = ConnectionManager::get('mongo_db');
$mongodb = $cake_monga->connect(); // An instance of the Monga Connection object
$database_list = $mongodb->listDatabases(); // We can call all of the methods on that Monga object provided by their API
```

Note that the $mongodb object instantiated above with the `connect()` method is the same object returned by Monga::connection() in the [Monga](https://github.com/thephpleague/monga) API:

```php
$cake_monga = ConnectionManager::get('mongo_db');
$mongodb = $cake_monga->connect();

// Alternatively:

$mongodb = Monga::connection($dns, $config_opts);
```

This information should help you make the bridge between instantiating the Datasource using CakePHP and utilizing the Monga API for data retrieval and saving.

### Configuration

cakephp-monga accepts all of the same options in the Datasource configuration that can be passed into the MongoClient() object in PHP.  Documentation for these options is defined [here](http://php.net/manual/en/mongoclient.construct.php).

```php
// In project_root/config/app.php: 

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
        'dns' => 'mongodb://localhost:27017',
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

### Connecting to a custom DNS using this library

By default, this library connects to the `mongodb://localhost:27017` DNS string.  You can specify a custom DNS to connect on by setting a 'dns' key on the connection's Datasource hash in the config/app.php file:

```php
// In project_root/config/app.php:

'Datasources' => [

    'mongo_db' => [
        'className' => 'CakeMonga\Database\MongoConnection',
        'dns' => 'mongodb://your.remote.host:27017'
    ]
],
```

### API and Accessing your MongoDB Instance

This plugin is a wrapper of the Mongo plugin by the League of Extraordinary Packages.  To find out how to query, save, and update data within your Mongo collections, check out the [Monga documentation](https://github.com/thephpleague/monga).

### Defining a custom Collection class

As of version 0.2.0, CakeMonga supports the usage of custom Collection classes.  These
custom classes are located with the `src/Model/MongoCollection` folder an extend the `CakeMonga\MongoCollection\BaseCollection` class.  Now you can
use these classes to encapsulate data layer logic into the appropriate class locations.  These methods are direct abstractions of their Monga counterparts.
 You can view the docs for these methods on the [Monga API Docs](https://github.com/thephpleague/monga).  The `BaseCollection` class provides the following methods for data access:

```php
class BaseCollection {
    public function getCollection();
    public function find($query = [], $fields = [], $findOne = false);
    public function findOne($query = [], $fields = []);
    public function drop();
    public function listIndexes();
    public function save($document, $options = []);
    public function update($values = [], $query = null, $options = []);
    public function insert(array $data, $options = []);
    public function remove($criteria, $options = []);
    public function truncate();
    public function aggregate($aggregation = []);
    public function distinct($key, $query = []);
    public function count($query = []);
}
````

Note that the MongoDB collection that is utilized by custom Collection classes is the tableized name of the Collection class itself.  For example, 
`UsersCollection.php` would map to the `users` collection inside of your MongoDB instance.  

### Retrieving a custom Collection model using CollectionRegistry

As of 0.2.0, custom Collection models extended from `BaseCollection` can be retrieved using the `CollectionRegistry` singleton
with the same syntax that `TableRegistry` employs:

```php
// Define a custom User collection at src/Model/MongoCollection/UserCollection.php. 
class UsersCollection extends BaseCollection
{
    public function getUsersNamedJohn()
    {
        return $this->find(['name' => 'John']);
    }
}

// We can retrieve this UsersCollection by using the static ::get() method on CollectionRegistry
$users_collection = CollectionRegistry::get('Users');
```

By default, the CollectionRegistry utilizes the default connection defined as 'mongo_db' in your app.php file.  Want to use
a different Mongo datasource?  No problem, just pass in a datasource string to the 'connection' attribute of the config array for CollectionRegistry::get():

```php
$users_collection = CollectionRegistry::get('Users', [
    'connection' => 'secondary_mongo_datasource'
]
```

This would construct the UsersCollection class with a connection to the other datasource.

### What is cakephp-monga?

This plugin is a wrapper for the popular [Monga](https://github.com/thephpleague/monga) library provided by [The League of Extraordinary packages.](https://thephpleague.com/)  In it's current form, this plugin is intended to get you quickly set up and running with access to a MongoDB instance so that you can access your data in your application.  This plugin provides all of the same functionality that the Monga library provides in terms of building queries and retrieving your data.

### What is cakephp-monga not?

This plugin is not currently intended as a drop in replacement for the SQL ORM provided by CakePHP core.  While you could theoretically build an entire application using cakephp-monga as the data layer, this plugin does not have the kind of application level integration (Events, Logging, etc) that the current ORM does.  Additionally, there is not abstraction layer for Database level, Collection level, or Entity level objects (EG - Defining methods on a supposed UserCollection.php, or creating a Mongo Entity at User.php), although this is on the roadmap for a future version very soon.

Additionally, it's important to recognize that while certain relational features can be emulated within a MongoDB dataset, Mongo is still not an ACID compliant database.  In the future, Collection level classes will be built for object abstraction that will implement CakePHP's Repository interface, but it should be noted that full ORM features will not be supported as Mongo is not a true object relational database.

### Roadmap

Here are some of the features that I plan on integrating into this project very soon:

- [X] Basic Connection object support for retrieving an instance of the Monga class for simple data retrieval. **Added in 0.1.0**
- [ ] Collection and Entity level abstraction layers (EG - UserCollection.php and User.php for Mongo)
- [ ] SSL Support via the stream context on the third argument of the MongoClient constructor
- [ ] Query logging via the stream context on the third argument of the MongoClient constructor
- [ ] A CollectionRegistry class for retrieving Mongo collections with connection params already passed in.

### Support

For bugs and feature requests, please use the [issues](https://github.com/LeWestopher/cakephp-monga/issues) section of this repository.

### Contributing

To contribute to this plugin please follow a few basic rules.

* Contributions must follow the [CakePHP coding standard](http://book.cakephp.org/3.0/en/contributing/cakephp-coding-conventions.html).
* [Unit tests](http://book.cakephp.org/3.0/en/development/testing.html) are required.

### Change Log

Yes, we have one of [those](https://github.com/LeWestopher/cakephp-monga/blob/master/CHANGELOG.md).

### Creators

[Wes King](http://www.github.com/lewestopher)

[Frank de Jonge](https://github.com/frankdejonge) - Creator of the Monga Dependency

[Monga Contributors](https://github.com/thephpleague/monga/contributors)

### License

Copyright 2016, Wes King

Licensed under The MIT License Redistributions of files must retain the above copyright notice.
