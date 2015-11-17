## lajax
Xajax integration for the Laravel framework

#### Installation

Install the package with Composer CLI or composer.json.

```
composer require lagdo/xajax
```

```
"lagdo/lajax": "dev-master"
```

#### Usage

##### A simple example

Add the classes to be exported in JS code in app/ajax/controllers/. They inherit from the Lagdo\Lajax\Xajax\Controller class.
Here's an example.

```
class Demo extends Lagdo\Lajax\Xajax\Controller
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
}
```

In the Laravel controller, register the exported classes, and add a function to process Ajax requests.

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
<input type="button" value="Calculate" onclick="XajaxDemo.multiply($('#x').val(), $('#y').val());return false;" />
```

For more info about Xajax library, visit the website at http://www.xajax-project.org or Github https://github.com/Xajax/Xajax.

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
