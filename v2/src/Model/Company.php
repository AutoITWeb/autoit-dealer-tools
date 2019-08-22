<?php


namespace Biltorvet\Model;

class Company
{
    /**
     * @var integer|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var integer|null
     */
    private $postNumber;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $phone;

    /**
     * @var float[]|null
     */
    private $coordinates;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param  int|null $id
     * @return Company
     */
    public function setId(?int $id): Company
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param  string|null $name
     * @return Company
     */
    public function setName(?string $name): Company
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param  string|null $address
     * @return Company
     */
    public function setAddress(?string $address): Company
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param  string|null $city
     * @return Company
     */
    public function setCity(?string $city): Company
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPostNumber(): ?int
    {
        return $this->postNumber;
    }

    /**
     * @param  int|null $postNumber
     * @return Company
     */
    public function setPostNumber(?int $postNumber): Company
    {
        $this->postNumber = $postNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param  string|null $email
     * @return Company
     */
    public function setEmail(?string $email): Company
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param  string|null $phone
     * @return Company
     */
    public function setPhone(?string $phone): Company
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return float[]|null
     */
    public function getCoordinates(): ?array
    {
        return $this->coordinates;
    }

    /**
     * @param  float[]|null $coordinates
     * @return Company
     */
    public function setCoordinates(?array $coordinates): Company
    {
        $this->coordinates = $coordinates;
        return $this;
    }
}
