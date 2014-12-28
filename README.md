ComposerDeployPlugin
====================

This composer plugin add extra configuration to copy folders from the vendor to another one.

It can be usefull to publish assets to a public document root.
The framework symfony uses the similar method to publish assets from users' extensions to the apache public document root.

Installation
------------

Add `jbouzekri/composer-deploy-plugin` as a dependency in `composer.json`.

``` yml
"jbouzekri/composer-deploy-plugin": "~1.0"
```

Then run the `composer install` or `composer update` command.

Configuration
-------------

All the plugin configuration must be placed in the `extra` key of your project `composer.json`.

``` json
{
    "extra": {
        "jb-composer-deploy": {
            "target-dir": "relative path to the folder where you want to deploy",
            "folders": [
                "list of folders name to deploy"
            ],
            "exclude": [
                "full package name to exclude"
            ],
            "symlink": false,
            "relative": false
        }
    }
}
```

* target-dir : relative path to the folder where you want to deploy (mandatory)
* folders : array of folders name to deploy
* exclude : array of full package name to exclude
* symlink : use symlink to deploy
* relative : use symlink relative path to deploy (symlink must be set at true)

**The target dir must exists before running the command**

Exemple
-------

In this example, all js and css folders from the project packages will be deployed in a web/composed folder
using a hard copy (no symlink) except the ones in `doctrine/orm` package.

``` json
{
    "require": {
        "doctrine/orm": "~2.2,>=2.2.3",
        "doctrine/doctrine-bundle": "~1.2",
        "jbouzekri/composer-deploy-plugin": "~1.0"
    },
    "extra": {
        "jb-composer-deploy": {
            "target-dir": "web/composed",
            "folders": [
                "js",
                "css"
            ],
            "exclude": [
                "doctrine/orm"
            ],
            "symlink": false,
            "relative": false
        }
    }
}
```

Imagine that a first composer install has created the following vendor tree :

* composer.phar
* composer.json
* vendor/jbouzekri/composer-deploy-plugin
* vendor/jbouzekri/composer-deploy-plugin/css
* vendor/doctrine/orm
* vendor/doctrine/orm/src
* vendor/doctrine/orm/css
* vendor/doctrine/doctrine-bundle
* vendor/doctrine/doctrine-bundle/src
* vendor/doctrine/doctrine-bundle/css
* vendor/doctrine/doctrine-bundle/js

After configuring the deploy plugin with the previous configuration, each composer install or update
will copy the css and js folders of all packages to the folder web/composed creating the following tree :

* web/composed/doctrine-orm/css
* web/composed/doctrine-bundle/css
* web/composed/doctrine-bundle/js

License
-------

[MIT](https://github.com/jbouzekri/ComposerDeployPlugin/blob/master/README.md)