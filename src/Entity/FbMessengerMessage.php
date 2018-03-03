<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FbMessengerMessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class FbMessengerMessage
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
    private $senderId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="bigint")
     */
    private $recipientId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        if (null === $this->getCreated()) {
            $this->created = new \DateTime('now');
        }
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
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param mixed $senderId
     * @return FbMessengerMessage
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     * @param mixed $recipientId
     * @return FbMessengerMessage
     */
    public function setRecipientId($recipientId)
    {
        $this->recipientId = $recipientId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return FbMessengerMessage
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return FbMessengerMessage
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return FbMessengerMessage
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return FbMessengerMessage
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }





}
