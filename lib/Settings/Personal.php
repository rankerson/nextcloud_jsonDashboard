<?php

declare(strict_types=1);
namespace OCA\JsonDashboard\Settings;
use OCA\JsonDashboard\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IUserSession;
use OCP\Settings\ISettings;
final class Personal implements ISettings {
    private const ICONS=['dashboard','home','thermometer','bolt','battery','server','database','wifi','cloud','water','fire','ship','clock','chart','speed','light','settings'];
    public function __construct(private IConfig $config,private IUserSession $users){}
    public function getForm():TemplateResponse{$uid=$this->users->getUser()?->getUID()??'';$widgets=[];for($i=1;$i<=Application::MAX_WIDGETS;$i++){$icon=$this->config->getUserValue($uid,Application::APP_ID,'widget_'.$i.'_icon','dashboard');$widgets[]=['enabled'=>$this->config->getUserValue($uid,Application::APP_ID,'widget_'.$i.'_enabled','0')==='1','title'=>$this->config->getUserValue($uid,Application::APP_ID,'widget_'.$i.'_title',''),'url'=>$this->config->getUserValue($uid,Application::APP_ID,'widget_'.$i.'_url',''),'key'=>$this->config->getUserValue($uid,Application::APP_ID,'widget_'.$i.'_key','Test'),'icon'=>in_array($icon,self::ICONS,true)?$icon:'dashboard'];}return new TemplateResponse(Application::APP_ID,'personal',['widgets'=>$widgets],'');}
    public function getSection():string{return 'jsondashboard';}
    public function getPriority():int{return 10;}
}
