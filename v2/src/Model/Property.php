<?php


namespace Biltorvet\Model;

class Property
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @var string|null
     */
    private $valueFormatted;

    /**
     * @var string|null
     */
    private $publicName;

    /**
     * @var boolean|null
     */
    private $isPublic;

    /**
     * @var string|null
     */
    private $group;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param  string|null $id
     * @return Property
     */
    public function setId(?string $id): Property
    {
        $this->id = $id;
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
     * @return Property
     */
    public function setValue(?string $value): Property
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getValueFormatted(): ?string
    {
        return $this->valueFormatted;
    }

    /**
     * @param  string|null $valueFormatted
     * @return Property
     */
    public function setValueFormatted(?string $valueFormatted): Property
    {
        $this->valueFormatted = $valueFormatted;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPublicName(): ?string
    {
        return $this->publicName;
    }

    /**
     * @param  string|null $publicName
     * @return Property
     */
    public function setPublicName(?string $publicName): Property
    {
        $this->publicName = $publicName;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    /**
     * @param  bool|null $isPublic
     * @return Property
     */
    public function setIsPublic(?bool $isPublic): Property
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * @param  string|null $group
     * @return Property
     */
    public function setGroup(?string $group): Property
    {
        $this->group = $group;
        return $this;
    }
}
