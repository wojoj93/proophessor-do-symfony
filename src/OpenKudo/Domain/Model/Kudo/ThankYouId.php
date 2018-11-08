<?php

namespace OpenKudo\Domain\Model\Kudo;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class ThankYouId
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @return self
     */
    public static function generate()
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @param $id
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function fromString($id)
    {
        return new self(Uuid::fromString($id));
    }

    public function __construct($id)
    {
        $this->uuid = $id;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->uuid->toString();
    }
}
