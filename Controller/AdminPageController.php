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

        $entities = $em->getRepository('SilverkixCMSBundle:Page')->findByParent(null);

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

            if($entity->getHome())
            {
                $page = $em->getRepository("SilverkixCMSBundle:Page")->findOneByHome(array("home"=>1));
                $page->setHome(false);
                $em->flush();
            }

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

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PageType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            if($entity->getHome())
            {
                $page = $em->getRepository("SilverkixCMSBundle:Page")->findOneBy(array("home" =>1));

                $page->setHome(false);
                $em->flush();
            }

            if($entity->getParent() !== null)
            {
                $entity->setTitle($entity->getTitle());
                $entity->setSlug($entity->getParent()->getSlug()."/".$entity->getSlug());
            }
            else
            {
                // Make sure we overwrite the slug if page is no longer set as child
                // Due to doctrine, this will not be updated since the title field may not be touched
                $entity->setTitle($entity->getTitle());
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_page'));
        }

        return $this->render('SilverkixCMSBundle:Admin:Page/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Page entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SilverkixCMSBundle:Page')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Page entity.');
            }

            $em->remove($entity);
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
