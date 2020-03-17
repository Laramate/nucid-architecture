The Nucid Architecture for Laravel
===============================================================================
* [Description](#description)
* [Nucid Architecture](#nucid-architecture)
  * [Services](#services)
  * [Features](#features)
  * [Domains](#domains)
  * [Jobs and operations](#jobs-and-operations)
* [Nucid Implementation](#nucid-implementation)
  * [Installation](#installation)
  * [Setting up Services](#setting-up-services)
  * [Configuration](#configuration)
  * [Additional tasks](#additional-tasks)
* [Further Information](#further-information)
  * [Changelog](#changelog)
  * [License](#license)


Description
-------------------------------------------------------------------------------
The Nucid Architecture for Laravel is a software architecture for structuring
a Laravel application based on services. Nucid supports you to build large
scale applications and keep them maintainable. In comparison to other approaches,
it keeps the Laravel concepts as they are, flexible and customizable.


This project is inspired by [The Lucid Architecture](https://github.com/lucid-architecture/laravel)
developed by [Abed Halawi](https://tech.vinelab.com/@mulkave). We made some adjustments to the core 
concepts and wrote a new package-based implementation with a lot of new features. 

The documentation is still in progess... sorry :)


Nucid Architecture
-------------------------------------------------------------------------------
The main goal of the Nucid Architecture for Laravel is to give you and your team
and application structure which is high scalable, easy maintainable and prevents 
redundancies.  

### Services
Modern webapplications have to do a lot more tasks than just displaying frontend 
websites. They have to deliver content in different formats, need a backend to 
get administrated and offering APIs to communicate with other solutions. Instead
of doing all these things anywhere in your project, we seperate them into different
parts. Thats what we call a service in Nucid. It consists of features.

### Features
A feature do not implement any concrete logic, it controls which jobs and operations
will be dispatched to solve the requested tasks for the current service. 

> Note that jobs, operations and features are provided in the seperate package
> [Laramate/composite](https://github.com/Laramate/composite). This allows you
> to reuse them in other projects no matter if they are using the Nucid 
> Architecture or not.  

### Domains
As in many architecture concepts, the domains implement the business logic. A domain
is a thematically group of concrete implementions and can consist of models, value objects,
factories, utility classes etc. It offers jobs and operations that can be called from the
features. 

Nucid does not define more conventions for the structure of the domains. There are a lot
concepts for that and most of them are good solutions and there is no reason to force a
common structure. Quite the contrary: It can be usefull to adjust it to your project size 
and needings.   

### Jobs and operations
work in progress

> Note that jobs and operations are implementing Laravel's queuing and serializing
> Trait. So you can do all the things which you know from the Laravel jobs.




Nucid Implementation
-------------------------------------------------------------------------------

### Installation
You can install the package via composer.

```bash
composer require laramate/nucid-architecture
```

After the composer installation you can customize paths in the [Nucid configuration](#configuration).

After you finished configuration, use the Nucid Artisan command to setup required 
directories and files. This will not override any existing files and folders.

```
 php artisan nucid:install
```

At least, you should remove the laravel route service provider from the 
```config/app.php``` configuration file.

```
App\Providers\RouteServiceProvider::class
```

### Setting up Services
Publish the Nucid services configuration file:

```
 php artisan vendor:publish --tag=laramate-nucid-services
```

After that you can find the ```nucid_services.php``` configuration file in the standard
Laravel ```config/``` folder.

It contains an array with the service confiuration where the key is the service name.
Here is an example:

```
'frontend' => [
    'subdomain'     => null,
    'route_prefix'  => null,
    'relative_path' => 'Frontend',
    'middleware'    => ['web'],
]
```

#### Service Options

##### Subdomain
If you set up a subdomain for the service, the service will only be loaded if the
subdomain matches. For example
```
'subdomain' => 'api.',
```
would match api.example.com.

##### Route prefix
If you set up a route prefix, the service will only be loaded if it matches the 
beginning of the request URI. For example
```
'route_prefix' => 'backend',
```
would match www.example.com/backend

##### Relative path
Set up the relative path of the service. All services will be resolved by using
the ```base_path```, which you can change in the Nucid configuration.
For example:
```
'route_prefix' => 'Frontend',
```


### Configuration
Usually you don't need to publish the Nucid configuration file, because
nearly all of the options can be overwritten by setting up env options.

However, if you want to configure it directly, you can publish the Nucid
configuration with:

```
 php artisan vendor:publish --tag=laramate-nucid-config
```

After that you can find the ```nucid_config.php``` configuration file in the standard
Laravel ```config/``` folder.

#### Activate/Deactivate Nucid Service System
You can activate or deactivate the whole nucid services system.
``` 
'active' => true,
```

Or set up the following option in your .env file:
```
NUCID_SERVICES_STATE=true
```

#### Default Service
You can set the default service manually. If you leave it blank,
the first service in the service configuration will be used.
```
'default' => 'frontend',
```

Or set up the following option in your .env file:
```
NUCID_SERVICES_DEFAULT=frontend
```

#### Service Base path
The Nucid services base path: 
```
'base_path' => 'app'.DIRECTORY_SEPARATOR.'Services',
```

Or set up the following option in your .env file:
```
NUCID_SERVICES_BASE_PATH=app/Services
```

#### Domains path
The domains path: 
```
'domain_path' => 'app'.DIRECTORY_SEPARATOR.'Domains',
```

Or set up the following option in your .env file:
```
NUCID_SERVICES_DOMAINS_PATH=app/Domains
```

#### Nucid services subfolders
```
'routes_dir'      => 'Routes',
'providers_dir'   => 'Providers',
'controllers_dir' => 'Controllers',
'features_dir'    => 'Features',
'resources_dir'   => 'Resources',
'requests_dir'    => 'Requests',
```

Or set up the following options in your .env file:
```
NUCID_SERVICES_ROUTES_DIR=Routes
NUCID_SERVICES_PROVIDERS_DIR=Providers
NUCID_SERVICES_CONTROLLERS_DIR=Controllers
NUCID_SERVICES_FEATURES_DIR=Features
NUCID_SERVICES_RESOURCES_DIR=Resources
NUCID_SERVICES_REQUESTS_DIR=Requests
```

#### Nucid Services file names

```
'routes_file'         => 'routes.php',
'service_config_file' => 'service.php',
```

Or set up the following options in your .env file:
```
NUCID_SERVICES_ROUTES_FILE=routes.php
NUCID_SERVICES_SERVICE_CONFIG_FILE=Providers=service.php
```

### Additional tasks

#### Moving the User Model to your Domain folder
Create a ```User``` Folder inside you domain directory. Then move
the standard Laravel user model ```app/User.php``` in this directory. 
Open the file and fix the namespace. 

Now open the Laravel auth config in the default config directory 
```config/auth.php```. Set up the moved user model.  

```
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => \App\Domains\User\User::class,
    ]
]
```


Further Information
-------------------------------------------------------------------------------

### Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed 
recently.

### License
The MIT License (MIT). Please see [License File](LICENSE.md) for more 
information.



---
&copy; 2019 Laramate
&nbsp;&bull;&nbsp; [MIT License](LICENSE.md)
&nbsp;&bull;&nbsp; [www.laramate.de][Laramate Website]
&nbsp;&bull;&nbsp; [github.com/Laramate][Laramate Github]

<!-- Common References -->
[logo]: https://avatars1.githubusercontent.com/u/45978330?s=100
[Laramate Website]: http://www.laramate.de 
[Laramate Github]: https://github.com/Laramate
