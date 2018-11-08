<?php

namespace OpenKudo\Domain\Model\Kudo\Handler;

use OpenKudo\Domain\Model\Kudo\Command\PostThankYou;
use OpenKudo\Domain\Model\Kudo\ThankYou;
use OpenKudo\Domain\Model\Kudo\ThankYouList;

final class PostThankYouHandler
{
    /**
     * @var ThankYouList
     */
    private $thankYouList;

    /**
     * PostHankYouHandler constructor.
     *
     * @param ThankYouList $thankYouList
     */
    public function __construct(ThankYouList $thankYouList)
    {
        $this->thankYouList = $thankYouList;
    }

    public function __invoke(PostThankYou $command)
    {
        $thankYou = ThankYou::post(
            $command->giverId(),
            $command->receiversId(),
            $command->reason(),
            $command->amount(),
            $command->thankYouId()
        );

        $this->thankYouList->save($thankYou);
    }
}
