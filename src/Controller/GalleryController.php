<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Form\PictureType;
use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTime;

class GalleryController extends AbstractController
{
    /**
     * @Route("/", name="gallery_index", methods={"GET"})
    */
    public function index(GalleryRepository $galleryRepository): Response
    {
        return $this->render('gallery/index.html.twig', [
            'galleries' => $galleryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="gallery_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $gallery->setSlug($slugger->slug($gallery->getTitle()."-".uniqid())->lower());
            $entityManager->persist($gallery);
            $entityManager->flush();

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('gallery/new.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * requirements={"slug"="\d+"}
     * priority=-10 le mettre dans la route
     * @Route("/{slug}", name="gallery_show", methods={"GET", "POST"}, priority=-10)
    */
    public function show(Gallery $gallery, Request $request): Response
    {
        $form = $this->createForm(PictureType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $request->files->get('picture')['image']; //UpladedFile (le fichier uploadé)
            //dump($request->files->get('picture')['image']);die;

        }

        return $this->render('gallery/show.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="gallery_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Gallery $gallery, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gallery->setSlug($slugger->slug($gallery->getTitle()."-".uniqid())->lower());
            $gallery->setUpdatedAt(new DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('gallery/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gallery_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Gallery $gallery): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gallery->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($gallery);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gallery_index');
    }
}
