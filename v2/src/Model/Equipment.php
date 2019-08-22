<?php


namespace Biltorvet\Model;

class Equipment
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
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param  string|null $id
     * @return Equipment
     */
    public function setId(?string $id): Equipment
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
     * @return Equipment
     */
    public function setValue(?string $value): Equipment
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
     * @return Equipment
     */
    public function setValueFormatted(?string $valueFormatted): Equipment
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
     * @return Equipment
     */
    public function setPublicName(?string $publicName): Equipment
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
     * @return Equipment
     */
    public function setIsPublic(?bool $isPublic): Equipment
    {
        $this->isPublic = $isPublic;
        return $this;
    }
}
