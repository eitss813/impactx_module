<?php
namespace Laravolt\Avatar;

use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\ImageManager;
use Laravolt\Avatar\Generator\DefaultGenerator;
use Laravolt\Avatar\Generator\GeneratorInterface;
use Laravolt\Avatar\StringScript;

class Avatar
{

  protected $name;
  protected $chars;
  protected $shape;
  protected $width;
  protected $height;
  protected $availableBackgrounds;
  protected $availableForegrounds;
  protected $fonts;
  protected $fontSize;
  protected $borderSize = 0;
  protected $borderColor;
  protected $ascii = false;
  protected $uppercase = false;

  /**
   * @var \Intervention\Image\Image
   */
  protected $image;
  protected $font = null;
  protected $background = '#cccccc';
  protected $foreground = '#ffffff';
  protected $initials = '';
  protected $driver;
  protected $initialGenerator;
  protected $defaultFont = APPLICATION_PATH_MOD . '/Siteuseravatar/Avatar/fonts/OpenSans-Bold.ttf';

  /**
   * Avatar constructor.
   *
   * @param array      $config
   */
  public function __construct(array $config = array())
  {
    $default = array(
      'driver' => 'gd',
      'shape' => 'circle',
      'chars' => 2,
      'backgrounds' => array($this->background),
      'foregrounds' => array($this->foreground),
      'fonts' => array($this->defaultFont),
      'fontSize' => 48,
      'width' => 100,
      'height' => 100,
      'ascii' => false,
      'uppercase' => false,
      'border' => array(
        'size' => 1,
        'color' => 'foreground',
      ),
    );

    $config += $default;

    $this->driver = $config['driver'];
    $this->shape = $config['shape'];
    $this->chars = $config['chars'];
    $this->availableBackgrounds = $config['backgrounds'];
    $this->availableForegrounds = $config['foregrounds'];
    $this->fonts = $config['fonts'];
    $this->font = $this->defaultFont;
    $this->fontSize = $config['fontSize'];
    $this->width = $config['width'];
    $this->height = $config['height'];
    $this->ascii = $config['ascii'];
    $this->uppercase = $config['uppercase'];
    $this->borderSize = $config['border']['size'];
    $this->borderColor = $config['border']['color'];
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string) $this->toBase64();
  }

  public function setGenerator(GeneratorInterface $generator)
  {
    $this->initialGenerator = $generator;
  }

  public function create($name)
  {
    $this->name = $name;

    $this->setForeground($this->getRandomForeground());
    $this->setBackground($this->getRandomBackground());

    return $this;
  }

  public function setFont($font)
  {
    if( is_file($font) ) {
      $this->font = $font;
    } else {
      $this->setRandomFont();
    }

    return $this;
  }

  public function toBase64()
  {

    $this->buildAvatar();

    return $this->image->encode('data-url');
  }

  public function save($path, $quality = 90)
  {
    $this->buildAvatar();

    return $this->image->save($path, $quality);
  }

  public function toSvg()
  {
    $this->buildInitial();

    $x = $y = $this->borderSize / 2;
    $width = $height = $this->width - $this->borderSize;
    $radius = ($this->width - $this->borderSize) / 2;
    $center = $this->width / 2;

    $svg = '<svg width="' . $this->width . '" height="' . $this->height . '">';

    if( $this->shape == 'square' ) {
      $svg .= '<rect x="' . $x
        . '" y="' . $y
        . '" width="' . $width . '" height="' . $height
        . '" stroke="' . $this->borderColor
        . '" stroke-width="' . $this->borderSize
        . '" fill="' . $this->background . '" />';
    } elseif( $this->shape == 'circle' ) {
      $svg .= '<circle cx="' . $center
        . '" cy="' . $center
        . '" r="' . $radius
        . '" stroke="' . $this->borderColor
        . '" stroke-width="' . $this->borderSize
        . '" fill="' . $this->background . '" />';
    }

    $svg .= '<text x="' . $center . '" y="' . $center
      . '" font-size="' . $this->fontSize
      . '" fill="' . $this->foreground . '" alignment-baseline="middle" text-anchor="middle">'
      . $this->getInitial()
      . '</text>';

    $svg .= '</svg>';

    return $svg;
  }

  public function setBackground($hex)
  {
    $this->background = $hex;

    return $this;
  }

  public function setForeground($hex)
  {
    $this->foreground = $hex;

    return $this;
  }

  public function setDimension($width, $height = null)
  {
    if( !$height ) {
      $height = $width;
    }
    $this->width = $width;
    $this->height = $height;

    return $this;
  }

  public function setFontSize($size)
  {
    $this->fontSize = $size;

    return $this;
  }

  public function setBorder($size, $color)
  {
    $this->borderSize = $size;
    $this->borderColor = $color;

    return $this;
  }

  public function setShape($shape)
  {
    $this->shape = $shape;

    return $this;
  }

  public function getInitial()
  {
    return $this->initials;
  }

  public function getImageObject()
  {
    $this->buildAvatar();

    return $this->image;
  }

  protected function getRandomBackground()
  {
    // return  str_pad(substr(dechex(crc32($this->name)), 0, 6), 6, '0', STR_PAD_LEFT);
    return $this->getRandomElement($this->availableBackgrounds, $this->background);
  }

  protected function getRandomForeground()
  {
    return $this->getRandomElement($this->availableForegrounds, $this->foreground);
  }

  protected function setRandomFont()
  {
    $randomFont = $this->getRandomElement($this->fonts, $this->defaultFont);

    $this->setFont($randomFont);
  }

  protected function getBorderColor()
  {
    if( $this->borderColor == 'foreground' ) {
      return $this->foreground;
    }
    if( $this->borderColor == 'background' ) {
      return $this->background;
    }

    return $this->borderColor;
  }

  public function buildAvatar()
  {
    $this->buildInitial();

    $x = $this->width / 2;
    $y = $this->height / 2;

    $manager = new ImageManager(['driver' => $this->driver]);
    $this->image = $manager->canvas($this->width, $this->height);

    $this->createShape();

    $font = $this->_getFontByScript();
    if( file_exists($font) ) {
      $this->font = $font;
    } else if( !file_exists($font) ) {
      $this->font = $this->defaultFont;
    }

    $this->image->text(
      $this->initials, $x, $y, function (AbstractFont $font) {
      $font->file($this->font);
      $font->size($this->fontSize);
      $font->color($this->foreground);
      $font->align('center');
      $font->valign('middle');
    }
    );

    return $this;
  }

  protected function createShape()
  {
    $method = 'create' . ucfirst($this->shape) . 'Shape';
    if( method_exists($this, $method) ) {
      return $this->$method();
    }

    throw new \InvalidArgumentException("Shape [$this->shape] currently not supported.");
  }

  protected function createCircleShape()
  {
    $circleDiameter = $this->width - $this->borderSize;
    $x = $this->width / 2;
    $y = $this->height / 2;

    $this->image->circle(
      $circleDiameter, $x, $y, function (AbstractShape $draw) {
      $draw->background($this->background);
      $draw->border($this->borderSize, $this->getBorderColor());
    }
    );
  }

  protected function createSquareShape()
  {
    $edge = (ceil($this->borderSize / 2));
    $x = $y = $edge;
    $width = $this->width - $edge;
    $height = $this->height - $edge;

    $this->image->rectangle(
      $x, $y, $width, $height, function (AbstractShape $draw) {
      $draw->background($this->background);
      $draw->border($this->borderSize, $this->getBorderColor());
    }
    );
  }

  protected function getRandomElement($array, $default)
  {
    if( strlen($this->name) == 0 || count($array) == 0 ) {
      return $default;
    }

    $number = ord($this->name[0]);
    $i = 1;
    $charLength = strlen($this->name);
    while( $i < $charLength ) {
      $number += ord($this->name[$i]);
      $i++;
    }

    return $array[$number % count($array)];
  }

  protected function buildInitial()
  {
    // fallback to default
    if( !$this->initialGenerator ) {
      $this->initialGenerator = new DefaultGenerator();
    }

    $this->initials = $this->initialGenerator->make($this->name, $this->chars, $this->uppercase, $this->ascii);
  }

  protected function _getFontByScript()
  {
    if( StringScript::isArabic($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Arabic-Regular.ttf';
    }
    if( StringScript::isArmenian($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Armenian-Regular.ttf';
    }
    if( StringScript::isBengali($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Bengali-Regular.ttf';
    }
    if( StringScript::isGeorgian($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Georgian-Regular.ttf';
    }
    if( StringScript::isHebrew($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Hebrew-Regular.ttf';
    }
    if( StringScript::isMongolian($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Mongolian-Regular.ttf';
    }
    if( StringScript::isThai($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Thai-Regular.ttf';
    }
    if( StringScript::isTibetan($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-Tibetan-Regular.ttf';
    }
    if( StringScript::isChinese($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-CJKJP-Regular.otf';
    }
    if( StringScript::isJapanese($this->getInitial()) ) {
      return APPLICATION_PATH_PUB . '/Siteuseravatar/fonts/script/Noto-CJKJP-Regular.otf';
    }
    return $this->font;
  }

}
