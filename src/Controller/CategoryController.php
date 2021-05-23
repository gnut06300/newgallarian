<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use dateTime;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        //$categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        //dd($categories);

        return $this->render('category/index.html.twig', [
            'categories'=>$categoryRepository->findBy(['isActived'=>true],['name'=>'ASC']), //'categories'=>$categoryRepository->findBy(['isActived'=>true],['name'=>'ASC'],4,0),
            //'categories' => $categories,
            //'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()
        ]);
    }
    /**
     * @Route("/categories/new", name="categories_new", methods={"GET","POST"})
     */
    public function newCategory(Request $request,EntityManagerInterface $em,SluggerInterface $slugger, TranslatorInterface $translator): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //methode 3 en passant l'objet dans createForm pour qu'il hydrate l'objet Category
            //dd($category);

            //methode 2 via request
            //dd($request->request->get('category_form')['name']);
            //$name=$request->request->get('category_form')['name'];
            //$content=$request->request->get('category_form')['content'];
            //dd($request->server->get('HTTP_HOST'));

            //methode 1 via formType
            //$name=$form->getData()->getName();
            //$content=$form->getData()->getContent();
            //$category->setName($name);
            //$category->setContent($content);
            //$em= $this->getDoctrine()->getManager(); //sans EntityManagerInterface $em uniquement dans un controller extends AbstractController
            $category->setSlug($slugger->slug($category->getName()."-".uniqid())->lower());//lower en minicule
            $em->persist($category);
            $em->flush();
            //dd($name,$content);

            //dd($form->getData()->getName());        
            //dd($form->getData(),$request->request,$category);
            $this->addFlash('success', $translator->trans("La catégorie a été crée", [] ,"security"));
            return $this->redirectToRoute('categories');

        }
        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        
        ]);
    }


    /**
     * @Route("/categories/update/{id}", name="categories_update", methods={"GET","PATCH"})
     */
    public function update(Category $category, EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(CategoryFormType::class, $category,["edit"=>true]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //dd($request->request->get('category_form'));
            //dd($category);
            if (!isset($request->request->get('category_form')['isActived'])) {
               $category->setIsActived(false);
                # code...
            }
            
            $category->setUpdateAt(new DateTime());
            //persist flush
            $em->persist($category);//Préparer
            $em->flush();//Envoyer en bdd

            return $this->redirectToRoute('categories');

        }
    
        Return $this->render('category/update.html.twig',
            ['updateForm' => $form->createView()]
        );

    }

    /**
     * @Route("/categories/delete/{id}", name="delete_categorie", methods={"DELETE"})
     */
    public function delete(Category $category, EntityManagerInterface $entityManagerInterface)
    {
        //supprimer la catégorie récupérée
        $entityManagerInterface->remove($category);

        //On demande à doctrine d'établir les changements
        $entityManagerInterface->flush();

        return $this->redirectToRoute('categories');
    }
    
}
