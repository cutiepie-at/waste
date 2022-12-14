<?php
include_once('syntaxhighlighter.php');
include_once('geshi.php');

$docpath = "docs/";
$randomchars = "abcdefghijklmnopqrstuvwxyz0123456789";
$filenamelength = 16;
$fileextension = '.paste';
$maxcontentlength = 1024 * 1024;
$usegeshi = true;


//xhtml utf8 header
//header('Content-type: application/xhtml+xml;charset=utf-8');

if(count($_POST) > 0)
{
  //check for valid post
  if(!isset($_POST['action']))
  {
    return;
  }
  
  //handle
  $action = $_POST['action'];
  switch($action)
  {
    case 'save':
      $content = $_POST['content'];
      $content = str_replace("\r\n", "\n", $content); //replace windows ls
      $content = str_replace("\r", "\n", $content);   //replace macintosh ls
      
      //check contents
      if(strlen($content) > $maxcontentlength)
      {
        echo 'Error: Maximum length is '.$maxcontentlength.'! (was '.strlen($content).')';
        return;
      }
    
      //get new filename
      do
      {
        $pastename = getRandomName();
        $filename = $pastename.$fileextension;
      } while(file_exists($docpath.$filename));
      
      //save
      file_put_contents($docpath.$filename, $content);
      
      //send new url
      echo $pastename;
      
      break;
    default:
      echo 'Error: Unsupported operation! IP logged.';
      break;
  }
  
  return;
}

if(isset($_GET["f"]))
{
  //var_dump($_GET);
  //echo $_SERVER['REQUEST_URI'];
  $doc = $_GET["f"];
  $lang = isset($_GET["l"]) ? $_GET["l"] : null;
  //var_dump($lang);
  
  //valid check
  if(!preg_match('#(a-z0-9)*#', $doc))
  {
    printHeader(false, 'Error', false, null);
    error('Unsupported operation! IP logged.');
    return;
  }
  
  $file = $docpath.$doc.$fileextension;
  if(file_exists($file))
  {
    //content
    $content = readTextFileFully($file);
    if ($lang == null)
      $lang = detectlangbin($file);
    else if($lang == '(none)')
      $lang = null;
    
    //header
    printHeader(false, $doc, false, $lang);
    
    printPaste($content, $file, $lang);
  }
  else
  {
    printHeader(false, 'Error', false, null);
    error('The requested paste does not exist!');
  }
}
else
{
  printHeader(true, 'New waste paste', true, null);
  
  echo '
    <textarea class="linenums"></textarea>
    <textarea id="pastebox" class="codeline" spellcheck="false"></textarea>';
}
printFooter();







function printHeader($editmode, $title, $isnew, $sellang)
{ // TODO noselect for rightupperbox
  echo '
<html>
  <head>
    <meta charset="utf-8" />
    <title>'.($title != null ? $title.' - ' : '').'Wastebin</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="/script.js"></script>
    <link href="/style.css" type="text/css" rel="stylesheet" />
    <link href="/hightlight.css" type="text/css" rel="stylesheet" />
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <link href="/favicon.ico" rel="icon" type="image/x-icon" />
  </head>
  <body>
    <div class="rightupperedge noselect">
      <div class="boxcontainer"><div class="box">
        <img class="wastelogo" alt="wastebin" src="/wastelogo.png"/>
        <ul>
          <li><img id="optionnew" class="boxoption" alt="new" title="New | F2 or Ctrl+n" src="/new.png" /></li>';
          if($editmode)
            echo '<li><img id="optionsave" class="boxoption" alt="save" title="Save | Ctrl+s" src="/save.png" /></li>';
          echo '<li><a href="http://qbnz.com/highlighter" title="Thanks GeSHi!"><img class="boxoption" src="/powered-by-geshi.png" alt="Powered by GeSHi" /></a></li>';
          if (!$isnew)
          {
            echo '<li><select id="optionlang" class="boxoption"><option'.($sellang == null ? ' selected' : '').'>(none)</option>';
              foreach(supportedlangs() as $lang)
                echo '<option'.($sellang == $lang ? ' selected' : '').'>'.$lang.'</option>';
            echo '</select></li>';
          }
          echo '
        </ul>
      </div></div>
      <div id="boxtoggle" class="boxtoggle" style="text-align:center">↑</div>
    </div>
  ';
}
function printFooter()
{
  echo "
  </body>
</html>";
}

function getRandomName()
{
  global $randomchars, $filenamelength;
  $ret = "";
  for($i = 0; $i < $filenamelength; $i++)
  {
    $ret .= substr($randomchars, rand(0, strlen($randomchars)), 1);
  }
  return $ret;
}

function error($msg)
{
  echo '<div class="pastebox">';
  echo '<aside class="error noselect">!</aside>
        <p class="error line">'.$msg.'</p><br />';
  echo '</div>';
}

function printPaste($paste, $file, $sellang)
{
  global $usegeshi;
  
  $lines = explode("\n", $paste);
  $linescount = count($lines);
  
  if($usegeshi)
  {
    $lines = highlightGeShi($lines, $file, $sellang);
    $linescount = count($lines);
  }
  else
    $lines = highlight($lines, $sellang);
  
  echo '<div class="pastebox">';
  
  for($i = 0; $i < $linescount; $i++)
  {
    echo '<aside class="noselect">';
    for($j = 0; $j < strlen((string)$linescount) - strlen((string)($i + 1)); $j++)
    {
      echo "&nbsp;";
    }
    
    $line = $lines[$i];
    $line = str_replace('	', '    ', $line);
    $line = replaceSpaces($line);
    
    echo ($i + 1).'</aside><span class="line">'.$line.'</span><br />'."\n";
  }
  
  echo '</div>';
  echo '
  <script>
    $(document).ready(function()
    {
      $(".line").css();
    });

  </script>';
}

function detectlangbin($file)
{
  //detect with file
  $out = shell_exec('file '.$file);
  $langindicatorsfile = array(
    'java' => 'Java source',
    'c' => 'C source',
    'python' => 'Python script',
    'c++' => 'C++ source',
    'html' => 'HTML document',
    'php' => 'PHP script',
    'xml' => 'XML');
  foreach($langindicatorsfile as $key => $value)
  {
    if(mb_str_contains($out, $value))
    {
      return $key;
    }
  }

  //detect with guesslang
  $out = shell_exec('guesslang -i '.$file.' 2>/dev/null');
  $out = preg_replace('/\x1b\[[0-9;]*m/u', '', $out);
  $i = mb_strpos($out, 'The source code is written in');
  if($i !== false)
  {
    $out = substr($out, $i);
    $out = substr($out, strlen('The source code is written in '));
    $out = explode(' ', $out)[0];
    //echo 'guesslang lang: '.$out;
    $langindicatorsguesslang = array(
      'java' => 'Java',
      'sql' => 'SQL',
      'javascript' => 'Javascript');
    foreach($langindicatorsguesslang as $key => $value)
    {
      if(str_contains($out, $value))
      {
        return $key;
      }
    }
  }
  
  $content = readTextFileFully($file);
  $lines = explode("\n", $content);
  $first = $lines[0];
  $second = count($lines) > 1 ? $lines[1] : null;
  if(preg_match('/(\[?[0-1][0-9]:[0-5][0-9]:([0-5][0-9])?\]?) (\[.*\]) (.*)/', $first) 
    || ($second != null && preg_match('/(\[?[0-1][0-9]:[0-5][0-9]:([0-5][0-9])?) (\[.*\]) (.*)/', $second)))
  {
    return 'log';
  }
  return null;
}

function highlightGeShi($lines, $file, $lang)
{
  if($lang != null && $lang != 'log')
  {
    //echo "geshi";
    $geshi = new GeSHi(implode("\n", $lines), $lang);
    $geshi->set_header_type(GESHI_HEADER_NONE);
    $geshi->enable_multiline_span(false);
    $geshi->set_line_ending("\n");
    $geshi->enable_classes();
    $lines = explode("\n", $geshi->parse_code());
  }
  else if($lang == 'log')
  {
    //echo "log";
    foreach($lines as &$line)
    {
      $line = preg_replace_callback('/(\[?[0-1][0-9]:[0-5][0-9]:([0-5][0-9])?) (\[.*\]) (.*)/', 
                          function($arr) {return $arr[1].' '.$arr[3].' '.unescapemccolors($arr[4]);},
                          $line);
    }
    unset($line);
  }
  else
  {
    //echo "none";
    $lines = array_map(function($e) {return str_replace('<', '&lt;', str_replace('>', '&gt;', $e));}, $lines);
  }
  
  return $lines;
}

function unescapemccolors($str)
{
  $close = 0;
  $ret = '';
  for($i = 0; $i < mb_strlen($str) - 1; $i++)
  {
    if(mb_substr($str, $i, 1) == "§")
    {
      $code = mb_substr($str, $i + 1, 1);
      if(mb_ereg_match('[0-9a-fA-Fl-oL-O]', $code))
      {
        $ret .= '<span class="mccolor'.mb_convert_case($code, MB_CASE_UPPER, "UTF-8").'">';
        $close += 1;
        $i++;
      }
      else if(mb_ereg_match('[kK]', $code))
      {
        //ignore §k
        $i++;
      }
      else if(mb_ereg_match('[rR]', $code))
      {
        for($i = 0; $i < $close; $i++)
          $ret .= '</span>';
        $close = 0;
        $i++;
      }
      else
      {
        //ignore
        $i++;
      }
    }
    else
      $ret .= mb_substr($str, $i, 1);

    //echo $ret;
  }
  if($i < mb_strlen($str))
  {
    $ret .= mb_substr($str, $i, 1);
  }
  for($i = 0; $i < $close; $i++)
    $ret .= '</span>';
  return $ret;
}

function mb_str_contains($haystack, $needle)
{
  return mb_strpos($haystack, $needle) !== false;
}

//function str_contains($haystack, $needle)
//{
//  return strpos($haystack, $needle) !== false;
//}

function replaceTrailingSpaces($str)
{
  $ret = '';
  
  $i = 0;
  for(; $i < mb_strlen($str) && (mb_substr($str, $i, 1) == ' '); $i++);
  for($j = 0; $j < $i; $j++)
  {
    $ret.='&nbsp;';
  }
  return $ret.mb_substr($str, $i);
}

function replaceSpaces($str)
{
  $allowreplace = true;
  for($i = 0; $i < mb_strlen($str); $i++)
  {
    $c = mb_substr($str, $i, 1);
    if($c == ' ' && $allowreplace)
    {
      $str = mb_substr($str, 0, $i).'&nbsp;'.mb_substr($str, $i + 1);
      $i += 5;
    }
    else if($c == '<')
    {
      $allowreplace = false;
    }
    else if($c == '>')
    {
      $allowreplace = true;
    }
  }
  
  return $str;
}

function readTextFileFully($path)
{
  $str = '';
  $f = fopen($path, 'r');
  while(!feof($f))
    $str .= fgets($f, 5000);
  fclose($f);
  return mb_convert_encoding($str, "UTF-8", "auto");
}
?>
