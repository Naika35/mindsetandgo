<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category", name="admin_category_browse")
     */
    public function browse(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/category/browse.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/admin/category-add", name="admin_category_add")
     */
    public function add(Request $request, ImageUploader $imageUploader, EntityManagerInterface $em)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            
            $pictureName = $imageUploader->upload($form, 'picture');
            $category->setPicture($pictureName);
            
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été rajoutée');

            return $this->redirectToRoute('admin_category_browse');
        }

        return $this->render('admin/category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("image/{id}", name="ulpoad", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function imageUpload(Request $request, Category $category, FileUploader $fileUploader)
    {
        $form = $this->createFormBuilder(null, ['csrf_protection' => false])
            ->add('imageFile', FileType::class, [
                'constraints' => new Image([
                    'maxSize' => '1k',
                ])
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form->get('imageFile')->getData();

            if ($uploadedFile) {
                $pictureName = $fileUploader->upload($uploadedFile);
                $category->setPicture($pictureName);
            }

            return new Response('form submitted');
        }

        return new Response('form not submitted');
    }

    /**
     * @Route("/admin/category-edit/{id}", name="admin_category_edit", requirements={"id"="\d+"}))
     */
    public function edit(Request $request, Category $category, ImageUploader $imageUploader, EntityManagerInterface $em)
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureName = $imageUploader->upload($form, 'picture', $category->getPicture());
            $category->setPicture($pictureName);

            $em->flush();

            $this->addFlash('success', 'La catégorie a bien été modifiée');

            return $this->redirectToRoute('admin_category_browse');
        }
        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/category-delete/{id}", name="admin_category_delete")
     */
    public function delete(Category $category, EntityManagerInterface $em)
    {
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'La catégorie a bien été supprimée.');

        return $this->redirectToRoute('admin_category_browse');
    }
}
