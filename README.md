# lucite/apispec
A library for building an mostly unopinionated openapi specifications, with additional functions for building a *strongly* opinionated specification that can be easily connected to other php frameworks.

## Unopinionated building blocks

The library provides set of classes for defining and connecting various parts of an openapi specification.

For example, you can create a string property named `title` with a minimum length of 10 like this:

`$titleProp = new Lucite\ApiSpec\Property('title', 'string', ['minLength' => 10]);`

You could then add that to a schema named `Employee` like this:

`$schema = (new Lucite\ApiSpec\Schema('Employee'))->addProperty($titleProp);`

The root level of a specification is, unsurprisingly, a `Specification` object:

`$spec = new Lucite\ApiSpec\Specification('myapi', 'v1.1');`

Other classes that are provided:

- `Path`, which can be added to a `Specification` instance.
- `Method`, which can be added to a `Path` instance.
- `PathParameter`, which can be added to a `Method` instance.
- `QueryParameter`, which can be added to a `Method ` instance.
- `Response`, which can be added to a `Method` instance.

While these classes do not allow for the construction of any possible openapi specification, they cover many common cases.

## Opinionated structure

The `Lucite\ApiSpec\Specification` class has a number of shortcut methods that let you quickly build a highly opinionated API structure.

For a given resource type (say, a Book), one can easily add these 5 routes:

- `GET /books/` which returns an array of zero or more Book resources
- `GET /books/{bookId}` which returns a single Book resource by id
- `POST /books/` which creats a single Book resource
- `PATCH /books/{bookId}` which updates a single Book resource by id
- `DELETE /books/{bookId}` which deletes a single Book resource by id

To build something like this,

```php
$bookSchema = (new Lucite\ApiSpec\Schema('Book'))
    ->addProperty(new Property('bookId', 'number'))
    ->addProperty(new Property('title', 'string', ['minLength' => 1]))
    ->addProperty(new Property('description', 'string'));

$spec = new Lucite\ApiSpec\Specification('testspec', '1.2.3');
$spec->addRestMethods('/books/', $bookSchema);
```

### Request format

The POST and PATCH methods require a `content-type: application/json` header with a request body containing the following structure:

```json
{
  "data": {
    /* schema fields here*/
  }
}
```

### Reponse format

There are 4 response formats that may be returned from the above routes:

- A successful, single resource response such as:

  ```json
  {
	  "succeess": true,
	  "warnings": {/* object containing warnings */},
	  "data": {/* schema properties here*/}
  }
  ```

  This structure is returned from successful requests to: `GET /books/{bookId}`, `POST /books/`, `PATCH /books/{bookId}`

- A successful collection of resources such as:

  ```json
  {
	  "succeess": true,
	  "warnings": {/* object containing warnings */},
	  "data": [
		{/* schema properties here*/},
		{/* schema properties here*/},
		{/* schema properties here*/},
	  ]
  }
  ```

  This structure is returned from successful requests to: `GET /books/`

- An unsuccessul response such as:

  ```json
  {
     "success": false,
     "warnings": {/* object containing warnings */},
     "errors": {/* object containing errors */}
  }
  ```
  
  This format is returned from `POST /books/` and `PATCH /books/{bookId}` when the request fails.

- An empty response with status code such as:
  - 401 Not Authorized
  - 404 Not Found
  - 204 Deleted


## Usage in a framework

### Writing specification json (yaml not supported)

```php
$spec = new Lucite\ApiSpec\Specification('myapi', 'v1.0.0');
# Add schemas, routes, etc to spec

file_put_contents(
    __DIR__.'/spec.json',
    json_encode($spec->finalize()),
);
```

### Looping over routes to add them to an app

Here's an example of how to use a Specification instance to map routes in Lumen or Slim. Other frameworks are probably very similar.

```php
$bookSchema = (new Lucite\ApiSpec\Schema('Book'))
    ->addProperty(new Lucite\ApiSpec\Property('bookId', 'number'))
    ->addProperty(new Lucite\ApiSpec\Property('title', 'string', ['minLength' => 1]))
    ->addProperty(new Lucite\ApiSpec\Property('description', 'string'));

$spec = new Lucite\ApiSpec\Specification('testspec', '1.2.3');
$spec->addRestMethods('/books/', $bookSchema);


$app = something(); # Framework dependent

foreach ($spec->generateRoutes() as $method => $details) {
    [$path, $schemaName, $function] = $details;
    
    # Lumen framework, requires you to define a class that can be autoloaded
    # via the name Book (you probably want to prepend this with a namespace)
    # with methods getOne, getMany, create, update, delete. Each method
    # should accept params
    # - $args
    # and return a string or array
    # See Lumen framework docs
    $app->$method($path, ['uses' => $schemaName.'Controller@'.$function]);
    
    # Slim framework, requires you to define a class that can be autoloaded
    # via the name Book (you probably want to prepend this with a namespace)
    # with methods getOne, getMany, create, update, delete. Each method
    # should accept params:
    # - Psr\Http\Message\ServerRequestInterface $request
    # - Psr\Http\Message\ResponseInterface $response
    # - array $args
    # and return Psr\Http\Message\ResponseInterface.
    # See slim framework docs
    $app->$method($path, $schemaName.':'.$function);
}
```

### Using Schema validators

Once a schema is defined, you can create a Lucite\ApiSpec\Validator from it. The validator takes an array and returns EITHER:

- a boolean true if the data passes validation
- an associative array of errors

Example:

```php
$bookSchema = (new Lucite\ApiSpec\Schema('Book'))
    ->addProperty(new Lucite\ApiSpec\Property('bookId', 'number'))
    ->addProperty(new Lucite\ApiSpec\Property('title', 'string', ['minLength' => 1]))
    ->addProperty(new Lucite\ApiSpec\Property('description', 'string'));

$data = ['name' => '']);
$result = $bookSchema->getValidator->validate($data);
# Will return an array containing: ['name' => '{field} must be at least 1 characters long']
```

You can get a validator for a schema from the specification object by schema name:

```php
$bookSchema = (new Lucite\ApiSpec\Schema('Book'))
    ->addProperty(new Lucite\ApiSpec\Property('bookId', 'number'))
    ->addProperty(new Lucite\ApiSpec\Property('title', 'string', ['minLength' => 1]))
    ->addProperty(new Lucite\ApiSpec\Property('description', 'string'));

$spec = new Lucite\ApiSpec\Specification('testspec', '1.2.3');
$spec->addRestMethods('/books/', $bookSchema);
$validator = $spec->getSchema('Book')->getValidator();
```

A good way to use this functionality would be to add the specification object to your dependency injection container, and then ->get() the spec/validator in a controller.

