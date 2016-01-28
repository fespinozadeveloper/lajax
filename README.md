## lajax
Xajax integration for the Laravel framework.

The Xajax library makes it possible to export PHP classes to Javascript code, so they can be called directly from client side.

For more info about Xajax features, visit the website at http://www.xajax-project.org or Github https://github.com/Xajax/Xajax.

*** Warning: This package is still in early development stage ***

#### Installation

Install the package with Composer command line.
```
composer require lagdo/xajax
```
Or add the line below in the composer.json file.
```
"lagdo/lajax": "dev-master"
```

Publish the package config and assets.
```
php artisan lajax:config
php artisan lajax:assets
```
Note: The published assets are actually those of the lagdo/xajax package.

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

After the config is published, you will see the config file in app/config/packages/lagdo/lajax/config.php.
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
        'route' => 'xajax', // Route for the Xajax post request
        'namespace' => '', // Namespace of the Lajax controllers
        'controllers' => app_path() . '/ajax/controllers, // Location of the Lajax controllers
        'extensions' => app_path() . '/ajax/extensions', // Location of the Xajax extensions
        'excluded' => array(), // These methods will not be exported to javascript
    ),
);
```

#### Usage

##### A simple example

The classes to be exported in Javascript code must be located in the app/ajax/controllers/ directory.
They must inherit from the \Lagdo\Lajax\Controller class. Each controller has an Xajax response attribute, which is used to send commands back to the browser in response to a xajax request.
See here for more info about the xajaxResponse class: http://www.xajax-project.org/en/docs-tutorials/api-docs/xajax-core/xajaxresponse-inc-php/xajaxresponse/.

We will use the Calculator class from the https://github.com/lagdo/lajax-demo package.

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

In the Laravel controller, we need to define two functions. The first one registers the Xajax classes and prints the page. The second one will process the Xajax requests.

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

Now we have to define two routes, one for each function above.

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

Include the Javascript in the HTML code of the page.

```
{{ \Lajax::javascript() }}
```

The PHP class is then exported to Javascript, and its methods can be called directly from the page.

As an example, here's a form for the Calculator class.
```
<div class="row">
    <div class="col-md-2">
        {{ \Form::text('x', '', array('id' => 'x', 'class' => 'form-control', 'value' => '2')) }}
    </div>
    <div class="col-md-2 text-center">
        {{ \Form::button('*', array('class' => 'btn btn-secondary btn-multiply', 'title' => 'Multiply')) }}
        {{ \Form::button('+', array('class' => 'btn btn-secondary btn-add', 'title' => 'Add')) }}
        {{ \Form::button('-', array('class' => 'btn btn-secondary btn-subtract', 'title' => 'Subtract')) }}
        {{ \Form::button('/', array('class' => 'btn btn-secondary btn-divide', 'title' => 'Divide')) }}
    </div>
    <div class="col-md-2">
        {{ \Form::text('y', '', array('id' => 'y', 'class' => 'form-control', 'value' => '3')) }}
    </div>
    <div class="col-md-1 text-center"> = </div>
    <div class="col-md-2">
        {{ \Form::text('z', '', array('id' => 'z', 'class' => 'form-control', 'value' => '')) }}
    </div>
</div>

<input type="text" name="x" id="x" value="2" size="3" /> *
<input type="text" name="y" id="y" value="3" size="3" /> =
<input type="text" name="z" id="z" value="" size="3" />
<input type="button" value="Multiply" onclick="XajaxCalculator.multiply($('#x').val(), $('#y').val());return false;" />
<input type="button" value="Add" onclick="XajaxCalculator.add($('#x').val(), $('#y').val());return false;" />
```

And here's the Xajax calls to the methods of the Calculator class.
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

##### Controller initialisation

TBD

##### Pre- and post-request processing

TBD

##### Exception handling

TBD

##### Pagination

TBD

##### Classpath and namespacing

TBD

##### Calling a controller from another one

TBD

