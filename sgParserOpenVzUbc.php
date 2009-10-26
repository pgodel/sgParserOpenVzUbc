<?php

class sgParserOpenVzUbc
{
  public $version;
  public $list = array();

  function __contruct()
  {

  }


  public function parseFile( $fname )
  {
    return $this->parse( file_get_contents( $fname ) );
  }


  public function parse($str)
  {
    $str = trim($str);

    if ( empty( $str ))
    {
      return false;
    }

    $currentVz = 0;

    $lines = explode( "\n", $str );

    foreach( $lines as $line )
    {
      $line = trim( $line );
      $line = preg_replace('/\s\s+/', ' ', $line );

      if ( !empty( $line ))
      {
        switch (true)
        {
          case strpos( $line, 'Version' ) !== false:
            $this->version = substr( $line, 8 );
            break;
          case strpos( $line, 'uid' ) !== false:
            // ignore line
            break;
          case strpos( $line, ':' ) !== false:
            $parts = explode( ':', $line );
            $currentVz = $parts[0];
            $parts[1] = trim( $parts[1]);

            $parts = explode( ' ', $parts[1]);
            $metric = $this->parseMetrics( $parts );
            $this->list[ $currentVz ][ $metric->name ] = $metric;
            break;
          default:
            $parts = explode( ' ', $line );
            $metric = $this->parseMetrics( $parts );
            $this->list[ $currentVz ][ $metric->name ] = $metric;
            break;
        }
      }
    }
    return true;
  }

  public function parseMetrics( $parts )
  {
    $metric = new sgOpenVzMetric(
    $parts[0],
    $parts[1],
    $parts[2],
    $parts[3],
    $parts[4],
    $parts[5]
    );

    return $metric;
  }
}

class sgOpenVzMetric
{
  public $name = "";
  public $value = 0;
  public $maxValue = 0;
  public $barrier = 0;
  public $limit = 0;
  public $failCount = 0;

  function __construct( $name, $value, $maxValue, $barrier, $limit, $failCount = 0 )
  {
    $this->name = $name;
    $this->value = $value;
    $this->maxValue = $maxValue;
    $this->barrier = $barrier;
    $this->limit = $limit;
    $this->failCount = $failCount;
  }
}

/*
$p = new sgParserOpenVzUbc();
$p->parseFile( '/proc/user_beancounters');
print_r( $p );
*/
