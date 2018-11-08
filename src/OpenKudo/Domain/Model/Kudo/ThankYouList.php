<?php

namespace OpenKudo\Domain\Model\Kudo;

interface ThankYouList
{
    public function save(ThankYou $thankYou);

    public function get(ThankYouId $id);
}
