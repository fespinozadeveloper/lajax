## lajax
Xajax integration for the Laravel framework

#### Installation

Installer le package à l'aide de Composer.

```
composer require lagdo/xajax
```

Ou bien ajouter cette ligne dans le fichier composer.json.

```
"lagdo/lajax": "dev-master"
```

#### Usage

##### Un exemple simple

Placer dans le répertoire app/ajax/controllers/ les classes à exporter avec Xajax. Elles héritent de la classe Lagdo\Lajax\Xajax\Controller.
En voici un exemple.

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

Dans un contrôleur, charger les classes dans la fonction qui affiche la page, et ajouter une fonction pour traiter la requête Ajax.

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

Enfin, inclure le code Javascript dans le code HTML de la page. Cette fonctions gènère le code et les inclusions de fichiers Javascript nécessaires à la librairie.

```
{{ Lajax::javascript() }}
```

La classe PHP est exportée dans le code Javascript de la page, et on peut par exemple écrire le code suivant.

```
XajaxDemo.multiply(x, y);
```

Pour plus d'info sur la librairie Xajax, consulter son site web http://www.xajax-project.org ou sa page Github https://github.com/Xajax/Xajax

#### Configuration


#### Advanced usage

##### Controller initialisation

##### Pre- and post-request processing

##### Exception handling

##### Calling a controller from another one
