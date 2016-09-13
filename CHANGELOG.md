# Change Log

### Version 0.1.0

This is the initial release version of this plugin.  The plugin as it is currently provides access to a MongoDB Datasource configured within the config/app.php file.

### Version 0.2.0

* Collection level object support added.  Developers can now define models inside of `src\Model\MongoCollection` that extend
the `BaseCollection` class so that business logic can be abstracted and encapsulated.  This class wraps all of the main data access methods
provided by the Monga API's collection object.
* A `CollectionRegistry` singleton has been added for constructing and retrieving custom Collections that extends the `BaseCollection` class.