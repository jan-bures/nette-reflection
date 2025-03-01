<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Reflection;

use mysql_xdevapi\Exception;
use Nette;


/**
 * Reports information about a class.
 * @property-read Method $constructor
 * @property-read Extension $extension
 * @property-read ClassType[] $interfaces
 * @property-read Method[] $methods
 * @property-read ClassType $parentClass
 * @property-read Property[] $properties
 * @property-read IAnnotation[][] $annotations
 * @property-read string $description
 * @property-read string $name
 * @property-read bool $internal
 * @property-read bool $userDefined
 * @property-read bool $instantiable
 * @property-read string $fileName
 * @property-read int $startLine
 * @property-read int $endLine
 * @property-read string $docComment
 * @property-read mixed[] $constants
 * @property-read string[] $interfaceNames
 * @property-read bool $interface
 * @property-read bool $abstract
 * @property-read bool $final
 * @property-read int $modifiers
 * @property-read array $staticProperties
 * @property-read array $defaultProperties
 * @property-read bool $iterateable
 * @property-read string $extensionName
 * @property-read string $namespaceName
 * @property-read string $shortName
 */
class ClassType extends \ReflectionClass
{
	use Nette\SmartObject;

	/**
	 * @param  string|object
	 * @return static
	 */
	public static function from($class)
	{
		return new static($class);
	}


	public function __toString(): string
	{
		return $this->getName();
	}


	/**
	 * @param  string
	 * @return bool
	 */
	public function is($type)
	{
		return is_a($this->getName(), $type, true);
	}


	/********************* Reflection layer ****************d*g**/


	/**
	 * @return Method|null
	 */
	public function getConstructor(): ?Method
	{
		return ($ref = parent::getConstructor()) ? Method::from($this->getName(), $ref->getName()) : null;
	}


	/**
	 * @return Extension|null
	 */
	public function getExtension(): ?Extension
	{
		return ($name = $this->getExtensionName()) ? new Extension($name) : null;
	}


	/**
	 * @return static[]
	 */
	public function getInterfaces(): array
	{
		$res = [];
		foreach (parent::getInterfaceNames() as $val) {
			$res[$val] = new static($val);
		}
		return $res;
	}


	/**
	 * @return Method
	 */
	public function getMethod(string $name): Method
	{
		return new Method($this->getName(), $name);
	}


	/**
	 * @return Method[]
	 */
	public function getMethods(?int $filter = -1): array
	{
		foreach ($res = parent::getMethods($filter) as $key => $val) {
			$res[$key] = new Method($this->getName(), $val->getName());
		}
		return $res;
	}


	/**
	 * @return static|false
	 */
	public function getParentClass(): static|false
	{
		return ($ref = parent::getParentClass()) ? new static($ref->getName()) : false;
	}


	/**
	 * @return Property[]
	 */
	public function getProperties(?int $filter = -1): array
	{
		foreach ($res = parent::getProperties($filter) as $key => $val) {
			$res[$key] = new Property($this->getName(), $val->getName());
		}
		return $res;
	}


	/**
	 * @return Property
	 */
	public function getProperty(string $name): Property
	{
		return new Property($this->getName(), $name);
	}


	/********************* Nette\Annotations support ****************d*g**/


	/**
	 * Has class specified annotation?
	 * @param  string
	 * @return bool
	 */
	public function hasAnnotation($name)
	{
		$res = AnnotationsParser::getAll($this);
		return !empty($res[$name]);
	}


	/**
	 * Returns an annotation value.
	 * @param  string
	 * @return IAnnotation|null
	 */
	public function getAnnotation($name)
	{
		$res = AnnotationsParser::getAll($this);
		return isset($res[$name]) ? end($res[$name]) : null;
	}


	/**
	 * Returns all annotations.
	 * @return IAnnotation[][]
	 */
	public function getAnnotations()
	{
		return AnnotationsParser::getAll($this);
	}


	/**
	 * Returns value of annotation 'description'.
	 * @return string
	 */
	public function getDescription()
	{
		return $this->getAnnotation('description');
	}
}
