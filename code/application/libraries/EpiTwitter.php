<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EpiTwitter extends EpiOAuth
{
    const EPITWITTER_SIGNATURE_METHOD = 'HMAC-SHA1';
    protected $requestTokenUrl = 'http://twitter.com/oauth/request_token';
    protected $accessTokenUrl = 'http://twitter.com/oauth/access_token';
    protected $authorizeUrl = 'http://twitter.com/oauth/authorize';
    protected $apiUrl = 'http://twitter.com';

    public function __call($name, $params = null)
    {
        $parts = explode('_', $name);
        $method = strtoupper(array_shift($parts));
        $parts = implode('_', $parts);
        $url = $this->apiUrl . '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
        if (!empty($params))
            $args = array_shift($params);

        return new EpiTwitterJson(call_user_func(array($this, 'httpRequest'), $method, $url, $args));
    }

    /**
     * Modified by prasad to make it accept an array as a single paramer which holds all required paramerts because liad->library method cant send multple paramers to any library
     * un modified signature: public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null)
     *
     * @author prasad
     * @date 30-08-2012
     *
     * @param $config
     * @internal param null $consumerKey
     * @internal param null $consumerSecret
     * @internal param null $oauthToken
     * @internal param null $oauthTokenSecret
     */
    public function __construct($config)
    {
        /**modified by prasad starts here**/
        $consumerKey = isset($config[0])?$config[0]:null;
        $consumerSecret = isset($config[1])?$config[1]:null;
        $oauthToken = isset($config[2])?$config[2]:null;
            $oauthTokenSecret = isset($config[3])?$config[3]:null;
        /**modified by prasad ends here**/

        parent::__construct(array($consumerKey, $consumerSecret, self::EPITWITTER_SIGNATURE_METHOD));
        $this->setToken($oauthToken, $oauthTokenSecret);
    }
}

class EpiTwitterJson
{
    private $resp;

    public function __construct($resp)
    {
        $this->resp = $resp;
    }

    public function __get($name)
    {
        $this->responseText = $this->resp->data;
        $this->response = (array)json_decode($this->responseText, 1);
        foreach ($this->response as $k => $v)
        {
            $this->$k = $v;
        }

        return $this->$name;
    }
}
