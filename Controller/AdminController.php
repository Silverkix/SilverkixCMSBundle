<?php

namespace Silverkix\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class AdminController extends Controller
{
    /* Render the index of the admin panel */
    public function indexAction()
    {
        return $this->render('SilverkixCMSBundle:Admin:index.html.twig');
    }

    /************************************************
    *  User Management
    *************************************************/

    /* Render the user management index */
    public function userIndexAction()
    {
        return $this->render('SilverkixCMSBundle:Admin:Users/index.html.twig');
    }


    /************************************************
    *  Utilities
    *************************************************/

    /* Perform the login for the admin panel */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'SilverkixCMSBundle:Admin:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }

}
