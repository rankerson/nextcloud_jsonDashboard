<?php

declare(strict_types=1);
namespace OCA\JsonDashboard\Controller;
use OCA\JsonDashboard\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;
final class SettingsController extends Controller {
    private const ICONS=['dashboard','home','thermometer','bolt','battery','server','database','wifi','cloud','water','fire','ship','clock','chart','speed','light','settings'];
    public function __construct(IRequest $request,private IConfig $config,private IUserSession $users){parent::__construct(Application::APP_ID,$request);}
    public function save(array $widgets):DataResponse{$uid=$this->users->getUser()?->getUID()??'';if($uid==='')return new DataResponse(['message'=>'Nicht angemeldet.'],401);for($i=1;$i<=Application::MAX_WIDGETS;$i++){$row=$widgets[$i-1]??[];$enabled=!empty($row['enabled']);$title=trim((string)($row['title']??''));$url=trim((string)($row['url']??''));$key=trim((string)($row['key']??''));$icon=(string)($row['icon']??'dashboard');if(!in_array($icon,self::ICONS,true))$icon='dashboard';if($enabled&&(filter_var($url,FILTER_VALIDATE_URL)===false||strtolower((string)parse_url($url,PHP_URL_SCHEME))!=='https'||$title===''||$key===''))return new DataResponse(['message'=>'Widget '.$i.': Titel, HTTPS-URL und JSON-Hauptschlüssel prüfen.'],400);foreach(['enabled'=>$enabled?'1':'0','title'=>$title,'url'=>$url,'key'=>$key,'icon'=>$icon] as $name=>$value)$this->config->setUserValue($uid,Application::APP_ID,'widget_'.$i.'_'.$name,$value);}return new DataResponse(['status'=>'ok']);}
}
