<?php

namespace Tests\UserBundle\Dto\Assembler;

use AppBundle\Utils\TestUtilTrait;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use UserBundle\Dto\Assembler\RoleDtoAssembler;
use UserBundle\Dto\RoleDto;
use UserBundle\Entity\Role;


class RoleDtoAssemblerTest extends TestCase
{
	use TestUtilTrait;

	/**
	 * @var array
	 */
	private $roleData;
	/**
	 * @var RoleDtoAssembler
	 */
	private $roleDtoAssembler;
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $emMock;

	protected function setUp()
	{
		$this->emMock = $this->createMock(EntityManager::class);
		$this->roleDtoAssembler = new RoleDtoAssembler($this->emMock);
		$this->roleData = $this->mockRoleData();
	}

	public function testFromDto()
	{
		$expected = $this->mockRole();
		$dto = $this->mockRoleDto();
		$actual = $this->roleDtoAssembler->fromDto($dto);
		$this->assertObjectsAreSame($actual, $expected);
	}

	public function testToDto()
	{
		$expected = $this->mockRoleDto();
		$role = $this->mockRole();
		// Manually set id (mock sql autoincrement)
		$this->setObjectPrivateProperty($role, 'id', $this->roleData['id']);
		$this->emMock->expects($this->once())->method('contains')->will($this->returnValue(true));
		$actual = $this->roleDtoAssembler->toDto($role);
		$this->assertObjectsAreSame($actual, $expected);
	}

	/**
	 * @return Role
	 */
	private function mockRole(): Role
	{
		$role = new Role();
		$role->setName($this->roleData['name']);
		$this->assertAllFieldsNotNull($role, ['id']);
		return $role;
	}

	/**
	 * @return RoleDto
	 */
	private function mockRoleDto(): RoleDto
	{
		$roleDto = new RoleDto();
		$roleDto->id = $this->roleData['id'];
		$roleDto->name = $this->roleData['name'];
		$this->assertAllFieldsNotNull($roleDto);
		return $roleDto;
	}

	/**
	 * @return array
	 */
	private function mockRoleData(): array
	{
		return [
			'id' => 1,
			'name' => "irrelevantName"
		];
	}
}