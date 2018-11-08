<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\RegisterPersonType;
use Assert\InvalidArgumentException;
use FOS\RestBundle\Controller\FOSRestController;
use OpenKudo\Domain\Model\Person\Command\RegisterPerson;
use OpenKudo\Domain\Model\Person\PersonId;
use OpenKudo\Domain\Projection\Table;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends FOSRestController
{
    /**
     * @Route(path="/api/person", methods={"POST"})
     */
    public function registerPerson(Request $request)
    {
        $personId = PersonId::generate();
        $form = $this->get('form.factory')->createNamed('', RegisterPersonType::class, null, ['csrf_protection' => false]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $data['person_id'] = $personId->toString();

            $command = new RegisterPerson($data);

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
     * @Route(path="/api/person", methods={"GET"})
     */
    public function listPerson()
    {
        $connection = $this->get('doctrine.dbal.default_connection');

        return $this->json($connection->executeQuery('SELECT t1.* FROM '.Table::PERSON.' t1')->fetchAll());
    }
}
