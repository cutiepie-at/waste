$(document).ready(function()
{
  $('#pastebox').focus();
  
  var save = function()
  {
    pastecontent = $('#pastebox').val();
    $.ajax({
      type: 'POST',
      url: '',
      data: {"action": "save", "content": pastecontent},
      success: function(msg)
      {
        if(msg.startsWith('Error:'))
        {          
          alert(msg);
        }
        else
        {
          $(location).attr('href', msg);
        }
      }
    });
  };
  
  //key events
  $(window).keydown(function(e)
  {
    if((e.ctrlKey && e.keyCode == 78) || e.keyCode == 113)
    {
      e.preventDefault();
      var win = window.open('/' , '_blank');
      win.focus();
    }
    else if(e.ctrlKey && e.keyCode == 83)
    {
      e.preventDefault();
      if(document.getElementById('optionsave') == null)
        return;
      save();
    }
  });
  
  
  //box toggling
  $('#boxtoggle').click(function()
  {
    if($(this).html() == "↑")
    {
      $(".boxcontainer").css('display', 'none');
      $(this).css("top", "0em");
      $(this).html("↓");
    }
    else
    {
      $(".boxcontainer").css('display', 'block');
      $(this).css("top", "5.75em");
      $(this).html("↑");
    }
    
    return false;
  });
  
  //option buttons
  $('#optionsave').click(function()
  {
    save();
    return false;
  });
  
  $('#optionnew').click(function()
  {
    $(location).attr('href','/');
  });
  
  $('#optionlang').change(function()
  {
    console.log('change');
    var pos = window.location.pathname.indexOf('/',1);
    if(pos < 0)
      pos = window.location.pathname.length;
    if($(this).val() == '(none)')
      $(location).attr('href', window.location.pathname.substring(0, pos));
    else
      $(location).attr('href', window.location.pathname.substring(0, pos) + "/" + $(this).val());
  });
  
  //disable box and line number selection
  $.fn.extend(
  {
    disableSelection: function() 
    { 
      this.each(function() 
      { 
        if (typeof this.onselectstart != 'undefined') 
        {
          this.onselectstart = function() { return false; };
        } 
        else if (typeof this.style.MozUserSelect != 'undefined') 
        {
          this.style.MozUserSelect = 'none';
        }
        else
        {
          this.onmousedown = function() { return false; };
        }
      });
    } 
  });
  
  //string startwith impl
  if ( typeof String.prototype.startsWith != 'function' ) 
  {
    String.prototype.startsWith = function( str ) 
    {
      return this.substring( 0, str.length ) === str;
    }
  };
});
