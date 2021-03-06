<?php namespace Gitlab\Model;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Gitlab\Api\AbstractApi;

abstract class AbstractModel
{
    /**
     * @var array
     */
    protected static $properties;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var Client
     */
    protected $client;

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client = null)
    {
        if (null !== $client) {
            $this->client = $client;
        }

        return $this;
    }

    /**
     * @param string $api
     * @return AbstractApi|mixed
     */
    public function api($api)
    {
        return $this->getClient()->api($api);
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function hydrate(array $data = array())
    {
        if (!empty($data)) {
            foreach ($data as $field => $value) {
                $this->setData($field, $value);
            }
        }

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    protected function setData($field, $value)
    {
        if (in_array($field, static::$properties)) {
            $this->data[$field] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @throws RuntimeException
     */
    public function __set($property, $value)
    {
        throw new RuntimeException('Model properties are immutable');
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if (!in_array($property, static::$properties)) {
            throw new RuntimeException(sprintf(
                'Property "%s" does not exist for %s object',
                $property, get_called_class()
            ));
        }

        if (isset($this->data[$property])) {
            return $this->data[$property];
        }

        return null;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->data[$property]);
    }
}
