<?php

declare(strict_types=1);
namespace OCA\JsonDashboard\Dashboard;
use OCA\JsonDashboard\AppInfo\Application;
use OCP\Dashboard\IConditionalWidget;
use OCP\Dashboard\IWidget;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IUserSession;
abstract class AbstractJsonDashboardWidget implements IWidget,IConditionalWidget {
    private const ICONS=['dashboard','home','thermometer','bolt','battery','server','database','wifi','cloud','water','fire','ship','clock','chart','speed','light','settings'];
    abstract protected function slot():int;
    public function __construct(protected IConfig $config,protected IUserSession $users,protected IInitialStateService $initialStateService){}
    protected function uid():string{return $this->users->getUser()?->getUID()??'';}
    public function getId():string{return Application::APP_ID.'_'.$this->slot();}
    public function getTitle():string{return $this->config->getUserValue($this->uid(),Application::APP_ID,'widget_'.$this->slot().'_title','JSON Dashboard '.$this->slot());}
    public function getOrder():int{return 40+$this->slot();}
    public function getIconClass():string{$icon=$this->config->getUserValue($this->uid(),Application::APP_ID,'widget_'.$this->slot().'_icon','dashboard');if(!in_array($icon,self::ICONS,true))$icon='dashboard';return 'icon-jsondashboard-'.$icon;}
    public function getUrl():?string{return null;}
    public function isEnabled():bool{return $this->uid()!==''&&$this->config->getUserValue($this->uid(),Application::APP_ID,'widget_'.$this->slot().'_enabled','0')==='1';}
    public function load():void{$this->initialStateService->provideInitialState(Application::APP_ID,'enabled_widget_'.$this->slot(),['slot'=>$this->slot()]);\OCP\Util::addScript(Application::APP_ID,'dashboard');\OCP\Util::addStyle(Application::APP_ID,'dashboard');}
}
