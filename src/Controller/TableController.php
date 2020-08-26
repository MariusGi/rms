<?php

namespace App\Controller;

use App\Entity\Table;
use App\Form\TableType;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TableController extends AbstractController
{
    /**
     * @Route("/restaurant/{id}/tables", name="tables", methods={"GET"})
     * @param TableRepository $tableRepository
     * @param $id
     * @return Response
     */
    public function index(TableRepository $tableRepository, $id): Response
    {
        return $this->render('table/index.html.twig', [
            'tables' => $tableRepository->findBy(
                ['restaurant' => $id]
            ),
            'id' => $id,
        ]);
    }

    /**
     * @Route("/restaurant/{id}/table/new", name="table_new", methods={"GET","POST"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function new(Request $request, $id): Response
    {
        $table = new Table();
        $form = $this->createForm(TableType::class, $table, [
            'restaurant_id' => $id,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($table);
            $entityManager->flush();

            return $this->redirectToRoute('tables', [
                'id' => $id,
            ]);
        }

        return $this->render('table/new.html.twig', [
            'table' => $table,
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    /**
     * @Route("/table/{id}", name="table_show", methods={"GET"})
     * @param Table $table
     * @param $id
     * @return Response
     */
    public function show(Table $table, $id): Response
    {
        return $this->render('table/show.html.twig', [
            'table' => $table,
            'id' => $id,
        ]);
    }

    /**
     * @Route("/table/{id}/edit", name="table_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Table $table
     * @return Response
     */
    public function edit(Request $request, Table $table): Response
    {
        $form = $this->createForm(TableType::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tables');
        }

        return $this->render('table/edit.html.twig', [
            'table' => $table,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/table/{id}", name="table_delete", methods={"DELETE"})
     * @param Request $request
     * @param Table $table
     * @return Response
     */
    public function delete(Request $request, Table $table): Response
    {
        if ($this->isCsrfTokenValid('delete'.$table->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($table);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tables');
    }
}
