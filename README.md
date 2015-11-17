# lajax
Xajax integration for the Laravel framework

## Installation

## Usage

### Un exemple simple

Placer dans le répertoire app/ajax/controllers/ les classes à exporter en Javascript dans la page HTML. Elles héritent de la classe Lagdo\Lajax\Xajax\Controller.

```
class Demo extends Lagdo\Lajax\Xajax\Controller
{
    public function __construct()
    {
        // Appel du constructeur du parent
        parent::__construct();
    }

    public function multiply($x, $y)
    {
        $this->response->assign("z", "value", intval($x) * intval($y));
        return $this->response;
    }
}
```

Charger les classes dans la fonction d'affichage de la page, et ajouter une focntion pour traiter la requête Ajax.

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

Définir les routes.

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

Enfin, inclure le code Javascript dans la page.

```
{{ Lajax::javascript() }}
```

La classe PHP est exportée dans le code Javascript de la page, et on peut l'appeler avec le code suivant.

```
XajaxDemo.multiply(x, y);
```

Pour plus d'info sur la librairie Xajax, consulter son site web: http://www.xajax-project.org.

## Configuration


## Advanced usage

### Controller initialisation

### Pre- and post-request processing

### Exception handling

### Calling a controller in another one
