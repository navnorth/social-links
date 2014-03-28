<?php
namespace SocialLinks\Providers;

use SocialLinks\Page;

abstract class ProviderBase {
	protected $page;

	public function __construct(Page $page)
	{
		$this->page = $page;
	}


    public function __get($key)
    {
        switch ($key) {
            case 'shareApp':
            case 'shareUrl':
            case 'shareCount':
                return $this->$key = $this->$key();
        }
    }

    public function shareApp()
    {
        return null;
    }


    protected static function executeRequest($url, $post = false, array $headers = null)
    {
        $connection = curl_init();

        curl_setopt_array($connection, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => '',
            CURLOPT_AUTOREFERER => true,
            CURLOPT_USERAGENT => 'SocialLinks PHP Library'
        ));

        if (!empty($post)) {
        	curl_setopt($connection, CURLOPT_POST, true);

        	if (is_string($post)) {
        		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
        	}
        }

        if (!empty($headers)) {
        	curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        }

        $content = curl_exec($connection) ?: '';

        curl_close($connection);

        return $content;
    }

    protected function getJson($url, array $pageParams = null, array $getParams = array(), $post = false, array $headers = null)
    {
    	return json_decode(self::executeRequest($this->buildUrl($url, $pageParams, $getParams), $post, $headers), true);
    }

    protected function getJsonp($url, array $pageParams = null, array $getParams = array(), $post = false, array $headers = null)
    {
    	preg_match("/^\w+\((.*)\)$/", self::executeRequest($this->buildUrl($url, $pageParams, $getParams), $post, $headers), $matches);

    	return json_decode($matches[1], true);
    }

    protected function buildUrl($url, array $pageParams = null, array $getParams = array())
    {
    	if ($pageParams) {
    		$getParams += $this->page->get($pageParams);
    	}

    	if ($getParams) {
    		return $url.'?'.http_build_query($getParams);
    	}

    	return $url;
    }
}