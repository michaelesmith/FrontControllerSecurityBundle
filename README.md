[![Build Status](https://travis-ci.org/michaelesmith/FrontControllerSecurityBundle.png?branch=master)](https://travis-ci.org/michaelesmith/FrontControllerSecurityBundle)
FrontControllerSecurityBundle
======

What is FrontControllerSecurityBundle?
-------------------

It provides a simple way to secure probably a development front controller on a production machine to a specific set of ip addresses.

Installation
------------

### Use Composer (*recommended*)

The recommended way to install the FrontControllerSecurityBundle is through composer.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -s http://getcomposer.org/installer | php

Just create a `composer.json` file for your project:

``` json
{
    "require": {
        "michaelesmith/front-controller-security-bundle": "dev-master"
    }
}
```

For more info on composer see https://github.com/composer/composer

If you want to be able to use the provided cli tasks to view, add and remove ips you need to enable the bundle in your AppKernel.php

``` php
        if ('dev' == $this->getEnvironment()) {
            $bundles[] = new MS\Bundle\FrontControllerSecurityBundle\MSFrontControllerSecurityBundle();
        }

````

Usage
---------------

### Configure directly in your front controller

``` php
    //web/app_dev.php

    $loader = require_once __DIR__.'/../app/bootstrap.php.cache';

    $security = new \MS\Bundle\FrontControllerSecurityBundle\Security\IPChecker();
    $security->addIP('127.0.0.1', null, 'loopback');
    $security->addIPRange('10.0.0.1', '10.0.0.255', null, 'remote office');

    if(isset($_SERVER['HTTP_CLIENT_IP']) || isset($_SERVER['HTTP_X_FORWARDED_FOR']) || !$security->isAuthorized(@$_SERVER['REMOTE_ADDR'])){
        header('HTTP/1.0 403 Forbidden');
        exit(sprintf('You are not allowed to access this file. Maybe you are looking for <a href="%1$s">%1$s</a>. Check %2$s for more information.', 'http://' . $_SERVER['HTTP_HOST'], basename(__FILE__)));
    }

    require_once __DIR__.'/../app/AppKernel.php';

    $kernel = new AppKernel('dev', true);
    $kernel->loadClassCache();
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

```

### Configure using a file

``` php
    //web/app_dev.php

    $loader = require_once __DIR__.'/../app/bootstrap.php.cache';

    $security = new \MS\Bundle\FrontControllerSecurityBundle\Security\IPChecker();
    $security->addIP('127.0.0.1', null, 'loopback');
    $security->addFile(__DIR__ . '/.app_dev.security.json');

    if(isset($_SERVER['HTTP_CLIENT_IP']) || isset($_SERVER['HTTP_X_FORWARDED_FOR']) || !$security->isAuthorized(@$_SERVER['REMOTE_ADDR'])){
        header('HTTP/1.0 403 Forbidden');
        exit(sprintf('You are not allowed to access this file. Maybe you are looking for <a href="%1$s">%1$s</a>. Check %2$s for more information.', 'http://' . $_SERVER['HTTP_HOST'], basename(__FILE__)));
    }

    ...

```

You can add this file to your version control if you want everyone to share the same or ignore it and configure what you want on the server. This bundle includes some command tasks to help in this respect:

 * front-controller:security:ip:list
 * front-controller:security:ip:add
 * front-controller:security:ip:remove

### Configure using APC caching

``` php
    //web/app_dev.php

    $loader = require_once __DIR__.'/../app/bootstrap.php.cache';

    if(!function_exists('apc_fetch') || !($security = apc_fetch('ms.app_dev.security'))){
        $security = new \MS\Bundle\FrontControllerSecurityBundle\Security\IPChecker();
        $security->addIP('127.0.0.1', null, 'loopback');
        $security->addFile(__DIR__ . '/.app_dev.security.json');

        if(function_exists('apc_store')){
            apc_store('ms.app_dev.security', $security);
        }
    }

    if(isset($_SERVER['HTTP_CLIENT_IP']) || isset($_SERVER['HTTP_X_FORWARDED_FOR']) || !$security->isAuthorized(@$_SERVER['REMOTE_ADDR'])){
        header('HTTP/1.0 403 Forbidden');
        exit(sprintf('You are not allowed to access this file. Maybe you are looking for <a href="%1$s">%1$s</a>. Check %2$s for more information.', 'http://' . $_SERVER['HTTP_HOST'], basename(__FILE__)));
    }

    ...

```
