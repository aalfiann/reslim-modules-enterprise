<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;        //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;            //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\enterprise\Enterprise as Enterprise;               //Your main modules class

//Define additional class for any purpose
use \classes\middleware\ApiKey as ApiKey;                       //ApiKey Middleware             >> To authorize request by using ApiKey generated by reSlim


    // Get module information (include cache and for public user)
    $app->map(['GET','OPTIONS'],'/enterprise/get/info/', function (Request $request, Response $response) {
        $enterprise = new Enterprise($this->db);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($enterprise->viewInfo());
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ApiKey);

    
    // Installation 
    $app->get('/enterprise/install/{username}/{token}', function (Request $request, Response $response) {
        $enterprise = new Enterprise($this->db);
        $enterprise->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $enterprise->username = $request->getAttribute('username');
        $enterprise->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($enterprise->install());
        return classes\Cors::modify($response,$body,200);
    });

    // Uninstall (This will clear all data) 
    $app->get('/enterprise/uninstall/{username}/{token}', function (Request $request, Response $response) {
        $enterprise = new Enterprise($this->db);
        $enterprise->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $enterprise->username = $request->getAttribute('username');
        $enterprise->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($enterprise->uninstall());
        return classes\Cors::modify($response,$body,200);
    });