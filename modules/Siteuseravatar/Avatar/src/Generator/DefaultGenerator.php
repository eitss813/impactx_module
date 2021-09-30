<?php
namespace Laravolt\Avatar\Generator;

use Stringy\Stringy;
//use Laravolt\Avatar\Initials;

class DefaultGenerator implements GeneratorInterface
{
  public function make($name, $length = 2, $uppercase = false, $ascii = false)
  {
    $this->setName($name, $ascii);
//    $initials = new Initials();
//    return $initials->length($length)->keepCase(!$uppercase)->generate($this->name);

    $words = explode(' ', $this->name);

    // if name contains single word, use first N character
    if( count($words) === 1 ) {
      $initial = (string) $this->name;

      if( strlen($this->name) >= $length ) {
        $initial = \Engine_String::substr($initial, 0, $length);
      }
    } else {
      // otherwise, use initial char from each word
      $initials = array();
      foreach( $words as $word ) {

        $initials[] = \Engine_String::substr($word, 0, 1);
      }

      $initial = join('', array_slice($initials, 0, $length));
    }

    if( $uppercase ) {
      $initial = mb_strtoupper($initial);
    }

    return $initial;
  }

  private function setName($name, $ascii)
  {
    if( is_array($name) ) {
      throw new \InvalidArgumentException(
      'Passed value cannot be an array'
      );
    } elseif( is_object($name) && !method_exists($name, '__toString') ) {
      throw new \InvalidArgumentException(
      'Passed object must have a __toString method'
      );
    }

    $name = Stringy::create($name)->collapseWhitespace();

    if( filter_var($name, FILTER_VALIDATE_EMAIL) ) {
      // turn bayu.hendra@gmail.com into "Bayu Hendra"
      $name = current($name->split('@', 1))->replace('.', ' ');
    }

    if( $ascii ) {
      $name = $name->toAscii();
    }

    $this->name = $name;
  }

}
