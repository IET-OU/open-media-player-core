<?php namespace IET_OU\Open_Media_Player;

/**
 * Extend the base class for a generic IFRAME oEmbed provider.
 */
use \IET_OU\Open_Media_Player\Oembed_Provider;

abstract class Generic_Iframe_Oembed_Provider extends Oembed_Provider
{

    public function getView()
    {
        return __DIR__. '/../views/oembed/_generic_iframe';
    }

    protected function getIframeResponse($url)
    {
        return (object) array(
            '_comment' => '/*TODO: work-in-progress! */',
            'original_url' => $url,
            #'is_iframe' => TRUE,
            #'view_name' => $this->getView(),
            'class_name' => $this->name,
            'provider_name' => $this->displayname,
            'provider_url' => $this->_about_url,
            'provider_icon' => $this->favicon,
            'type' => $this->type, #rich
            'title'=> null,
            'width' => '100%', #640, #720,
            'height'=> 400, #$height,
            'embed_url'=> null,
        );
    }
}
