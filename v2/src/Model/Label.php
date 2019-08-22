<?php

namespace Biltorvet\Model;

class Label
{
    /**
     * @var integer|null
     */
    private $key;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @return int|null
     */
    public function getKey(): ?int
    {
        return $this->key;
    }

    /**
     * @param  int|null $key
     * @return Label
     */
    public function setKey(?int $key): Label
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param  string|null $value
     * @return Label
     */
    public function setValue(?string $value): Label
    {
        $this->value = $value;
        return $this;
    }
}
