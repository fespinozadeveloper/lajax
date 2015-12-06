## lajax
Xajax integration for the Laravel framework.

The Xajax library makes it possible to export PHP classes to Javascript code, so they can be called directly from client side.

For more info about Xajax features, visit the website at http://www.xajax-project.org or Github https://github.com/Xajax/Xajax.

*** Warning: This package is still in early development stage ***

#### Installation

Install the package with Composer CLI or composer.json.

```
composer require lagdo/xajax
```

```
"lagdo/lajax": "dev-master"
```

Publish the package config and assets.

```
php artisan lajax:config
php artisan lajax:assets
```
The published assets are actually those of the lagdo/xajax package.

#### Usage

##### A simple example

Add the classes to be exported in Javascript code in app/ajax/controllers/.
They should inherit from the Lagdo\Lajax\Controller class.

Here's an example.

```
class Calculator extends Lagdo\Lajax\Controller
{
    public function __construct()
    {
        //Call parent contructor
        parent::__construct();
    }

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
}
```

In the Laravel controller, register the Xajax classes, and add a function to process Ajax requests.

```
class HomeController extends Controller
{
    public function index()
    {
        // Register the Xajax controllers
        Lajax::register();
        // Print the page
        return View::make('pages.index');
    }

    public function xajax()
    {
        // Process Ajax request
        Lajax::processRequest();
    }
}
```

Define the routes.

```
Route::get('/', array(
    'as' => 'index',
    'uses' => 'HomeController@index'
));

Route::post('xajax', array(
    'as' => 'xajax',
    'uses' => 'HomeController@xajax'
));
```

Include the Javascript in the HTML code. This function generate Javascript code for Xajax functions and file inclusion.

```
{{ Lajax::javascript() }}
```

The PHP class is then exported to Javascript, and its methods can be called directly from the page.

```
<input type="text" name="x" id="x" value="2" size="3" /> * 
<input type="text" name="y" id="y" value="3" size="3" /> = 
<input type="text" name="z" id="z" value="" size="3" /> 
<input type="button" value="Multiply" onclick="XajaxCalculator.multiply($('#x').val(), $('#y').val());return false;" />
<input type="button" value="Add" onclick="XajaxCalculator.add($('#x').val(), $('#y').val());return false;" />
```

#### Configuration

TBD

#### Advanced usage

##### Controller initialisation

TBD

##### Pre- and post-request processing

TBD

##### Exception handling

TBD

##### Calling a controller from another one

TBD
