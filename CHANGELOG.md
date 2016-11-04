# Change Log

### Version 0.1.0

This is the initial release version of this plugin.  The plugin as it is currently provides access to a MongoDB Datasource configured within the config/app.php file.

### Version 0.2.0

* Collection level object support added.  Developers can now define models inside of `src\Model\MongoCollection` that extend
the `BaseCollection` class so that business logic can be abstracted and encapsulated.  This class wraps all of the main data access methods
provided by the Monga API's collection object.
* A `CollectionRegistry` singleton has been added for constructing and retrieving custom Collections that extends the `BaseCollection` class.

### Version 0.3.0

* CakeMonga now has query logging support.  To enable query logging, define a custom logger class that extends `CakeMonga\Logger\MongoLogger` to define logging callbacks used by the MongoDB stream context.

### Version 0.4.0

* Added an`initialize()` method to the BaseCollection class to fall more in line with the structure of the Table class.
* Collections are no longer tied exclusively to the class name of the Collection.  Now you can pass in a `collection` key to the $config array to `CollectionRegistry` to define the collection that should be accessed in MongoDB.
* This version ties in the ability to declare events on your Collection classes such as `beforeSave()`, `beforeFind()` and the like.  To find out more, check the wiki.

### Version 0.5.0

* Custom Behavior support is now available on classes extended from the BaseCollection object.
* The `MongoBehavior` and `MongoBehaviorRegistry` classes were added to bridge the gap between Table behaviors and Mongo Collection behaviors.