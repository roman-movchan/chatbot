<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FbMessengerUserRepository")
 */
class FbMessengerUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $messengerId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * FbMessengerUser constructor.
     * @param integer $messengerId
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($messengerId, $firstName, $lastName)
    {
        $this->messengerId = $messengerId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMessengerId()
    {
        return $this->messengerId;
    }

    /**
     * @param mixed $messengerId
     * @return FbMessengerUser
     */
    public function setMessengerId($messengerId)
    {
        $this->messengerId = $messengerId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return FbMessengerUser
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return FbMessengerUser
     */
    public function setSecondName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }



}
