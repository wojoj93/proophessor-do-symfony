<?php
declare(strict_types = 1);

namespace AppBundle\Controller;

use Prooph\ServiceBus\Exception\CommandDispatchException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

final class ApiCommandController extends Controller
{
    const NAME_ATTRIBUTE = 'command_name';

    /**
     * @Route(path="/api/command/post-thank-you", defaults={"command_name":"\OpenKudo\Domain\Model\Kudo\Command\PostThankYou"})
     */
    public function postAction(Request $request)
    {
        $commandName = $request->attributes->get(self::NAME_ATTRIBUTE);

        if (null === $commandName) {
            return JsonResponse::create(
                [
                    'message' => sprintf(
                        'Command name attribute ("%s") was not found in request.',
                        self::NAME_ATTRIBUTE
                    )
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        try {
            $payload = $this->getPayloadFromRequest($request);
        } catch (\Throwable $error) {
            return JsonResponse::create(
                [
                    'message' => $error->getMessage()
                ],
                $error->getCode()
            );
        }

        $command = $this->get('prooph_service_bus.message_factory')->createMessageFromArray($commandName, ['payload' => $payload]);

        try {
            $this->get('prooph_service_bus.kudo_command_bus')->dispatch($command);
        } catch (CommandDispatchException $ex) {
            $params = $ex->getFailedDispatchEvent()->getParams();

            return JsonResponse::create(
                ['message' => $ex->getPrevious()->getMessage(), 'dispatch_details' => $params],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Throwable $error) {
            return JsonResponse::create(['message' => $error->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return JsonResponse::create(null, Response::HTTP_ACCEPTED);
    }

    private function getPayloadFromRequest(Request $request): array
    {
        $payload = json_decode($request->getContent(), true);

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new \Exception('Invalid JSON, maximum stack depth exceeded.', 400);
            case JSON_ERROR_UTF8:
                throw new \Exception('Malformed UTF-8 characters, possibly incorrectly encoded.', 400);
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_STATE_MISMATCH:
                throw new \Exception('Invalid JSON.', 400);
        }

        return $payload === null ? [] : $payload;
    }
}
