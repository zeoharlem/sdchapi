Phalcon Table Sort
===================

Phalcon HTML table sort component 

## Requirements

* Phalcon v1.4.x

## Installing ##

Install using Composer:

```json
{
	"require": {
		"mattdanger/phalcon-table-sort": "dev-master"
	}
}
```

You'll also need to add each function to the Volt service:

```php
$di->set('view', function () use ($config) {

  $view = new View();
  // ...
  $view->registerEngines(array(
    '.volt' => function ($view, $di) use ($config) {

      $volt = new VoltEngine($view, $di);

      $volt->getCompiler()->addFunction('sortLink', function ($resolvedArgs, $expArgs) {
        return 'TableSort\Sort::sortLink(' . $resolvedArgs . ')';
      });
      $volt->getCompiler()->addFunction('sortIcon', function ($resolvedArgs, $expArgs) {
        return 'TableSort\Sort::sortIcon(' . $resolvedArgs . ')';
      });
      // ... 

      return $volt;
    },
    // ...
  ));

  return $view;

});
```


## Using Helpers

Here's a list of what's included:

`sortLink($key, $default_sort = 'ASC')`

Return a formatted URI string with sort order

`sortIcon($key, $default = FALSE)`

Return a sort icon

