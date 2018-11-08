<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\PostThankYouType;
use Assert\InvalidArgumentException;
use FOS\RestBundle\Controller\FOSRestController;
use OpenKudo\Domain\Model\Kudo\Command\PostThankYou;
use OpenKudo\Domain\Model\Kudo\ThankYouId;
use OpenKudo\Domain\Projection\Table;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThankYouController extends FOSRestController
{
    /**
     * @Route(path="/api/thank-you", methods={"POST"})
     */
    public function postThankYou(Request $request)
    {
        $thankYouId = ThankYouId::generate();
        $form = $this->get('form.factory')->createNamed('', PostThankYouType::class, null, ['csrf_protection' => false]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $data['thank_you_id'] = $thankYouId->toString();

            $command = new PostThankYou($data);

            try {
                $this->get('prooph_service_bus.kudo_command_bus')->dispatch($command);

                return $this->json([], Response::HTTP_ACCEPTED);
            } catch (CommandDispatchException $ex) {
                return $this->json(['message' => $ex->getPrevious()->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $form;
    }

    /**
     * @Route(path="/api/thank-you", methods={"GET"})
     */
    public function listThankYou()
    {
        $connection = $this->get('doctrine.dbal.default_connection');

        return $this->json($connection->executeQuery('SELECT t1.*, t2.receiver_id FROM '.Table::THANK_YOU.' t1 LEFT JOIN '.Table::THANK_YOU_RECEIVER.' t2 ON t1.id = t2.thank_you_id')->fetchAll());
    }
}
