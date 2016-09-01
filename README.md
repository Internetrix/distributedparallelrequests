# Distributed Parallel Requests

Maintainer Contact
------------------
*  Guy Watson (<guy.watson@internetrix.com.au>)

## Requirements

SilverStripe 3.4.1. (Not tested with any other versions)

## Description
The Distributed Parallel Requests module replaces the domain for relative images, js and css.
The idea is to trick the browser into thinking they are on different domains so that requests can be made concurrently

### Configuration

After installation, make sure you rebuild your database through `dev/build` and run `?flush=all`
Unfortunatly the framework does not have a suitable extension hook. Add the following function to Page_Controller.php

	protected function handleAction($request, $action) {
		$result = parent::handleAction($request, $action);
		$this->extend('modifyResponse', $result);
		
		return $result;
	}

You will then need to define what domains should be used. By default subdomains are used i.e cdn1, cdn2, cdn3, cdn4

####Example 1 (setup with sub domains)

Configure the domains

	ParallelLinkExtension:
		servers:
    		- 'cdn0'
    		- 'cdn1'
    		- 'cdn2'
    		- 'cdn3'


####Example 2 (setup with full domains)

Configure the domains

	ParallelLinkExtension:
		use_full_server_domain: true
		servers:
    		- '//cdn0.mysite.com.au'
    		- '//cdn1.mysite.com.au'
    		- '//cdn2.mysite.com.au'
    		- '//cdn3.mysite.com.au'


              
