<?php

namespace Silverkix\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('SilverkixCMSBundle:Admin:index.html.twig');
    }

}
