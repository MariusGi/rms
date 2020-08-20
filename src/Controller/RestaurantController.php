<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;

class RestaurantController extends AbstractController
{
    /**
     * @Route("/restaurants", name="restaurants", methods={"GET"})
     * @param Request $request
     * @param RestaurantRepository $restaurantRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, RestaurantRepository $restaurantRepository, PaginatorInterface $paginator): Response
    {
        if ($request->query->getAlnum('filter')) {
            $query = $restaurantRepository->findByLikeField($request->query->getAlnum('filter'));
        } else {
            $query = $restaurantRepository->findAll();
        }

        $result = $paginator->paginate($query);

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $result,
        ]);
    }

    /**
     * @Route("/restaurant/new", name="restaurant_new", methods={"GET","POST"})
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $restaurant->setPhoto($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('restaurants');
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("restaurant/{id}", name="restaurant_show", methods={"GET"})
     * @param Restaurant $restaurant
     * @return Response
     */
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @Route("restaurant/{id}/edit", name="restaurant_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Restaurant $restaurant
     * @return Response
     */
    public function edit(Request $request, Restaurant $restaurant): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('restaurants');
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurant_delete", methods={"DELETE"})
     * @param Request $request
     * @param Restaurant $restaurant
     * @return Response
     */
    public function delete(Request $request, Restaurant $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('restaurants');
    }
}
