<?php

namespace LightSaml\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

abstract class SSODescriptor extends RoleDescriptor
{
    /** @var  SingleLogoutService[] */
    protected $singleLogoutServices = array();

    /** @var  string[]|null */
    protected $nameIDFormats;

    /**
     * @param SingleLogoutService $singleLogoutService
     *
     * @return SSODescriptor
     */
    public function addSingleLogoutService(SingleLogoutService $singleLogoutService)
    {
        $this->singleLogoutServices[] = $singleLogoutService;

        return $this;
    }

    /**
     * @return SingleLogoutService[]
     */
    public function getAllSingleLogoutServices()
    {
        return $this->singleLogoutServices;
    }

    /**
     * @param string $binding
     *
     * @return SingleLogoutService[]
     */
    public function getAllSingleLogoutServicesByBinding($binding)
    {
        $result = array();
        foreach ($this->getAllSingleLogoutServices() as $svc) {
            if ($binding == $svc->getBinding()) {
                $result[] = $svc;
            }
        }

        return $result;
    }

    /**
     * @param string|null $binding
     *
     * @return SingleLogoutService|null
     */
    public function getFirstSingleLogoutService($binding = null)
    {
        foreach ($this->getAllSingleLogoutServices() as $svc) {
            if (null == $binding || $binding == $svc->getBinding()) {
                return $svc;
            }
        }

        return null;
    }

    /**
     * @param string $nameIDFormat
     *
     * @return SSODescriptor
     */
    public function addNameIDFormat($nameIDFormat)
    {
        $this->nameIDFormats[] = $nameIDFormat;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getAllNameIDFormats()
    {
        return $this->nameIDFormats;
    }

    /**
     * @param string $nameIdFormat
     *
     * @return bool
     */
    public function hasNameIDFormat($nameIdFormat)
    {
        if ($this->nameIDFormats) {
            foreach ($this->nameIDFormats as $format) {
                if ($format == $nameIdFormat) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        parent::serialize($parent, $context);

        $this->manyElementsToXml($this->getAllNameIDFormats(), $parent, $context, 'NameIDFormat', SamlConstants::NS_METADATA);

        $this->manyElementsToXml($this->getAllSingleLogoutServices(), $parent, $context, null);
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        parent::deserialize($node, $context);

        $this->manyElementsFromXml($node, $context, 'NameIDFormat', 'md', null, 'addNameIDFormat');

        $this->manyElementsFromXml(
            $node,
            $context,
            'SingleLogoutService',
            'md',
            'LightSaml\Model\Metadata\SingleLogoutService',
            'addSingleLogoutService'
        );
    }
}