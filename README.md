# SymfonyDevTCBundle

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require symfony-devs/symfony-dev-tc-bundle dev-master
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new SymfonyDev\TCBundle\SymfonyDevTCBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Setup Route
-------------------

Add bellow code to your routing.yml file.

```yml

symfony_dev_tc:
    resource: "@SymfonyDevTCBundle/Resources/config/routing.yml"
    prefix:   /payment
    
```