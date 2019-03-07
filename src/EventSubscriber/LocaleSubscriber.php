<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {

        $request = $event->getRequest();
        $host=$event->getRequest()->getHost();
        $localeParam=$request->query->get('_locale');
        $localeSession=$request->getSession()->get("_locale");

        if(is_null($localeSession)){
            $localSession=$this->defaultLocale;
            /*$localeBrowser=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if($localeBrowser==="fr" || $localeBrowser==="en"){
                $localSession=$localeBrowser;
            }*/
            $host=$event->getRequest()->getHost();
            if(strstr($host,"jugementmajoritaire")){
                $localSession="fr";
            }else{
                $localSession="en";
            }
            $request->getSession()->set('_locale', $localSession);
        }

        if($localeParam==='fr' || $localeParam==='en') {
            $request->getSession()->set('_locale', $localeParam);
        }
        $request->setLocale( $request->getSession()->get('_locale'));

    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }
}