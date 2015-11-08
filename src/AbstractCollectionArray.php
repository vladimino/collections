<?php

// Copyright (c) Lellys Informática. All rights reserved. See License.txt in the project root for license information.
namespace Collections;

use Closure;
use Collections\Iterator\MergeIterator;
use Collections\Rx\ReactiveExtensionInterface;
use Collections\Rx\RxTrait;
use Collections\Traits\StrictIterableTrait;
use Collections\Traits\StrictKeyedIterableTrait;
use Rx\Observable\ArrayObservable;
use Rx\ObservableInterface;
use Traversable;

/**
 * Provides the abstract base class for a strongly typed collection.
 */
abstract class AbstractCollectionArray extends AbstractCollection implements
    CollectionInterface,
    IndexAccessInterface,
    ReactiveExtensionInterface,
    ConstIndexAccessInterface,
    CollectionConvertableInterface,
    \Serializable,
    \JsonSerializable
{

    use
        StrictIterableTrait,
        StrictKeyedIterableTrait,
        RxTrait,
        SortTrait;

    /**
     * @var array
     */
    protected $storage = [];

    public function __construct($array = null)
    {
        if ($array !== null) {
            $this->addAll($array);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->storage);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->storage = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->count() < 1;
    }

    /**
     * {@inheritdoc}
     */
    public function values()
    {
        return $this->toValuesArray();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->storage);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->storage = unserialize($serialized);

        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function concat(\Iterable $collection)
    {
        $this->storage = array_merge($this->storage, $collection->toArray());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function containsKey($key)
    {
        return $this->offsetExists($key);
    }

    public function set($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($index)
    {
        return $this->offsetGet($index);
    }

    /**
     * {@inheritdoc}
     */
    public function tryGet($index, $default = null)
    {
        if ($this->containsKey($index) === false) {
            return $default;
        }

        return $this->get($index);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(Closure $closure)
    {
        foreach ($this->storage as $key => $element) {
            if ($closure($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ObservableInterface
     */
    public function toObservable()
    {
        return new ArrayObservable($this->toArray());
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->storage;
    }
}
