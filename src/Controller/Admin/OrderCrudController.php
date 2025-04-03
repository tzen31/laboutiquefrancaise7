<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Classe\State;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
//use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
//use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;  //Symfony 5
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator; //Symfony 6

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator; //$crudUrlGenerator (symfony 5)

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions {
        // $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        // $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');
        
        $show = Action::new('Afficher')->linkToCrudAction('show');

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            //->add(Crud::PAGE_INDEX, Action::DETAIL);
            ->add(Crud::PAGE_INDEX, $show);

            //->add('detail', $updatePreparation)
            //->add('detail', $updateDelivery)
            //->add('index', 'detail');
    }

    public function show(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, Request $request) {
        // $order = $context->getEntity()->getInstance(); //Bug easyAdmin 4.25 et symfony 7 mais fonctionne sur easyAdmin 4.1 sur symfony 6 !
        
        // // Récupérer l'URL de notre action "show"
        // $url = $adminUrlGenerator->setController(self::class)->setAction('show')->setEntityId($order->getId())->generateUrl();

        // // Traitement des changement de statut
        // if ($request->get('state')) {
        //     $this->changeState($order, $request->get('state'));
        // }

        // return $this->render('admin/order.html.twig', [
        //     'order' => $order,
        //     'current_url' => $url
        // ]);
    }

    /*
    * Fonction permettant le changement de statut de commande
    */
    public function changeState($order, $state) {
        //dd(State::STATE[$state]);

        //1. Modification du statut de la commande
        $order->setState($state);
        $this->entityManager->flush();

        //2. Affichage du Flash Message pour informer l'administrateur
        $this->addFlash('success','Statut de la commande correctement mis à jour');

        //3. Informer l'utilisateur du statut de sa commande
        $to = $order->getUser()->getEmail();
        $mail = new Mail();
        $vars = [
            'firstname' => $order->getUser()->getFirstName(),
            'id_order' => $order->getId()
        ];
        $content = 'Bonjour Thierry<br>J\'espère que vous allez bien.';
        $mail->send($to, $order->getUser()->getFirstName(). ' '.$order->getUser()->getLastName(), State::STATE[$state]['email_subject'], State::STATE[$state]['email_template'], $vars);

    }


    public function updatePreparation(AdminContext $context)
    {
        //die('ok');
        $order = $context->getEntity()->getInstance();
        //dd($order);
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green'><b>La commande <b>".$order->getReference()."</b>b> est bien <u>en cours de préparation</u>.</b></span>");

        //Symfony 6 (redirection)
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:orange'><b>La commande <b>".$order->getReference()."</b>b> est bien <u>en cours de livraison</u>.</b></span>");

        //Symfony 6 (redirection)
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud {   
        return $crud
                ->setEntityLabelInSingular('Commande')
                ->setEntityLabelInPlural('Commandes')
                ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt', 'Date'),
            DateTimeField::new('createdAt', 'Passée le'),
            NumberField::new('State')->setLabel('Statut')->setTemplatePath('admin/state.html.twig'),
            AssociationField::new('user'),
            //TextField::new('user.getFullName', 'Utilisateur'),
            TextField::new('carrierName', 'Transporteur'),
            TextEditorField::new('delivery','Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('totalWt', 'Total produit')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'frais de port')->setCurrency('EUR'),
            //BooleanField::new('isPaid', 'Payée'),
            ChoiceField::new('state')->setChoices([
                'Non payé' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }

}
