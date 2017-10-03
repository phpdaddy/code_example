<?php

namespace UserBundle\Controller;

use AppBundle\Utils\ErrorView;
use AppBundle\Utils\NotFoundView;
use AppBundle\Utils\WebCalendarException;
use UserBundle\Dto\Assembler\RoleDtoAssembler;
use UserBundle\Dto\RoleDto;
use UserBundle\Entity\Role;
use UserBundle\Form\RoleType;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Voryx\RESTGeneratorBundle\Controller\VoryxController;

/**
 * Role controller.
 * @RouteResource("Role")
 */
class RoleRESTController extends VoryxController
{
	/**
	 * @var RoleDtoAssembler
	 */
	private $roleDtoAssembler;

	/**
	 * RoleRESTController constructor.
	 * @param RoleDtoAssembler $roleDtoAssembler
	 */
	public function __construct(RoleDtoAssembler $roleDtoAssembler)
	{
		$this->roleDtoAssembler = $roleDtoAssembler;
	}

	/**
	 * Get a Role entity
	 *
	 * @View(serializerEnableMaxDepthChecks=true)
	 *
	 * @param Role $role
	 * @return Response
	 */
	public function getAction(Role $role)
	{
		return $this->roleDtoAssembler->toDto($role);
	}

	/**
	 * Get all Role entities.
	 *
	 * @View(serializerEnableMaxDepthChecks=true)
	 *
	 * @param ParamFetcherInterface $paramFetcher
	 *
	 * @return Response
	 *
	 * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing notes.")
	 * @QueryParam(name="limit", requirements="\d+", default="20", description="How many notes to return.")
	 * @QueryParam(name="order_by", nullable=true, array=true, description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC")
	 * @QueryParam(name="filters", nullable=true, array=true, description="Filter by fields. Must be an array ie. &filters[id]=3")
	 */
	public function cgetAction(ParamFetcherInterface $paramFetcher)
	{
		try {
			$offset = $paramFetcher->get('offset');
			$limit = $paramFetcher->get('limit');
			$order_by = $paramFetcher->get('order_by');
			$filters = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : array();

			$em = $this->getDoctrine()->getManager();
			$roles = $em->getRepository('UserBundle:Role')->findBy($filters, $order_by, $limit, $offset);

			$roleDtos = array_map(function (Role $role) {
				return $this->roleDtoAssembler->toDto($role);
			}, $roles);

			if ($roleDtos) {
				return $roleDtos;
			}

			return NotFoundView::create();
		} catch (\Exception $e) {
			return ErrorView::createGeneral($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Create a Role entity.
	 *
	 * @View(statusCode=201, serializerEnableMaxDepthChecks=true)
	 *
	 * @param Request $request
	 *
	 * @return Response
	 *
	 */
	public function postAction(Request $request)
	{
		try {
			$roleDto = new RoleDto();
			$form = $this->createForm(get_class(new RoleType()), $roleDto, array("method" => $request->getMethod()));
			$this->removeExtraFields($request, $form);
			$form->handleRequest($request);

			if (!$form->isValid()) {
				return ErrorView::createValidation($form->getErrors(), Codes::HTTP_INTERNAL_SERVER_ERROR);
			}

			$role = $this->roleDtoAssembler->fromDto($roleDto);

			$em = $this->getDoctrine()->getManager();
			$em->persist($role);
			$em->flush();

			return $this->roleDtoAssembler->toDto($role);
		} catch (WebCalendarException $e) {
			return ErrorView::create($e->getMessage(), null, Codes::HTTP_INTERNAL_SERVER_ERROR);
		} catch (\Exception $e) {
			return ErrorView::createGeneral($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Update a Role entity.
	 *
	 * @View(serializerEnableMaxDepthChecks=true)
	 *
	 * @param Request $request
	 * @param Role $role
	 *
	 * @return Response
	 */
	public function putAction(Request $request, Role $role)
	{
		try {
			$roleDto = new RoleDto();
			$em = $this->getDoctrine()->getManager();
			$request->setMethod('PATCH'); //Treat all PUTs as PATCH
			$form = $this->createForm(get_class(new RoleType()), $roleDto, array("method" => $request->getMethod()));
			$this->removeExtraFields($request, $form);
			$form->handleRequest($request);
			if (!$form->isValid()) {
				return ErrorView::createValidation($form->getErrors(), Codes::HTTP_INTERNAL_SERVER_ERROR);
			}

			$role = $this->roleDtoAssembler->fromDto($roleDto, $role);
			$em->persist($role);
			$em->flush();

			return $this->roleDtoAssembler->toDto($role);
		} catch (WebCalendarException $e) {
			return ErrorView::create($e->getMessage(), null, Codes::HTTP_INTERNAL_SERVER_ERROR);
		} catch (\Exception $e) {
			return ErrorView::createGeneral($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Partial Update to a Role entity.
	 *
	 * @View(serializerEnableMaxDepthChecks=true)
	 *
	 * @param Request $request
	 * @param Role $role
	 *
	 * @return Response
	 */
	public function patchAction(Request $request, Role $role)
	{
		return $this->putAction($request, $role);
	}

	/**
	 * Delete a Role entity.
	 *
	 * @View(statusCode=204)
	 *
	 * @param Request $request
	 * @param Role $role
	 *
	 * @return Response
	 */
	public function deleteAction(Request $request, Role $role)
	{
		try {
			$em = $this->getDoctrine()->getManager();
			$em->remove($role);
			$em->flush();

			return null;
		} catch (\Exception $e) {
			return ErrorView::createGeneral($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}