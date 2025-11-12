<?php

namespace tei187\GitDisWebhook\Traits;

/**
 * A trait that provides a magic getter method for accessing properties of an object.
 */
trait UsesMagicGetter {
    /**
     * Retrieves the value of the specified, non-static property from the object.
     *
     * @param string $param The name of the property to retrieve.
     * @return mixed The value of the specified, non-static property, or null if the property does not exist.
     */
    function __get($param) {
        if(isset($this->$param)) {
            return $this->$param;
        }
    }
}