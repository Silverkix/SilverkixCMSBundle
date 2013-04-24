<?php

namespace Silverkix\CMSBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Silverkix\CMSBundle\Entity\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Create new user
        $userAdmin = new User();

        // Create encoder and hashed password
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($userAdmin);
        $password = $encoder->encodePassword('mypass', $userAdmin->getSalt());

        // Set user data
        $userAdmin->setUsername('admin');
        $userAdmin->setPassword($password);

        $userAdmin->setEmail("me@me.com");
        $userAdmin->setIsActive(true);

        // Save to db
        $manager->persist($userAdmin);
        $manager->flush();
    }
}
