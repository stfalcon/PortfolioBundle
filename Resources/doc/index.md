Getting Started With PortfolioBundle
==================================

Simple small bundle for simple site portfolio

## Prerequisites

This version of the bundle requires:

1. Symfony >= 2.0
2. LiipFunctionalTestBundle for testing
3. DoctrineFixturesBundle for fixtures
4. SonataAdminBundle for administering
5. VichUploaderBundle for uploads
6. StofDoctrineExtensionsBundle for timestamps

## Installation

Installation is a quick 4 step process:

1. Add PortfolioBundle in your composer.json
2. Enable the Bundle
3. Import PortfolioBundle routing
4. Update your database schema

### Step 1: Add PortfolioBundle in your composer.json

```js
{
    "require": {
        "stfalcon/portfolio-bundle": "*"
    }
}
```

### Step 2: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Stfalcon\Bundle\PageBundle\StfalconPortfolioBundle(),
    );
}
```

### Step 3: Import PortfolioBundle routing

Now that you have installed and activated the bundle, all that is left to do is
import the PortfolioBundle routing.

In YAML:

``` yaml
# app/config/routing.yml
StfalconPortfolioBundle:
    resource: '@StfalconPortfolioBundle/Resources/config/routing/routing.yml'
    prefix:   /
```

### Step 4: Update your database schema

Now that the bundle is configured, the last thing you need to do is update your
database schema because you have added a two new entities, the `Project` and the `Category`.

Run the following command.

``` bash
$ php app/console doctrine:schema:update --force
```
Now that you have completed the installation and configuration of the PortfolioBundle!