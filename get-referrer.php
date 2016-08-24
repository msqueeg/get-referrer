<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Uri;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class GetReferrerPlugin
 * @package Grav\Plugin
 */
class GetReferrerPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }


        // Enable the main event we are interested in
        $this->enable([
            'onPageInitialized' => ['onPageInitialized', 0]
        ]);
    }

    /**
     * Do some work for this event, full details of events can be found
     * on the learn site: http://learn.getgrav.org/plugins/event-hooks
     *
     * @param Event $e
     */
    public function onPageInitialized(Event $e)
    {
        // Get a variable from the plugin configuration
        $uri = New Uri;
        $referrer = (strpos($uri->referrer(),'bechtel.com') !== false? $uri->referrer(): false);
        if($referrer) {
            $_SESSION['referrer'] = $referrer;    
        }

        /**
         * expecting something like www.example.com/?target='url to referring page'
         */
        $target = (isset($_GET['target'])? $_GET['target'] : false);
        if($target) {
            $_SESSION['target'] = $target;
        }
        
       if($referrer || $target) {
            $this->enable([
                'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
            ]);
       }
    }

    public function onTwigSiteVariables(Event $e)
    {

        $this->grav['twig']->twig_vars['nsTarget'] = (isset($_SESSION['target'])? $_SESSION['target']: '' );


        $this->grav['twig']->twig_vars['nsReferrer'] = (isset($_SESSION['referrer'])? $_SESSION['referrer']: '' );

    }
}
