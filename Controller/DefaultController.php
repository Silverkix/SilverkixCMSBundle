<?php

namespace Silverkix\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * handle normal page requests
     */
    public function indexAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository('SilverkixCMSBundle:Page')->findOneBySlug($slug);

        return $this->render('SilverkixCMSBundle:Site:Page/normal.html.twig', array(
            'page'      => $page,
        ));
    }
}
