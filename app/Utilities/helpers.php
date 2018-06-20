<?php
/**
 * Get the class name from the full class path.
 *
 * @param  string $name
 * @return string
 */
function getClassName($name)
{
  return str_replace('_', ' ', snake_case(class_basename($name)));
}