# Welcome to the Silverkix CMSBundle for Symfony
This bundle will help you kickstart any project requiring a simple CMS. This will mainly be for smaller websites which will be managed by the client himself and not updated very often.

This cms includes the following features:

* Pages (include Seo data)
* Blog (including categories and tags)
* User management

## installation

###[[TODO]] 1) Using composer

### 2) Past to `security.yml`
    security:
        encoders:
            Silverkix\CMSBundle\Entity\User:
                algorithm:        sha512
                encode_as_base64: false
                iterations:       1

        role_hierarchy:
            ROLE_ADMIN:       ROLE_ADMIN
            ROLE_SUPER_ADMIN: [ ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH ]

        providers:
            administrators:
                entity: { class: SilverkixCMSBundle:User, property: username }

        firewalls:
            admin_area:
                pattern:    ^/
                anonymous: ~
                form_login:
                    login_path:  admin_login
                    check_path:  admin_login_check
                    default_target_path: admin_index
                    always_use_default_target_path: true
                logout:
                    path: /logout
                    target: /

        access_control:
            - { path: ^/admin, roles: ROLE_ADMIN }

### 3) Update database
Run `php app/console doctrine:schema:update` to update the database

### 4) Load fixtures
add the fixtures bundle to your kernel:

`new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),`

### 5) Extend your bundle
In order to overwrite the fontend template you need to extend out bundle so you can overwrite any necessary stuffz.

    <?php
    // src/Acme/DemoBundle/AcmeDemoBundle.php

    namespace Acme\DemoBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AcmeDemoBundle extends Bundle
    {
        public function getParent()
        {
            return 'SilverkixCMSBundle';
        }
    }

### 6) Create public uploads folder
`[PUBLIC_HTML]/uploads` (chmod 777)
