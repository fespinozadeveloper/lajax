## lajax
Xajax integration for Laravel 4.2 and 5.

The Xajax library makes it possible to export PHP classes to Javascript code, so they can be called directly from client side.

For more info about Xajax features, visit the website at http://www.xajax-project.org or Github https://github.com/Xajax/Xajax.

*** Warning: This package is still in early development stage ***

#### Installation

The package installs with Composer.

##### For Laravel 5

Add `lagdo/lajax` to the `composer.json` file.
```
"lagdo/lajax": "0.2.*"
```

Run Composer to update the package to the latest version, and then publish the config and assets.
```
composer update
php artisan vendor:publish
```

In config/app.php, register the service provider and the facade.
```
'providers' => array(
  Lagdo\Lajax\LajaxServiceProvider::class,
);
```

```
'aliases' => array(
  'Lajax'  => Lagdo\Lajax\Facades\Lajax::class,
);
```

##### For Laravel 4.2

Update the composer.json file.
```
"lagdo/lajax": "0.1.*"
```

Run Composer to update the package to the latest version, and then publish the config and assets.
```
composer update
php artisan lajax:config
php artisan lajax:assets
```

In config/app.php, register the service provider and the facade.
```
'providers' => array(
  'Lagdo\Lajax\LajaxServiceProvider'
);
```

```
'aliases' => array(
  'Lajax'  => 'Lagdo\Lajax\Facades\Lajax',
);
```

#### Configuration

The Lajax config file can be updated with application specific settings.

For Laravel 5, the config file is located at
```
config/lajax.php
```

And for Laravel 4.2,
```
config/packages/lagdo/lajax/config.php
```

The settings of the Xajax library are under the 'lib' key, and the Laravel application are under the 'app' key.
```
<?php
return array(
    'lib' => array(
        'wrapperPrefix' => 'Xajax',
        'characterEncoding' => 'UTF-8',
        'deferScriptGeneration' => false,
        'javascript_URI' => asset('/assets/xajax'),
        'javascript_Dir' => public_path('/assets/xajax'),
        'errorHandler' => false,
        'debug' => false,
    ),
    'app' => array(
        'route' => 'xajax',    // Route for the Xajax post request
        'namespace' => '',     // Namespace of the Lajax controllers
        'controllers' => '',   // Location of the Lajax controllers, defaults to app_path('/Ajax/Controllers)
        'extensions' => '',    // Location of the Xajax extensions, defaults to app_path('/Ajax/Extensions')
        'excluded' => array(), // These methods will not be exported to javascript
    ),
);
```

#### Usage

##### A simple example

By default, the classes to be exported in Javascript code must be located in the app/Ajax/Controllers/ directory.

They must inherit from the \Lagdo\Lajax\Controller class. Each controller has an Xajax response attribute, which is used to send commands back to the browser in response to a xajax request.
See here for more info about the xajaxResponse class: http://www.xajax-project.org/en/docs-tutorials/api-docs/xajax-core/xajaxresponse-inc-php/xajaxresponse/.

We will use the Calculator class from the https://github.com/lagdo/lajax-demo package.
The form below lets the user enter two numbers and choose an operation. The result is then printed in the third input.
```
<div class="row">
    <div class="col-md-2">
    	<input type="text" name="x" id="x" class="form-control" value="2" />
    </div>
    <div class="col-md-2 text-center">
    	<button class="btn btn-secondary btn-multiply" title="Multiply"> * </button>
    	<button class="btn btn-secondary btn-add" title="Add"> + </button>
    	<button class="btn btn-secondary btn-subtract" title="Subtract"> - </button>
    	<button class="btn btn-secondary btn-divide" title="Divide"> / </button>
    </div>
    <div class="col-md-2">
    	<input type="text" name="y" id="y" class="form-control" value="3" />
    </div>
    <div class="col-md-1 text-center"> = </div>
    <div class="col-md-2">
    	<input type="text" name="z" id="z" class="form-control" value="" />
    </div>
</div>
```

This is the Lajax controller class.
It performs the calculation and prints the result back in the form using an Xajax response object.

```
class Calculator extends \Lagdo\Lajax\Controller
{
    public function multiply($x, $y)
    {
        $this->response->assign("z", "value", intval($x) * intval($y));
        return $this->response;
    }
    public function add($x, $y)
    {
        $this->response->assign("z", "value", intval($x) + intval($y));
        return $this->response;
    }
    public function subtract($x, $y)
    {
        $this->response->assign("z", "value", intval($x) - intval($y));
        return $this->response;
    }
    public function divide($x, $y)
    {
        if(!intval($y))
            throw new \Exception('Cannot divide by zero');
        $this->response->assign("z", "value", intval($x) / intval($y));
        return $this->response;
    }
}
```

Now we'll see how to tie the form and the Lajax controller class together.

In the Laravel controller, we'll define two functions.
The first one registers the Lajax controllers and prints the page, while the second one will process the Ajax requests.

```
class HomeController extends Controller
{
    public function index()
    {
        \Lajax::register();
        return View::make('pages.index');
    }
    public function xajax()
    {
        // Process Ajax request
        \Lajax::processRequest();
    }
}
```

Now let define two routes, one for each function above.

```
Route::get('/', array(
    'as' => 'index',
    'uses' => 'HomeController@index'
));

Route::post(Config::get('lajax::app.route', 'xajax'), array(
    'as' => 'xajax',
    'uses' => 'HomeController@xajax'
));
```

After the Lajax controllers are registered, the corresponding javascript code now have to be generated
and included in the HTML code of the page.

```
{{ \Lajax::javascript() }}
```

Finally, here's the Xajax calls to the methods of the Calculator class.
```
<script type="text/javascript">
$(document).ready(function(){
    $('.btn-multiply').click(function(){
        XajaxCalculator.multiply($('#x').val(), $('#y').val());
    });
    $('.btn-add').click(function(){
        XajaxCalculator.add($('#x').val(), $('#y').val());
    });
    $('.btn-subtract').click(function(){
        XajaxCalculator.subtract($('#x').val(), $('#y').val());
    });
    $('.btn-divide').click(function(){
        XajaxCalculator.divide($('#x').val(), $('#y').val());
    });
});
</script>
```

That's all for a simple example. Now we'll see more advanced features on the package.

#### Advanced usage

##### Exception handling

The app/start/global.php file contains an error handler for all exceptions in a Laravel application.
This function needs to be modified as follow to handle exceptions thrown in Lajax controllers. 

```
App::error(function(Exception $exception)
{
	Log::error($exception);

	// Process exception thrown from Xajax request
	if(\Lajax::hasRequest())
	{
		$ajaxResponse = \Lajax::response();
		// ... Print an error message for the application here
		// Return a Laravel HTTP response
		return $ajaxResponse->http();
	}
});
```

##### Calling a controller from another one

An Lajax controller can be instanciated anywhere in the application.

```
$controller = \Lajax::controller('Class');
```

Thank to this feature, several Lajax controllers can be used to process a single request.

```
class A extends \Lagdo\Lajax\Controller
{
	public function doA()
	{
		// ...
		return $this->response;
	}
}

class B extends \Lagdo\Lajax\Controller
{
	public function doB()
	{
		$a = \Lajax::controller('A');
		// ...
		$a->doA();
		// ...
		return $this->response;
	}
}
```

Since all the Lajax controllers share the same instance of the Xajax response, no extra action is required to concatenate the results together.

##### Controller initialization

An Lajax controller can be initialized either by an init callback, or by an init method.

The init callback is called each time a new Lajax controller object is created, and it takes the controller as parameter.
```
\Lajax::setInitCallback(function($controller){
	// Initialize the controller here
});
```

Any init method defined in an Lajax controller class will be called when the class is instanciated, after the init callback. 

##### Pre- and post-request processing

The Lajax library allows the definition of two additional callbacks, which are called before and after the request is actually processed.

```
\Lajax::setPreCallback(function($controller, $method, &$bEndRequest){
	// ...
});

\Lajax::setPostCallback(function($controller, $method){
	// ...
});
```

When they are defined, these callbacks are called only once for each request. 

If the $bEndRequest parameter is set to true in the pre-callback function, the request is not processed further and the Xajax response is returned.
This feature can be used for example to implement checks on user session and access rights before executing an action.

##### Classpath and namespacing

When processing a request, Lajax automatically loads the corresponding controller file, based on the class name in the Xajax request.
The class A will be loaded from the A.php in the Lajax controller directory, and exported to javascript as the A class.

Sometimes, a developer may want to organize the Lajax controllers into multi-level subdirectories.
Lajax provides a mechanism called classpath to cope with this requirement.
When exporting Lajax controller classes to javascript, the intermediate directories are turned into class hierarchy.
For example, the class defined in A/B/C.php file in PHP will be exported as A.B.C in javascript.
```
$('.the-button').click(function(){
    A.B.C.f();
});
```
It is instanciated in the Laravel application as follow.
```
$a = \Lajax::controller('A.B.C');
```

When using classpath only, Lajax controller class names must still be unique. That is, defining classes B/A.php and C/A.php
will lead to a PHP duplicate class error.
In order to be able to define classes with same name in different directories, the Lajax controller classes shall be namespaced.

The namespace needs to be setup in the Lajax config file, and declared accordingly in all Lajax controller classes.

```
<?php
return array(
    'lib' => array(
        // ...
    ),
    'app' => array(
        'namespace' => 'Xajax', // Namespace of the Lajax controllers
        // ...
    ),
);
```

In A.php,
```
namespace Xajax;

class A extends \Lagdo\Lajax\Controller
{
	public function doA()
	{
		// ...
		return $this->response;
	}
}

```

In B/A.php,
```
namespace Xajax\B;

class A extends \Lagdo\Lajax\Controller
{
	public function doA()
	{
		// ...
		return $this->response;
	}
}

```

In C/A.php,
```
namespace Xajax\C;

class A extends \Lagdo\Lajax\Controller
{
	public function doA()
	{
		// ...
		return $this->response;
	}
}

```
 
Using namespace do not alter the names of the javascript classes. They are still named with their classpath within the
Lajax controller directory.

##### Request helpers

Lajax provides a set of helpers to ease the generation of client side calls to controller methods.

```
<button class="btn btn-secondary" onclick="{{ lxCall('A', 'doA') }}"> Do A </button>
<button class="btn btn-secondary" onclick="{{ lxCall('B.A', 'doA') }}"> Do A </button>
<button class="btn btn-secondary" onclick="{{ lxCall('C.A', 'doA') }}"> Do A </button>
```

These helpers handle calls to Lajax controller methods, as well as their parameters.
For example, the calls to the Calculator class can be written as follow.

```
<script type="text/javascript">
$(document).ready(function(){
    $('.btn-multiply').click(function(){
    	{{ lxCall('Calculator', 'multiply', array(lxInput('x'), lxInput('y'))) }};
    });
    $('.btn-add').click(function(){
    	{{ lxCall('Calculator', 'add', array(lxInput('x'), lxInput('y'))) }};
    });
    $('.btn-subtract').click(function(){
    	{{ lxCall('Calculator', 'subtract', array(lxInput('x'), lxInput('y'))) }};
    });
    $('.btn-divide').click(function(){
    	{{ lxCall('Calculator', 'divide', array(lxInput('x'), lxInput('y'))) }};
    });
});
</script>
```

##### Pagination

There are some differences between the pagination in Laravel and Lajax.
While Laravel generates links to different web pages, Lajax generates calls to different javascript functions.
Therefore, the pagination function in Laravel needs to be provided with a method of a controller, and its whole set of parameters.

Pagination is setup with a simple call.

```
\Lajax::paginate($currentPage, $itemsPerPage, $itemsTotal, $controller, $method, $parameters);
```

The last parameter is optional and defaults to an empty array.
The helper function lxPageNumber() can be used to specify the page number in the parameters array.
If the page number is omitted, Lajax will automatically append it at the end of the parameters array.
