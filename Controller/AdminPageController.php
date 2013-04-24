<?php

namespace Silverkix\CMSBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Silverkix\CMSBundle\Entity\Page;
use Silverkix\CMSBundle\Form\PageType;

/**
 * Page controller.
 *
 */
class AdminPageController extends Controller
{
    /**
     * Lists all Page entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SilverkixCMSBundle:Page')->findBy(array("parent"=>null),array("orderid"=>"asc"));

        return $this->render('SilverkixCMSBundle:Admin:Page/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Page entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Page();
        $form = $this->createForm(new PageType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Get siblings and determ which is the next orderid
            $siblings = $em->getRepository("SilverkixCMSBundle:Page")->findByParent($entity->getParent(), array("orderid"=>"asc"));
            $next = count($siblings) > 0 ? $siblings[ count($siblings) - 1 ]->getOrderid() + 1 : 1;
            $entity->setOrderid($next);

            // Welcome to the family!
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_page'));
        }

        // Render the new page form
        return $this->render('SilverkixCMSBundle:Admin:Page/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Page entity.
     *
     */
    public function newAction()
    {
        $entity = new Page();
        $form   = $this->createForm(new PageType(), $entity);

        return $this->render('SilverkixCMSBundle:Admin:Page/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilverkixCMSBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $editForm = $this->createForm(new PageType(), $entity);

        return $this->render('SilverkixCMSBundle:Admin:Page/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
     * Edits an existing Page entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        // Get the page being edited
        $entity = $em->getRepository('SilverkixCMSBundle:Page')->find($id);

        // Get the old parent before it is overwritten by the form
        $parent = $entity->getParent();

        // Check if we found the page being edited
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        // Create edit form
        $editForm = $this->createForm(new PageType(), $entity);
        $editForm->bind($request);

        // Check if the form is valid
        if ($editForm->isValid()) {

            if($parent !== $entity->getParent())
            {
                // it seems we have been adopted by new parents
                // Lets say hi to our new siblings and goodbye to our old siblings
                $siblings = $em->getRepository("SilverkixCMSBundle:Page")->findByParent($entity->getParent(), array("orderid"=>"asc"));
                $next = count($siblings) > 0 ? $siblings[ count($siblings) - 1 ]->getOrderid() + 1 : 1;
                $entity->setOrderid( $next );

                $em->persist($entity);
                $em->flush();

                // Update old siblings
                $oldSiblings = $em->getRepository("SilverkixCMSBundle:Page")->findByParent($parent, array("orderid"=>"asc"));
                if($oldSiblings)
                {
                    for($i = 0; $i < count($oldSiblings); $i++)
                    {
                        $oldSiblings[$i]->setOrderid( $i + 1);
                        $em->persist($oldSiblings[$i]);
                    }
                    $em->flush();
                }
            }
            else
            {
                // We still have the same parents so we can just save everything
                $em->persist($entity);
                $em->flush();
            }

            // Return to the admin page
            return $this->redirect($this->generateUrl('admin_page'));
        }

        // Render the edit form
        return $this->render('SilverkixCMSBundle:Admin:Page/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Move a page up in a group
     */
    public function moveUpAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository("SilverkixCMSBundle:Page")->find($id);

        // Set the new order id
        $page->setOrderid($page->getOrderid() - 1);

        // Get the sibling to switch with
        $otherPage = $em->getRepository("SilverkixCMSBundle:Page")->findOneBy(
            array(
                "parent" => $page->getParent(),
                "orderid" => $page->getOrderid()
                )
            );
        $otherPage->setOrderid($otherPage->getOrderid() + 1);

        // Save to db
        $em->flush();

        return $this->redirect($this->generateUrl('admin_page'));
    }

    /**
     * Move a page down in a group
     */
    public function moveDownAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository("SilverkixCMSBundle:Page")->find($id);

        // Set the new order id
        $page->setOrderid($page->getOrderid() + 1);

        // Find the sibling to switch with
        $otherPage = $em->getRepository("SilverkixCMSBundle:Page")->findOneBy(
            array(
                "parent" => $page->getParent(),
                "orderid" => $page->getOrderid()
                )
            );
        $otherPage->setOrderid($otherPage->getOrderid() - 1);

        // Save to database
        $em->flush();

        return $this->redirect($this->generateUrl('admin_page'));
    }

    /**
     * Deletes a Page entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SilverkixCMSBundle:Page')->find($id);

        // Save the parent to update the siblings later
        $parent = $entity->getParent();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        // Find all siblings to say goodby
        $children = $em->getRepository('SilverkixCMSBundle:Page')->findByParent($entity);

        // Remove all children
        if(count($children) > 0)
        {
            foreach($children as $child)
            {
                $em->remove($child);
            }
        }

        // Remove parents
        $em->remove($entity);
        $em->flush();

        // Update old siblings
        $oldSiblings = $em->getRepository("SilverkixCMSBundle:Page")->findByParent($parent, array("orderid"=>"asc"));
        if($oldSiblings)
        {
            for($i = 0; $i < count($oldSiblings); $i++)
            {
                $oldSiblings[$i]->setOrderid( $i + 1);
                $em->persist($oldSiblings[$i]);
            }
            $em->flush();
        }

        // Return to admin page
        return $this->redirect($this->generateUrl('admin_page'));
    }
}
