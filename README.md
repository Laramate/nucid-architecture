The Nucid Architecture for Laravel
===============================================================================
* [Description](#description)
* [Documentation](#documentation)
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
a Laravel application based on services. 

This project is inspired by [The Lucid Architecture](https://github.com/lucid-architecture/laravel)
developed by [Abed Halawi](https://tech.vinelab.com/@mulkave).

We made some adjustments to the core concepts and wrote a new package-based
implementation with a lot of new features. 

The documentation is still in progess... sorry :)


Documentation
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
