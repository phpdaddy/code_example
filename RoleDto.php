<?php

namespace UserBundle\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role Dto
 */
class RoleDto
{
	/**
	 * @var int|null
	 */
	public $id;
	/**
	 * @var string|null
	 *
	 * @Assert\Type("string")
	 * @Assert\NotBlank()
	 * @Assert\Length( min=3, max = 255 )
	 */
	public $name;
}