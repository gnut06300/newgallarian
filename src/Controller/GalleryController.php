<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Picture;
use App\Form\GalleryType;
use App\Form\PictureType;
use App\Repository\GalleryRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function show(Gallery $gallery, Request $request, SluggerInterface $slugger,string $directoryUpload): Response
    {
        $form = $this->createForm(PictureType::class);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $picture = new Picture();
            $file = $request->files->get('picture')['image']; //UpladedFile (le fichier uploadé)
            //dump($request->files->get('picture')['image']);die;
            
            $originalName = $file->getClientOriginalName();// nom du fichier complet avec son extention
            $fileName = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);// nom du fichier sans extention
            
            $slugName = $slugger->slug($fileName)->lower();
            $directory = $directoryUpload;
            $picture->setName($originalName);
            $picture->setSlug($slugName);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture);
            $entityManager->flush();
            try {
                $file->move($directory,$originalName);
               
            }
            catch (Exception $e) { 
                dd($e->getMessage());
            }
            return $this->redirectToRoute('gallery_show', ['slug'=>$gallery->getSlug()]);
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
