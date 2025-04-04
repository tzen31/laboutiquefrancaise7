<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use App\Repository\OrderRepository;

class InvoiceController extends AbstractController
{
    /*
     * IMPRESSION FACTURE PDF pour un utilisateur connecté
     * Vérification de la commande par un utilisateur donné
     */        
    #[Route('/compte/facture/impression/{id_order}', name: 'app_invoice_customer')]
    public function printForCustomer(OrderRepository $orderRepository, $id_order): Response
    {  
        // 1. Vérification sde l'objet commande - Existe ?
        $order = $orderRepository->findOneById($id_order);

        if (!$order) {
            return $this->redirectToRoute('app_account');
        }
         
        // 2. Vérification de l'objet commande - Ok pour l'utilisateur
        if ($order->getUser() != $this->getUser() ) {
            return $this->redirectToRoute('app_account');
        }

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $html = $this->renderView('/invoice/index.html.twig', [
            'order' => $order
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('facture.pdf', [
            'Attachment' => false
        ]);

        exit();
    }

    /*
     * IMPRESSION FACTURE PDF pour un administrateur
     * Vérification de la commande par un utilisateur donné
     */        
    #[Route('/admin/facture/impression/{id_order}', name: 'app_invoice_admin')]
    public function printForAdmin(OrderRepository $orderRepository, $id_order): Response
    {  
        // Vérification de l'objet commande - Existe ?
        $order = $orderRepository->findOneById($id_order);

        if (!$order) {
            return $this->redirectToRoute('admin');
        }       

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $html = $this->renderView('/invoice/index.html.twig', [
            'order' => $order
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('facture.pdf', [
            'Attachment' => false
        ]);

        exit();
    }
}
