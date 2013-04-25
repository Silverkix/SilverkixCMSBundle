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
        $homePage = new Page();

        $homePage->setTitle("Home");
        $homePage->setSlug('');
        $homePage->setKeywords("cms, silverkix, new");
        $homePage->setDescription("Welcome to your brand new CMS-enabled page.");
        $homePage->setContent('<p>Hi there, thanks for using the <a href="https://github.com/Silverkix/SilverkixCMSBundle" target="_blank">SilverkixCMSBundle</a>! It seems you have set everything up correctly and are all ready to go from here. All that&#39;s left to do is overwrite the front-end template and create a new admin user.</p>');
        $homePage->setOrderid(1);
        $homePage->setOnline(true);

        $manager->persist($homePage);

        // Create a second page
        $page = new Page();
        $page->setTitle("About us");
        $page->setSlug("about-us");
        $page->setKeywords("about, us");
        $page->setDescription("Some content telling more about the owner of the website.");
        $page->setContent('<p>Hi there, thanks for using the <a href="https://github.com/Silverkix/SilverkixCMSBundle" target="_blank">SilverkixCMSBundle</a>! It seems you have set everything up correctly and are all ready to go from here. All that&#39;s left to do is overwrite the front-end template and create a new admin user.</p>');
        $page->setOnline(true);
        $page->setOrderid(2);

        $manager->persist($page);

        // Create a child page
        $childPage = new Page();
        $childPage->setTitle("Our history");
        $childPage->setSlug("our-history");
        $childPage->setKeywords("our, history");
        $childPage->setDescription("Some content telling more about the owner of the website.");
        $childPage->setContent('<p>Hi there, thanks for using the <a href="https://github.com/Silverkix/SilverkixCMSBundle" target="_blank">SilverkixCMSBundle</a>! It seems you have set everything up correctly and are all ready to go from here. All that&#39;s left to do is overwrite the front-end template and create a new admin user.</p>');
        $childPage->setOnline(true);
        $childPage->setOrderid(1);
        $childPage->setParent($page);

        $page->addChildren($childPage);
        $manager->persist($childPage);

        // Create another child page
        $childPage2 = new Page();
        $childPage2->setTitle("Our Future");
        $childPage2->setSlug("our-future");
        $childPage2->setKeywords("our, future");
        $childPage2->setDescription("Some content telling more about the owner of the website.");
        $childPage2->setContent('<p>Hi there, thanks for using the <a href="https://github.com/Silverkix/SilverkixCMSBundle" target="_blank">SilverkixCMSBundle</a>! It seems you have set everything up correctly and are all ready to go from here. All that&#39;s left to do is overwrite the front-end template and create a new admin user.</p>');
        $childPage2->setOnline(true);
        $childPage2->setOrderid(2);
        $childPage2->setParent($page);

        $page->addChildren($childPage2);
        $manager->persist($childPage2);

        // Create a second page
        $page2 = new Page();
        $page2->setTitle("Contact");
        $page2->setSlug("contact");
        $page2->setKeywords("contact, us");
        $page2->setDescription("Some content telling more about the owner of the website.");
        $page2->setContent('<p>Hi there, thanks for using the <a href="https://github.com/Silverkix/SilverkixCMSBundle" target="_blank">SilverkixCMSBundle</a>! It seems you have set everything up correctly and are all ready to go from here. All that&#39;s left to do is overwrite the front-end template and create a new admin user.</p>');
        $page2->setOnline(true);
        $page2->setOrderid(3);

        $manager->persist($page2);

        // Save all to database
        $manager->flush();
    }
}
