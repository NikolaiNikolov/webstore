<?php

namespace WebstoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @UniqueEntity("username", message="This username is already taken")
 * @ORM\Entity(repositoryClass="WebstoreBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Please enter a username")
     * @Assert\Length(
     *     min = 3,
     *     minMessage="Username can't be shorter than {{ limit }} symbols",
     *     max = 200,
     *     maxMessage="Username can't be longer than {{ limit }} symbols"
     * )
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @var string
     * @Assert\NotBlank(message="Please enter a password")
     * @Assert\Length(
     *     min = 3,
     *     minMessage="Password can't be shorter than {{ limit }} symbols",
     *     max = 200,
     *     maxMessage="Password can't be longer than {{ limit }} symbols"
     * )
     * @ORM\Column(name="password", type="string", length=100)
     */
    private $password;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 3,
     *     minMessage="First name can't be shorter than {{ limit }} symbols",
     *     max = 200,
     *     maxMessage="First name can't be longer than {{ limit }} symbols"
     * )
     * @ORM\Column(name="firstName", type="string", length=255, options={"default" : 0})
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 3,
     *     minMessage="Last name can't be shorter than {{ limit }} symbols",
     *     max = 200,
     *     maxMessage="Last name can't be longer than {{ limit }} symbols"
     * )
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;
    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2)
     */
    private $balance = 10000.00;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WebstoreBundle\Entity\Product", mappedBy="owner")
     */
    private $products;
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="WebstoreBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *     )
     */
    private $roles;

    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts(): ArrayCollection
    {
        return $this->products;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function addMoney($money)
    {
        $this->setBalance($this->getBalance() + $money);
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance)
    {
        $this->balance = $balance;
    }

    public function takeMoney($money)
    {
        $this->setBalance($this->getBalance() - $money);
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return bool
     */
    public function isOwner(Product $product)
    {
        return $product->getOwner()->getUsername() == $this->getUsername();
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return array_map(function (Role $role) {
            return $role->getName();
        }, $this->roles->toArray());
    }

    /**
     * @return bool
     */
    public function isEditor()
    {
        return in_array("ROLE_EDITOR", $this->getRoles());
    }

    /**
     * Get image
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return User
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
}

