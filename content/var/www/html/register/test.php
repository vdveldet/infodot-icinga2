$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();

// Specifying all controllers as RESTful:
$restRoute = new Zend_Rest_Route($front);
$router->addRoute('default', $restRoute);

// Specifying the "api" module only as RESTful:
$restRoute = new Zend_Rest_Route($front, array(), array(
    'api',
));
$router->addRoute('rest', $restRoute);

// Specifying the "api" module as RESTful, and the "task" controller of the
// "backlog" module as RESTful:
$restRoute = new Zend_Rest_Route($front, array(), array(
    'api',
    'backlog' => array('task'),
));
$router->addRoute('rest', $restRoute);

To define a RESTful action controller, you can either extend Zend_Rest_Controller, or simply define the following methods in a standard controller extending Zend_Controller_Action (you'll need to define them regardless):

// Or extend Zend_Rest_Controller
class RestController extends Zend_Controller_Action
{
    // Handle GET and return a list of resources
    public function indexAction() {}

    // Handle GET and return a specific resource item
    public function getAction() {}

    // Handle POST requests to create a new resource item
    public function postAction() {}

    // Handle PUT requests to update a specific resource item
    public function putAction() {}

    // Handle DELETE requests to delete a specific item
    public function deleteAction() {}
}

