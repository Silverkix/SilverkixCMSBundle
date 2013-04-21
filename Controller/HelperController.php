<?php

namespace Silverkix\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelperController extends Controller
{
    public function navigationAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('SilverkixCMSBundle:Page')->findBy(array("parent"=>null));

        return $this->render('SilverkixCMSBundle:Site:Snippets/navigation.html.twig', array(
            'pages'      => $pages,
        ));
    }
}
