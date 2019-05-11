VoltHelpers\Helpers
===================

Useful additions to the Volt template engine 

## Installing ##

Install using Composer:

```json
{
	"require": {
		"mattdanger/volt-helpers": "dev-master"
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

      $volt->getCompiler()->addFunction('ordinal', function ($resolvedArgs, $expArgs) {
        return 'VoltHelpers\Helpers::ordinal(' . $resolvedArgs . ')';
      });
      $volt->getCompiler()->addFunction('strToCurrency', function ($resolvedArgs, $expArgs) {
        return 'VoltHelpers\Helpers::strToCurrency(' . $resolvedArgs . ')';
      });
      $volt->getCompiler()->addFunction('pluralize', function ($resolvedArgs, $expArgs) {
        return 'VoltHelpers\Helpers::pluralize(' . $resolvedArgs . ')';
      });
      $volt->getCompiler()->addFunction('paginationPath', function ($resolvedArgs, $expArgs) {
        return 'VoltHelpers\Helpers::paginationPath(' . $resolvedArgs . ')';
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

`ordinal($number)`

Number ordinal service - returns 1st, 2nd, 10th, 43rd, 724th, etc.

`strToCurrency($value)`

Output a string in currency format

`pluralize($count, $singular, $plural)`

Pluralize string

`paginationPath()`

Returns a URL encoded string with current request params plus a param for current pagination page number.

