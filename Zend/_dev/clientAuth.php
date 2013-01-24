 
//chemin de la librairie
set_include_path('ZendGdata-1.10.2/library');
 
//chargement des classes dont on a besoin
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Http_Client');
 
 
//on récupère le nom du service
$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
 
//données de l'utilisateur (identifiant du compte gmail)
$user = user;
$pass = password;
 
//on s'authentifie
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
 
//on crée un nouvel objet calendar
$gcal = new Zend_Gdata_Calendar($client);
 
//configuration du flux
$query = $gcal->newEventQuery();
$query->setUser('default');
$query->setVisibility('private');
$query->setProjection('basic');
$query->setOrderby('starttime');
 
//s'il n'y a pas d'erreur cela créer le flux
try{
    $feed = $gcal->getCalendarEventFeed($query);
}catch (Zend_Gdata_App_Exception $e){
    "ERREUR: " . $e->getResponse();
}
//affichage du titre de chaque evenements contenu dans l'agenda principal
foreach ($feed as $event {		
    echo $event->title;
}