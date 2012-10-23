Getting Started With PortfolioBundle
==================================

Simple small bundle for simple site portfolio

## Prerequisites

This version of the bundle requires:

1. Symfony >= 2.1
2. LiipFunctionalTestBundle for testing
3. DoctrineFixturesBundle for fixtures
4. SonataAdminBundle for administering
5. VichUploaderBundle for uploads
6. StofDoctrineExtensionsBundle for timestamps
7. KnpPaginatorBundle for automate pagination

## Bundle features



## Installation

Installation is a quick 4 step process:

1. Add PortfolioBundle in your composer.json
2. Enable the Bundle
3. Import PortfolioBundle routing
4. Configure a pagination
5. Update your database schema

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
        new Stfalcon\Bundle\PortfolioBundle\StfalconPortfolioBundle(),
    );
}
```

### Step 3: Import PortfolioBundle routing

Now that you have installed and activated the bundle, all that is left to do is
to set the StfalconPortfolioBundle and SonataAdminBundle routings.

In YAML:

``` yaml
# app/config/routing.yml
_stfalcon_portfolio:
    resource: "@StfalconPortfolioBundle/Resources/config/routing.yml"
```
[Routing in SonataAdminBundle](https://github.com/sonata-project/SonataAdminBundle/blob/master/Resources/doc/reference/getting_started.rst#step-1-define-sonataadminbundle-routes)



Add following lines to your config file:

In YAML:

``` yaml
# app/config/config.yml
sonata_block:
    default_contexts: [cms]
    blocks:
        sonata.admin.block.admin_list:
            contexts:   [admin]

vich_uploader:
    db_driver: orm
    mappings:
        project_image:
            upload_dir: %kernel.root_dir%/../web/uploads/portfolio/projects
            namer: stfalcon_portfolio.namer.project
```

### Step 4: Update your database schema

Now that the bundle is configured, the last thing you need to do is update your
database schema because you have added a two new entities, the `Project` and the `Category`.

Run the following command.

``` bash
$ php app/console doctrine:schema:update --force
```
Now that you have completed the installation and configuration of the PortfolioBundle!