<?php

namespace AppBundle\Command;

use OpenKudo\Domain\Model\Kudo\ThankYou;
use OpenKudo\Domain\Model\Kudo\ThankYouId;
use OpenKudo\Domain\Model\Person\PersonId;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostThankYouCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('thank-you:give');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $giverid = PersonId::generate();
        $receiversIds = [
            PersonId::generate()
        ];
        $reason = 'Just for being';
        $amount = 10;
        $thankYouId = ThankYouId::generate();
        $thankYou = ThankYou::post($giverid, $receiversIds, $reason, $amount, $thankYouId);

        var_dump($thankYou);
    }
}
