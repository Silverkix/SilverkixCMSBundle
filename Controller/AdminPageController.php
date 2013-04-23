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

            $siblings = $em->getRepository("SilverkixCMSBundle:Page")->findByParent($entity->getParent(), array("orderid"=>"asc"));
            $next = count($siblings) > 0 ? $siblings[ count($siblings) - 1 ]->getOrderid() + 1 : 1;
            $entity->setOrderid($next);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_page'));
        }

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
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SilverkixCMSBundle:Admin:Page/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Page entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SilverkixCMSBundle:Page')->find($id);
        // Get the old parent before it is overwritten by the form
        $parent = $entity->getParent();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PageType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            // Figure out the new orderid
            if($parent !== $entity->getParent())
            {
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
                $em->persist($entity);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('admin_page'));
        }

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

        $page->setOrderid($page->getOrderid() - 1);
        $otherPage = $em->getRepository("SilverkixCMSBundle:Page")->findOneBy(
            array(
                "parent" => $page->getParent(),
                "orderid" => $page->getOrderid()
                )
            );
        $otherPage->setOrderid($otherPage->getOrderid() + 1);
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

        $page->setOrderid($page->getOrderid() + 1);


        $otherPage = $em->getRepository("SilverkixCMSBundle:Page")->findOneBy(
            array(
                "parent" => $page->getParent(),
                "orderid" => $page->getOrderid()
                )
            );

        $otherPage->setOrderid($otherPage->getOrderid() - 1);

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
        $parent = $entity->getParent();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $children = $em->getRepository('SilverkixCMSBundle:Page')->findByParent($entity);

        if(count($children) > 0)
        {
            foreach($children as $child)
            {
                $em->remove($child);
            }
        }

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

        return $this->redirect($this->generateUrl('admin_page'));
    }

    /**
     * Creates a form to delete a Page entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
