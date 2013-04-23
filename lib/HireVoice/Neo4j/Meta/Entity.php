<?php
/**
 * Copyright (C) 2012 Louis-Philippe Huberdeau
 *
 * Permission is hereby granted, free of charge, to any person obtaining a 
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

namespace HireVoice\Neo4j\Meta;
use Doctrine\Common\Annotations\Reader as AnnotationReader;
use HireVoice\Neo4j\Exception;

class Entity extends GraphElement
{
    private $repositoryClass = 'HireVoice\\Neo4j\\Repository';
    private $manyToManyRelations = array();
    private $manyToOneRelations = array();

	function loadRelations($reader, $properties)
	{
        foreach ($properties as $property) {
            $prop = new Property($reader, $property);
			if ($prop->isRelationList()) {
				$this->manyToManyRelations[] = $prop;
			} elseif ($prop->isRelation()) {
				$this->manyToOneRelations[] = $prop;
			}
		}
	}

	function setRepositoryClass($repositoryClass)
	{
		if ($repositoryClass) {
			$this->repositoryClass = $repositoryClass;
		}
	}

    function getRepositoryClass()
    {
        return $this->repositoryClass;
    }

    function getManyToManyRelations()
    {
        return $this->manyToManyRelations;
    }

    function getManyToOneRelations()
    {
        return $this->manyToOneRelations;
    }

    /**
     * Finds property by $name.
     *
     * @param string $name
     * @return \HireVoice\Neo4j\Meta\Property|null
     */
    function findProperty($name)
    {
		if ($p = parent::findProperty($name)) {
			return $p;
		}

        $property = Reflection::getProperty($name);

        foreach ($this->manyToManyRelations as $p) {
            if ($p->matches(substr($name, 3), $property)) {
                return $p;
            }
        }

        foreach ($this->manyToOneRelations as $p) {
            if ($p->matches(substr($name, 3), $property)) {
                return $p;
			}
		}
	}
}
