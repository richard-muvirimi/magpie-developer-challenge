<?php

declare(strict_types=1);

/**
 * Product Model file
 *
 * @package App
 * @subpackage App\Models
 * @since 1.0.0
 * @version 1.0.0
 */

namespace App\Models;

/**
 * Product model class
 *
 * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
 * @since 1.0.0
 * @version 1.0.0
 */
class Product
{

      /**
     * Storage for entity attributes
     *
     * @access protected
     * @since 1.0.0
     * @version 1.0.0
     * @var array
     */
    protected array $attributes;

      /**
     * Constructor for an entity, will call fill with passed data
     *
     * @param array $data
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    public function __construct(array $data = [])
    {

        $this->attributes = [];
        $this->fill($data);
    }

    /**
     * Fill the entities attributes with data
     *
     * Will try to merge and call custom functions to set the data
     *
     * @param array $data
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return void
     */
    public function fill(array $data): void
    {

        #merge data, preferering the passed data
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Magic method to handle property access
     *
     * Will attempt to call custom methods if they exists
     *
     * @param string $name
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return mixed #type intentionally left out.
     */
    public function __get(string $name)
    {

        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * Magic method to access entity properties
     *
     * Will attempt to call custom methods if they exists
     *
     * @param string $name
     * @param mixed  $value
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return mixed #type intentionally left out.
     */
    public function __set(string $name, $value)
    {

        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }

        #let php handle unset attributes
        $this->attributes[$name] = $value;
    }

    /**
     * Magic method to check if a property exists
     *
     * Will first check if there is a custom method, then the property
     *
     * @param string $name
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return boolean
     */
    public function __isset(string $name): bool
    {

        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return true;
        }

        return isset($this->attributes[$name]);
    }

    /**
     * Magic method to call a custom entity method
     *
     * @param string $name
     * @param array  $args
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return mixed #type intentionally left out.
     */
    public function __call(string $name, array $args)
    {

        $name = strtolower($name);

        if (str_starts_with($name, 'set')) {
            $this->{str_replace('set', '', $name)} = $args[0];
        }

        if (str_starts_with($name, 'get')) {
            return $this->{str_replace('get', '', $name)};
        }
    }

    /**
     * Convert entity to an array
     *
     * May optionally limit the returned fields
     *
     * @param array $prefer
     *
     * @author Richard Muvirimi <rich4rdmuvirimi@gmail.com>
     * @since 1.0.0
     * @version 1.0.0
     *
     * @return array
     */
    public function toArray(array $prefer = []): array
    {

        if (empty($prefer)) {
            return $this->attributes ;
        }
        return array_intersect_key($this->attributes, array_flip($prefer));
    }
}
