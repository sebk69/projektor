<?php
/**
 * This file is a part of SmallUserBundle
 * Distributed under GNU GPL V3 licence
 * © 2015 - Sébastien Kus
 */

namespace Sebk\SmallUserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Sebk\SmallOrmBundle\Dao\Model;

class User extends Model implements UserInterface, \Serializable
{
    public function beforeSave()
    {
        $this->setUpdatedAt(new \DateTime);
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getSalt()
    {
        return parent::getSalt();
    }

    public function setSalt($salt)
    {
        return parent::setSalt($salt);
    }

    public function getPassword()
    {
        return parent::getPassword();
    }

    public function getRoles()
    {
        return array("ROLE_USER");
    }

    public function eraseCredentials()
    {

    }

    public function serialize()
    {
        return parent::toArray();
    }

    public function unserialize($serialized)
    {
        $this->container
            ->get("sebk_smallorm_dao")
            ->get($this->getBundle(), $this->getModelName())
            ->makeModelFromStdClass((Object) unserialize($serialized), false,
                $this);
        ;
    }

    public function isEnabled()
    {
        return $this->getEnabled();
    }

    public function setPassword($plainPassword)
    {
        $encoder       = $this->container->get('security.password_encoder');
        $encoded       = $encoder->encodePassword($this, $plainPassword);

        return parent::setPassword($encoded);
    }

    public function toArray($dependecies = true, $onlyFields = false)
    {
        $array = parent::toArray($dependecies, $onlyFields);
        unset($array["password"]);
        unset($array["salt"]);

        return $array;
    }

    public function setEmail($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) || $email === null) {
            return parent::setEmail($email);
        }

        throw new \Exception("Invalid email");
    }
}