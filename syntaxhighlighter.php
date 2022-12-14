<?php
$langs = array
(
  'None' => array
  (
    'name' => 'None', 
    'keywords' => array(),
    'types' => array(),
    'preprocessor' => array(),
    'special' => array('the', 'is', 'was', 'are', 'were', 'he', 'i', 'she', 'it', 'they')
  ),
  'Generic' => array
  (
    'name' => 'Generic', 
    'keywords' => array('class', 'struct', 'union', 'goto', 'while', 'do', 'for', 'try', 'catch', 'except', 'interface', 'extends', 'implements', 'public', 'protected', 'private', 'final', 'static', 'native', 'extern', 'synchronized', 'volatile', 'typedef', 'import', 'using', 'if', 'then', 'else', 'elseif', 'elif', 'end', 'endif', 'return', 'lambda', 'def', 'function', 'include', 'include_once', 'require', 'require_once', 'var_dump', 'switch', 'case', 'default', 'break', 'continue', 'const', 'auto', 'enum', 'sizeof'), 
    'types' => array('void', 'bool', 'boolean', 'float', 'double', 'int', 'short', 'long', 'unsigned', 'signed', 'byte', 'char'), 
    'preprocessor' => array('#define', '#ifdef', '#ifndef', '#def', '#include', '#if', '#else', '#endif')
  ),
  'C' => array
  (
    'name' => 'C',
    'keywords' => array('auto', 'break', 'case', 'const', 'continue', 'default', 'do', 'else', 'enum', 'extern', 'for', 'goto', 'if', 'register', 'return', 'static', 'struct', 'switch', 'typedef', 'union', 'volatile', 'while'), 
    'types' => array('void', 'float', 'double', 'int', 'short', 'long', 'unsigned', 'signed', 'byte', 'char'), 
    'preprocessor' => array('#define', '#ifdef', '#ifndef', '#def', '#include', '#if', '#else', '#endif'),
    'special' => array('sizeof')
  ),
  'C++' => array
  (
    'name' => 'CPlusPlus',
    'keywords' => array('alignas', 'alignof', 'and', 'and_eq', 'asm', 'auto', 'bitand', 'bitor', 'break', 'case', 'catch', 'class', 'compl', 'const', 'constexpr', 'const_cast', 'continue', 'decltype', 'default', 'delete', 'do', 'dynamic_cast', 'else', 'enum', 'explicit', 'export', 'extern', 'for', 'friend', 'goto', 'if', 'inline', 'mutable', 'namespace', 'new', 'noexcept', 'not', 'not_eq', 'nullptr', 'operator', 'or', 'or_eq', 'private', 'protected', 'public', 'register', 'reinterpret_cast', 'return', 'static', 'static_assert', 'static_cast', 'struct', 'switch', 'template', 'thread_local', 'throw', 'try', 'typedef', 'typeid', 'typename', 'union', 'using', 'virtual', 'volatile', 'while', 'xor', 'xor_eq'), 
    'types' => array('void', 'bool', 'float', 'double', 'int', 'short', 'long', 'unsigned', 'signed', 'byte', 'char'), 
    'preprocessor' => array('#define', '#ifdef', '#ifndef', '#def', '#include', '#if', '#else', '#endif'),
    'special' => array('char16_t', 'char32_t', 'false', 'sizeof', 'this', 'true', 'wchar_t')
  ),
  'Java' => array
  (
    'name' => 'Java',
    'keywords' => array('abstract', 'break', 'case', 'catch', 'class', 'const', 'continue', 'default', 'do', 'else', 'enum', 'extends', 'final', 'finally', 'for', 'goto', 'if', 'implements', 'import', 'instanceof', 'interface', 'native', 'new', 'package', 'private', 'protected', 'public', 'return', 'static', 'strictfp', 'switch', 'synchronized', 'throw', 'throws', 'transient', 'try', 'volatile'), 
    'types' => array('void', 'boolean', 'float', 'double', 'int', 'short', 'long', 'unsigned', 'signed', 'byte', 'char'), 
    'preprocessor' => array(),
    'special' => array('assert', 'false', 'super', 'this', 'true')
  ),
  'PHP' => array
  (
    'name' => 'PHP',
    'keywords' => array('abstract', 'and', 'as', 'break', 'case', 'catch', 'cfunction', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'do', 'else', 'elseif', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'interface', 'instanceof', 'namespace', 'new', 'old_function', 'or', 'private', 'protected', 'public', 'static', 'switch', 'throw', 'try', 'use', 'var', 'while', 'xor'), 
    'types' => array('bool', 'float', 'double', 'int', 'short', 'long', 'array'), 
    'preprocessor' => array(),
    'special' => array('false', 'true')
  ),
  'Python' => array
  (
    'name' => 'Python',
    'keywords' => array('and', 'as', 'assert', 'break', 'class', 'continue', 'def', 'del', 'elif', 'else', 'except', 'exec', 'finally', 'for', 'from', 'global', 'if', 'import', 'in', 'is', 'lambda', 'not', 'or', 'pass', 'print', 'raise', 'return', 'try', 'while', 'with', 'yield'), 
    'types' => array(), 
    'preprocessor' => array(),
    'special' => array('false', 'true')
  )
);
foreach($langs as $lang => $data)
{
  if(!defined($lang))
  {
    define($lang, $data['name']);
  }
}

function supportedlangs()
{
  $files = scandir('geshi');
  unset($files[0]);
  unset($files[1]);
  //var_dump($files);
  $files = array_map(function($e){return substr($e, 0, strlen($e) - 4);}, $files);
  return $files;
}

function highlight($content, $lang)
{
  global $langs;
  
  if(!is_array($content))
  {
    $content = explode("\n", $content);
    $lang = detectLang($content);
    $func = 'highlight'.$langs[$lang]['name'];
    $content = $func($content, $lang);
    $content = implode("\n", $lines);
    
    return $content;
  }
  $lang = detectLang($content);
  $func = 'highlight'.$langs[$lang]['name'];
  $content = $func($content, $lang);
  return $content;
}

function detectLang($content)
{
  return Generic;
  
  //global $langs;
  
  //$prop = array();
  
  //foreach($content as &$line)
  //{
    //$line = splitLine($line);
  //}
  //unset($line);
  
  ////count all elements
  //$elcount = 0;
  //foreach($content as $line)
  //{
    //$elcount += count($line);
  //}

  ////count per lang
  //$langcounts = array();
  //$langwordcounts = array();
  //foreach($langs as $lang => $data)
  //{
    //if($lang == None) {continue;}
    //foreach($content as $line)
    //{
      //foreach($line as $el)
      //{
        //$e = strtolower($el);
        //if(in_array($e, $data['keywords']) || in_array($e, $data['types']) || in_array($e, $data['preprocessor']) || in_array($e, $data['special']))
        //{
          //$langcounts[$lang] += 1;
        //}
      //}
    //}
    //$langcounts[$lang] = count(array_merge($data['keywords'], $data['types'], $data['preprocessor'], $data['special']));
  //}
  
  //var_dump($langcounts);
  
  //$lang = None;
  //$max = 0;
  //foreach($langcounts as $name => $p)
  //{
    //$t = $p / $elcount;
    //echo '<br />'.$name.': '.$t."\n";
    //if($max < $t)// && $t > 0.025)
    //{
      //$max = $t;
      //$lang = $name;
    //}
  //}
  //echo $lang;
  //return $lang;
}

function highlightNone($content, $lang)
{
  return $content;
}
function highlightGeneric($lines, $lang)
{
  global $langs;
  
  //~ var_dump($langs[$lang]);
  
  $inslcomment = false;
  $inmlcomment = false;
  $inmlpcomment = false;
  $insqstring = false;
  $indqstring = false;
  
  foreach($lines as &$line)
  {
    $wasmlcomment = $inmlcomment;
    $wasmlpcomment = $inmlpcomment;
    
    $elements = splitLine($line);
    foreach($elements as &$e)
    {
      if($e == ' ') continue;
      
      //comments
      if( $inmlcomment && $e == '*/') {$e = $e.'</span>'; $inmlcomment = false; continue;}
      else if( $inmlcomment && $e != '*/') {continue;}
      if(!$inmlcomment && $e == '/*') {$e = '<span class="comment">'.$e; $inmlcomment = true; continue;}
      
      if( $inmlpcomment && $e == '"""') {$e = $e.'</span>'; $inmlpcomment = false; continue;}
      else if( $inmlpcomment && $e != '"""') {continue;}
      if(!$inmlpcomment && $e == '"""') {$e = '<span class="comment">'.$e; $inmlpcomment = true; continue;}
      
      if(!$inmlcomment && $e == '//') {$e = '<span class="comment">'.$e; $inslcomment = true; break;}
      
      //strings
      if( $insqstring && $e == "'") {$e = $e.'</span>'; $insqstring = false; continue;}
      else if( $insqstring && $e != "'") {continue;}
      if(!$insqstring && $e == "'") {$e = '<span class="string">'.$e; $insqstring = true; continue;}
      
      if( $indqstring && $e == '"') {$e = $e.'</span>'; $indqstring = false; continue;}
      else if( $indqstring && $e != '"') {continue;}
      if(!$indqstring && $e == '"') {$e = '<span class="string">'.$e; $indqstring = true; continue;}

      
      if(isOperator($e)) {$e = '<span class="operator">'.$e.'</span>';}
      if(isBracket($e)) {$e = '<span class="brackets">'.$e.'</span>';}
      if(isNumber($e)) {$e = '<span class="number">'.$e.'</span>';}
      if(in_array(strtolower($e), $langs[$lang]['keywords'])) { $e = '<span class="keyword">'.$e.'</span>'; }
      if(in_array(strtolower($e), $langs[$lang]['types'])) { $e = '<span class="type">'.$e.'</span>'; }
      if(in_array(strtolower($e), $langs[$lang]['preprocessor'])) { $e = '<span class="preprocessor">'.$e.'</span>'; }
      if(in_array(strtolower($e), $langs[$lang]['special'])) { $e = '<span class="special">'.$e.'</span>'; }
      
      if($e == '<') {$e = '&lt;';} //error here
      if($e == '>') {$e = '&gt;';}
    }
    unset($e);
    
    $line = implode('', $elements);
    
    //comments
    if($wasmlcomment || $wasmlpcomment)
    {
      $line = '<span class="comment">'.$line;
    }
    
    if($inslcomment)
    {
      $line .= '</span>';
      $inslcomment = false;
    }
    if($inmlcomment)
    {
      $line .= '</span>';
    }
    
    //strings
    if($insqstring)
    {
      $line .= '</span>';
      $insqstring = false;
    }
    if($indqstring)
    {
      $indqstring = false;
      $line .= '</span>';
    }
  }
  unset($line);
  
  
  return $lines;
}
function highlightC($content, $lang)
{
  return highlightGeneric($content, $lang);
}
function highlightCPlusPlus($content, $lang)
{
  return highlightGeneric($content, $lang);
}
function highlightJava($content, $lang)
{
  return highlightGeneric($content, $lang);
}
function highlightPHP($content, $lang)
{
  return highlightGeneric($content, $lang);
}
function highlightPython($content, $lang)
{
  return highlightGeneric($content, $lang);
}
function highlightWhatever($content, $lang)
{
  return highlightGeneric($content, $lang);
}

function checkStartBraces($word, $pre)
{
  return startsWith($word, $pre.'(') ||
         startsWith($word, $pre.')') ||
         startsWith($word, $pre.'[') ||
         startsWith($word, $pre.']') ||
         startsWith($word, $pre.'}') ||
         startsWith($word, $pre.';') ||
         startsWith($word, $pre.':') ||
         startsWith($word, $pre.'?');
}
function checkEndBraces($word, $pre)
{
  return endsWith($word, '('.$pre) ||
         endsWith($word, ')'.$pre) ||
         endsWith($word, '['.$pre) ||
         endsWith($word, ']'.$pre) ||
         endsWith($word, '}'.$pre) ||
         endsWith($word, ';'.$pre) ||
         endsWith($word, ':'.$pre) ||
         endsWith($word, '?'.$pre);
}

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function removehtml($html)
{
  return $html;
}

function splitLine($line)
{
  $splitter = array(' ', '(', ')', '[', ']', '{', '}', '?', ':', ';', ',', '*', "'", '"', '>', '<', '!', '=', '+', '-', '*', '/', '%', '&', '|');
  $splitter2 = array('//', '/*', '*/', '\'', '\"', '>=', '<=', '!=', '==', '+=', '-=', '*=', '/=', '%=', '<<', '>>', '&&', '||', '&=', '|=', '++', '--');
  $splitter3 = array('===', '!==', '"""', "\'\'\'", '\"', '>>=', '<<=');
  $splitter4 = array('>>>=', '<<<=');
  
  $ret = array();
  
  $element = '';
  for($i = 0; $i < strlen($line); $i++)
  {
    $c = substr($line, $i, 1);
    $c2 = substr($line, $i, min(2, strlen($line) - $i));
    $c3 = substr($line, $i, min(3, strlen($line) - $i));
    $c4 = substr($line, $i, min(4, strlen($line) - $i));
    if(in_array($c4, $splitter4))
    {
      if(strlen($element) > 0)
      {
        $ret[] = $element;
      }
      $ret[] = $c4;
     
      $element = '';
      $i += 3; // since we check 4 chars
      continue;
    }
    else if(in_array($c3, $splitter3))
    {
      if(strlen($element) > 0)
      {
        $ret[] = $element;
      }
      $ret[] = $c3;
     
      $element = '';
      $i += 2; // since we check 3 chars
      continue;
    }
    else if(in_array($c2, $splitter2))
    {
      if(strlen($element) > 0)
      {
        $ret[] = $element;
      }
      $ret[] = $c2;
     
      $element = '';
      $i++; // since we check 2 chars
      continue;
    }
    else if(in_array($c, $splitter))
    {
      if(strlen($element) > 0)
      {
        $ret[] = $element;
      }
      $ret[] = $c;
      $element = '';
      continue;
    }
    
    $element .= $c;
  }
  
  if(strlen($element) > 0)
  {
    $ret[] = $element;
  }
  
  return $ret;
}

function isBracket($str)
{
  $brackets = array('(', ')', '[', ']', '{', '}', '?', ':', ';', ',');
  return in_array($str, $brackets);
}
function isOperator($str)
{
  $operators = array
  (
    '!', '=', '+', '-', '*', '/', '%', '&', '|', '<', '>',
    '>=', '<=', '!=', '==', '+=', '-=', '*=', '/=', '%=', '<<', '>>', '&&', '||', '&=', '|=', '++', '--',
    '===', '!==', '>>=', '<<=',
    '>>>=', '<<<='
  );
  return in_array($str, $operators);
}
function isNumber($str)
{
  if(strlen($str) > 1 && (endsWith($str, 'd') || endsWith($str, 'D') || endsWith($str, 'f') || endsWith($str, 'F')))
  {
    $str = substr($str, 0, strlen($str) - 1);
  }
  
  if(startsWith($str, '0x')) {return preg_match('#^[0-9a-fA-F]*$#', substr($str, 2));}
  if(startsWith($str, '0b')) {return preg_match('#^[0-1]*$#', substr($str, 2));}
  if(startsWith($str, '0'))  {return preg_match('#^[0-7]*$#', substr($str, 1));}
  
  $expused = false;
  $dotused = false;
  
  for($i = 0; $i < strlen($str); $i++)
  {
    $c = substr($str, $i, 1);
    
    $isdigit = false;
    for($j = 0; $j < 10; $j++)
    {
      if($c == (string)$j)
      {
        $isdigit = true;
        break;
      }
    }
    if($isdigit)
    {
      continue;
    }
    else
    {
      return false;
    }
    
    if($c == 'e')
    {
      if($expused) return false;
      else $expused = true;
    }
    
    if($c == '.')
    {
      if($dotused) return false;
      else $dotused = true;
    }
  }
  
  return true;
}
?>
