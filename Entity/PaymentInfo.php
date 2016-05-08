<?php

namespace SymfonyDev\TCBundle\Entity;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * PaymentInfo
 *
 * @ORM\Table(name="payment_info")
 * @ORM\Entity(repositoryClass="SymfonyDev\TCBundle\Repository\PaymentInfoRepository")
 */
class PaymentInfo
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
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]*$/",
     *     message="Your name should have only letters."
     * )
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Your name cannot be longer than {{ limit }} characters"
     * )
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="postCode", type="smallint")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 4,
     *      max = 4,
     *      minMessage = "Your post code must be at least {{ limit }} characters long",
     *      maxMessage = "Your post code cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="Only number allowed."
     * )
     */
    private $postCode;

    /**
     * @var string
     *
     * @ORM\Column(name="creditCardNumber", type="string", length=255)
     */
    private $creditCardNumber;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 13,
     *      max = 16,
     *      minMessage = "Your credit card number must be at least {{ limit }} characters long",
     *      maxMessage = "Your credit card number cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Luhn()
     */
    private $creditCardNumberPlain;

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        $ccn = $this->getCreditCardNumberPlain();
        $ti = $this->typeInfo[$this->getType()];

        if (!preg_match('/'.$ti[0].'/', $ccn, $match)) {
            $context->buildViolation('Card number not matched with his type.')
                ->atPath('creditCardNumberPlain')
                ->addViolation();
        }

        $isValidLength = false;
        $maxLengths = explode('|', $ti[1]);
        foreach ($maxLengths as $len) {
            if ($len == strlen($ccn)) {
                $isValidLength = true;
                break;
            }
        }
        if (!$isValidLength) {
            $context->buildViolation('Invalid card number length.')
                ->atPath('creditCardNumberPlain')
                ->addViolation();
        }
    }

    /**
     * @var string
     */
    private $type;

    const TYPE_AMEX = 'AMEX';
    const TYPE_DISCOVER = 'Discover';
    const TYPE_MASTERCARD = 'MasterCard';
    const TYPE_VISA = 'Visa';

    /**
     * @var array
     */
    private $typeInfo = array(
        'AMEX' => array('^(34|37)', '15'),
        'Discover' => array('^6011', '16'),
        'MasterCard' => array('^(51|52|53|54|55)', '16'),
        'Visa' => array('^4', '13|16')
    );

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PaymentInfo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set postCode
     *
     * @param integer $postCode
     * @return PaymentInfo
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get postCode
     *
     * @return integer
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set creditCardNumber
     *
     * @param string $creditCardNumber
     * @return PaymentInfo
     */
    public function setCreditCardNumber($creditCardNumber)
    {
        $this->creditCardNumber = $creditCardNumber;

        return $this;
    }

    /**
     * Get creditCardNumber
     *
     * @return string
     */
    public function getCreditCardNumber()
    {
        return $this->creditCardNumber;
    }

    /**
     * Set creditCardNumberPlain
     *
     * @param string $creditCardNumberPlain
     * @return PaymentInfo
     */
    public function setCreditCardNumberPlain($creditCardNumberPlain)
    {
        $this->creditCardNumberPlain = $creditCardNumberPlain;
        $this->setCreditCardNumber($this->encodeString($this->creditCardNumberPlain));

        return $this;
    }

    /**
     * Get creditCardNumberPlain
     *
     * @return string
     */
    public function getCreditCardNumberPlain()
    {
        return $this->creditCardNumberPlain;
    }

    /**
     * Encode string
     *
     * @param string $string
     * @return string
     */
    public function encodeString($string)
    {
        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $encodedString = $encoder->encodePassword(
            $string,
            $salt
        );

        return $encodedString;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return PaymentInfo
     */
    public function setType($type)
    {
        $ucase = strtoupper($type);
        if (!defined('static::TYPE_'.$ucase)) {
            throw new \InvalidArgumentException('Invalid type passed');
        }
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get type options
     *
     * @return array
     */
    public static function getTypeOptions()
    {
        return array(
            static::TYPE_AMEX => 'AMEX',
            static::TYPE_DISCOVER => 'Discover',
            static::TYPE_MASTERCARD => 'Master Card',
            static::TYPE_VISA => 'Visa'
        );
    }
}
