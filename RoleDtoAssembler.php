<?php

namespace UserBundle\Dto\Assembler;

use AppBundle\Utils\WebCalendarException;
use Doctrine\ORM\EntityManager;
use UserBundle\Dto\RoleDto;
use UserBundle\Entity\Role;

/**
 * Role Dto Assembler
 */
class RoleDtoAssembler
{
	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * RoleDtoAssembler constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

	/**
	 * @param Role $role
	 * @return RoleDto
	 * @throws WebCalendarException
	 */
	public function toDto(Role $role): RoleDto
	{
		if (!$this->em->contains($role)) {
			throw new WebCalendarException("Can not convert non existent object to DTO");
		}
		$roleDto = new RoleDto();
		$roleDto->id = $role->getId();
		$roleDto->name = $role->getName();
		return $roleDto;
	}

	/**
	 * @param RoleDto $roleDto
	 * @param Role|null $role
	 * @return Role
	 */
	public function fromDto(RoleDto $roleDto, Role $role = null): Role
	{
		if (!isset($role)) {
			$role = new Role();
		}
		if (isset($roleDto->name)) {
			$role->setName($roleDto->name);
		}
		return $role;
	}
}