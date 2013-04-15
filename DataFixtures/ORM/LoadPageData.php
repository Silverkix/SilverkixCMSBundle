<?php

namespace Silverkix\CMSBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Silverkix\CMSBundle\Entity\Page;

class LoadPageData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Create a Parent page
        $page = new Page();

        $page->setTitle("Testpagina");
        $page->setKeywords("Some, weird, keywords");
        $page->setDescription("A really useful description of this awesome page");
        $page->setContent("<h2>Subheading</h2><p>And a paragraphe</p>");
        $page->setOnline(true);

        $manager->persist($page);

        $page2 = new Page();

        // Create a child page
        $page2->setTitle("Nog een pagina");
        $page2->setKeywords("more, useless keywords");
        $page2->setDescription("This becomes a description");
        $page2->setContent("<h2>Subheading</h2><p>And a paragraphe</p>");
        $page2->setOnline(true);
        $page2->setParent($page);

        $page->addChildren($page2);

        $manager->persist($page2);

        // Save all to database
        $manager->flush();
    }
}
