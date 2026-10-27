<?php

namespace rpf\extension\module;
use rpf\extension\extensionModule;
use rpf\system\module\exception;


/**
 * RP2-Direct-Login
 *
 * EXPERIMENTAL / CURRENTLY NON-FUNCTIONAL:
 * Caution: Doesn't work cause of creepy api-implementation of df:
 * You have to authenticate as admin to authenticate as user. WTF?!
 *
 * The RPC-SID can't be used as RP2-Login-Token :(
 *
 * execute() fails loudly instead of silently redirecting into a dead end,
 * until DomainFactory's RP2-API supports handing over an authenticated
 * session to the panel. See the commented-out attempts below for context.
 *
 * @package rpf\extension\module
 */
class rp2Login extends extensionModule
{
    /*public function httpAuth()
    {
        $token = $this->getApi()->getSessionAuthToken()->getAuthToken($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        if ( !(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && $token == NULL ))
        {
            static::sendHttpAuth();
            return false;
        }
        else
        {
            return $token;
        }
    }

    protected static function sendHttpAuth()
    {
        header("WWW-Authenticate: Basic realm=\"Please enter your RP2-User and Password");
        header('HTTP/1.0 401 Unauthorized');
        \rpf\showError('Login', "Please authenticate with your RP²-Username and Password");
        exit;
    }

    public function execute($param)
    {
        parent::execute($param);
        $token = $this->httpAuth();
        header("Redirect:".RPF_API_MODULE_BBRPC_SETURL_URL.$token);
    }*/

    public function execute($param)
    {
        parent::execute($param);
        throw new exception(
            'rp2Login is not functional: the RP2-API does not allow using the RPC-Session-ID '.
            'as a panel-login-token (see class-doc). Don\'t rely on this extension until '.
            'DomainFactory provides a supported way to hand over an authenticated session.'
        );
    }
}