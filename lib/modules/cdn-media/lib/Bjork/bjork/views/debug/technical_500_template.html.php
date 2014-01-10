<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="robots" content="NONE,NOARCHIVE">
  
  <title><?php if (isset($exception_type)):?><?=$self['exception_type']?><?php else: ?>Report<?php endif; ?><?php if (isset($request)): ?> at <?=$self->escape($request->getPathInfo())?><?php endif; ?></title>
  <style type="text/css">
    html * { padding:0; margin:0; }
    body * { padding:10px 20px; }
    body * * { padding:0; }
    body { font:small sans-serif; }
    body>div { border-bottom:1px solid #ddd; }
    h1 { font-weight:normal; }
    h2 { margin-bottom:.8em; }
    h2 span { font-size:80%; color:#666; font-weight:normal; }
    h3 { margin:1em 0 .5em 0; }
    h4 { margin:0 0 .5em 0; font-weight: normal; }
    code, pre { font-size: 100%; white-space: pre-wrap; }
    table { border:1px solid #ccc; border-collapse: collapse; width:100%; background:white; }
    tbody td, tbody th { vertical-align:top; padding:2px 3px; }
    thead th { padding:1px 6px 1px 3px; background:#fefefe; text-align:left; font-weight:normal; font-size:11px; border:1px solid #ddd; }
    tbody th { width:12em; text-align:right; color:#666; padding-right:.5em; }
    table.vars { margin:5px 0 2px 40px; }
    table.vars td, table.req td { font-family:monospace; }
    table td.code { width:100%; }
    table td.code pre { overflow:hidden; }
    table.source th { color:#666; }
    table.source td { font-family:monospace; white-space:pre; border-bottom:1px solid #eee; }
    ul.traceback { list-style-type:none; color: #222; }
    ul.traceback li.frame { padding:0.4em 0.3em 0.5em;/*padding-bottom:1em;*/ color:#666; }
    ul.traceback li.user { background-color:#e0e0e0; color:#000 }
    div.context { padding:10px 0; overflow:hidden; }
    div.context ol { padding-left:30px; margin:0 10px; list-style-position: inside; }
    div.context ol li { font-family:monospace; white-space:pre; color:#777; cursor:pointer; }
    div.context ol li pre { display:inline; }
    div.context ol.context-line li { color:#505050; background-color:#dfdfdf; }
    div.context ol.context-line li span { position:absolute; right:32px; }
    .user div.context ol.context-line li { background-color:#bbb; color:#000; }
    .user div.context ol li { color:#666; }
    div.commands { margin-left: 40px; }
    div.commands a { color:#555; text-decoration:none; }
    .user div.commands a { color: black; }
    #summary { background: #ffc; }
    #summary h2 { font-weight: normal; color: #666; }
    #explanation { background:#eee; }
    #template, #template-not-exist { background:#f6f6f6; }
    #template-not-exist ul { margin: 0 0 0 20px; }
    #unicode-hint { background:#eee; }
    #traceback { background:#eee; }
    #requestinfo { background:#f6f6f6; padding-left:120px; }
    #summary table { border:none; background:transparent; }
    #requestinfo h2, #requestinfo h3 { position:relative; margin-left:-100px; }
    #requestinfo h3 { margin-bottom:-1em; }
    .error { background: #ffc; }
    .specific { color:#cc3300; font-weight:bold; }
    h2 span.commands { font-size:.7em;}
    span.commands a:link {color:#5E5694;}
    pre.exception_value { font-family: sans-serif; color: #666; font-size: 1.5em; margin: 10px 0 10px 0; }
  </style>
  <?php if (!$is_email): ?>
  <script type="text/javascript">
  //<!--
    function getElementsByClassName(oElm, strTagName, strClassName){
        // Written by Jonathan Snook, http://www.snook.ca/jon; Add-ons by Robert Nyman, http://www.robertnyman.com
        var arrElements = (strTagName == "*" && document.all)? document.all :
        oElm.getElementsByTagName(strTagName);
        var arrReturnElements = new Array();
        strClassName = strClassName.replace(/\-/g, "\\-");
        var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
        var oElement;
        for(var i=0; i<arrElements.length; i++){
            oElement = arrElements[i];
            if(oRegExp.test(oElement.className)){
                arrReturnElements.push(oElement);
            }
        }
        return (arrReturnElements)
    }
    function hideAll(elems) {
      for (var e = 0; e < elems.length; e++) {
        elems[e].style.display = 'none';
      }
    }
    window.onload = function() {
      hideAll(getElementsByClassName(document, 'table', 'vars'));
      hideAll(getElementsByClassName(document, 'ol', 'pre-context'));
      hideAll(getElementsByClassName(document, 'ol', 'post-context'));
      hideAll(getElementsByClassName(document, 'div', 'pastebin'));
    }
    function toggle() {
      for (var i = 0; i < arguments.length; i++) {
        var e = document.getElementById(arguments[i]);
        if (e) {
          e.style.display = e.style.display == 'none' ? 'block' : 'none';
        }
      }
      return false;
    }
    function varToggle(link, id) {
      toggle('v' + id);
      var s = link.getElementsByTagName('span')[0];
      var uarr = String.fromCharCode(0x25b6);
      var darr = String.fromCharCode(0x25bc);
      s.innerHTML = s.innerHTML == uarr ? darr : uarr;
      return false;
    }
    function switchPastebinFriendly(link) {
      s1 = "Switch to copy-and-paste view";
      s2 = "Switch back to interactive view";
      link.innerHTML = link.innerHTML == s1 ? s2 : s1;
      toggle('browserTraceback', 'pastebinTraceback');
      return false;
    }
    //-->
  </script>
  <?php endif; ?>
</head>
<body>
<div id="summary">
  <h1><?php if (isset($exception_type)):?><?=$self['exception_type']?><?php else: ?>Report<?php endif; ?><?php if (isset($request)): ?> at <?=$self->escape($request->getPathInfo())?><?php endif; ?></h1>
  <pre class="exception_value"><?php if (isset($exception_value)):?><?=$self->force_escape($exception_value)?><?php else: ?>No exception supplied<?php endif; ?></pre>
  <table class="meta">
<?php if (isset($request)): ?>
    <tr>
      <th>Request Method:</th>
      <td><?=$request->getMethod()?></td>
    </tr>
    <tr>
      <th>Request URL:</th>
      <td><?=$self->escape($request->buildAbsoluteURI())?></td>
    </tr>
<?php endif; ?>
    <tr>
      <th>Bjork Version:</th>
      <td><?=$bjork_version?></td>
    </tr>
<?php if (isset($exception_type)): ?>
    <tr>
      <th>Exception Type:</th>
      <td><?=$exception_type?></td>
    </tr>
<?php endif; ?>
<?php if (isset($exception_type) && isset($exception_value)): ?>
    <tr>
      <th>Exception Value:</th>
      <td><pre><?=$self->force_escape($exception_value)?></pre></td>
    </tr>
<?php endif; ?>
<?php if (isset($lastframe)): ?>
    <tr>
      <th>Exception Location:</th>
      <td><?=$self->escape($lastframe['filename'])?> in <?=$self->escape($lastframe['function'])?>, line <?=$lastframe['lineno']?></td>
    </tr>
<?php endif; ?>
    <tr>
      <th>PHP SAPI Name:</th>
      <td><?=$self->escape($sys_sapi_name)?></td>
    </tr>
    <tr>
      <th>PHP Version:</th>
      <td><?=$self->escape($sys_version)?></td>
    </tr>
    <tr>
      <th>PHP Path:</th>
      <td><pre><?=implode("\n", $sys_path)?></pre></td>
    </tr>
    <tr>
      <th>Server time:</th>
      <td><?=$server_time->format('r')?></td>
    </tr>
  </table>
</div>
<?php if ($template_does_not_exist): ?>
<div id="template-not-exist">
    <h2>Template-loader postmortem</h2>
    <?php if ($loader_debug_info): ?>
        <p>Bjork tried loading these templates, in this order:</p>
        <ul>
          <?php foreach ($loader_debug_info as $loader): ?>
            <li>Using loader <code><?=$loader['loader']?>:</code>
              <ul>
                <?php foreach ($loader['templates'] as $t): ?>
                <li><code><?=$t['name']?></code> (File <?php if ($t['exists']): ?>exists<?php else: ?>does not exist<?php endif ?>)</li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endforeach ?>
        </ul>
    <?php else: ?>
        <p>Bjork couldn't find any templates because your <code>TEMPLATE_LOADERS</code> setting is empty!</p>
    <?php endif ?>
</div>
<?php endif; ?>
<?php if ($template_info): ?>
<div id="template">
   <h2>Error during template rendering</h2>
   <p>In template <code><?=$template_info['name']?></code>, error at line <strong><?=$template_info['line']?></strong></p>
   <h3><?=$template_info['message']?></h3>
   <?php if (false): ?>
   <table class="source{% if template_info.top %} cut-top{% endif %}{% ifnotequal template_info.bottom template_info.total %} cut-bottom{% endifnotequal %}">
   {% for source_line in template_info.source_lines %}
   {% ifequal source_line.0 template_info.line %}
       <tr class="error"><th>{{ source_line.0 }}</th>
       <td>{{ template_info.before }}<span class="specific">{{ template_info.during }}</span>{{ template_info.after }}</td></tr>
   {% else %}
      <tr><th>{{ source_line.0 }}</th>
      <td>{{ source_line.1 }}</td></tr>
   {% endifequal %}
   {% endfor %}
   </table>
   <?php endif ?>
</div>
<?php endif; ?>
<?php if ($frames): ?>
<div id="traceback">
  <h2>Traceback <span class="commands"><?php if (!$is_email): ?><a href="#" onclick="return switchPastebinFriendly(this);">Switch to copy-and-paste view</a></span><?php endif; ?></h2>
  <!-- {% autoescape off %} -->
  <div id="browserTraceback">
    <ul class="traceback">
      <?php foreach ($frames as $frame): ?>
        <li class="frame <?=$frame['type']?>">
          <code><?=$self->escape($frame['filename'])?> (line <?=$frame['lineno']?>)</code> in <code><?=$self->escape($frame['function'])?>(<?=implode(', ', array_map('htmlentities', $frame['funcargs']))?>)</code>
          
          <?php if (false): ?>
          {% if frame.context_line %}
            <div class="context" id="c{{ frame.id }}">
              {% if frame.pre_context and not is_email %}
                <ol start="{{ frame.pre_context_lineno }}" class="pre-context" id="pre{{ frame.id }}">{% for line in frame.pre_context %}<li onclick="toggle('pre{{ frame.id }}', 'post{{ frame.id }}')"><pre>{{ line|escape }}</pre></li>{% endfor %}</ol>
              {% endif %}
              <ol start="{{ frame.lineno }}" class="context-line"><li onclick="toggle('pre{{ frame.id }}', 'post{{ frame.id }}')"><pre>{{ frame.context_line|escape }}</pre>{% if not is_email %} <span>...</span>{% endif %}</li></ol>
              {% if frame.post_context and not is_email  %}
                <ol start='{{ frame.lineno|add:"1" }}' class="post-context" id="post{{ frame.id }}">{% for line in frame.post_context %}<li onclick="toggle('pre{{ frame.id }}', 'post{{ frame.id }}')"><pre>{{ line|escape }}</pre></li>{% endfor %}</ol>
              {% endif %}
            </div>
          {% endif %}
          <?php endif ?>
          
          <?php if (false): ?>
          {% if frame.vars %}
            <div class="commands">
                {% if is_email %}
                    <h2>Local Vars</h2>
                {% else %}
                    <a href="#" onclick="return varToggle(this, '{{ frame.id }}')"><span>&#x25b6;</span> Local vars</a>
                {% endif %}
            </div>
            <table class="vars" id="v{{ frame.id }}">
              <thead>
                <tr>
                  <th>Variable</th>
                  <th>Value</th>
                </tr>
              </thead>
              <tbody>
                {% for var in frame.vars|dictsort:"0" %}
                  <tr>
                    <td>{{ var.0|force_escape }}</td>
                    <td class="code"><pre>{{ var.1 }}</pre></td>
                  </tr>
                {% endfor %}
              </tbody>
            </table>
          {% endif %}
          <?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
  <!-- {% endautoescape %} -->
  <?php if (!$is_email): ?>
  <form <?php if (false): ?>action="http://dpaste.com/"<?php endif ?> name="pasteform" id="pasteform" method="post">
  <div id="pastebinTraceback" class="pastebin"><?php if (false): ?>
    <input type="hidden" name="language" value="PythonConsole">
    <input type="hidden" name="title" value="<?=$self->escape($exception_type)?><?php if (isset($request)): ?> at <?=$self->escape($request->getPathInfo())?><?php endif; ?>">
    <input type="hidden" name="source" value="Bjork Dpaste Agent">
    <input type="hidden" name="poster" value="Bjork"><?php endif ?>
    <textarea name="content" id="traceback_area" cols="140" rows="25">
Environment:

<?php if (isset($request)): ?>
Request Method: <?=$request->getMethod()?> 
Request URL: <?=$self->escape($request->buildAbsoluteURI())?> 
<?php endif; ?>
Bjork Version: <?=$bjork_version?> 
PHP Version: <?=$self->escape($sys_version)?> 
Installed Applications:
  <?=implode("\n  ", $settings['INSTALLED_APPS'])?> 
Installed Middleware:
  <?=implode("\n  ", $settings['MIDDLEWARE_CLASSES'])?> 

<?php if ($template_does_not_exist): ?>Template Loader Error:
<?php if ($loader_debug_info): ?>Bjork tried loading these templates, in this order:
<?php foreach ($loader_debug_info as $loader): ?>Using loader <?=$loader['loader']?>:
<?php foreach ($loader['templates'] as $t): ?><?=$t['name']?> (File <?php if ($t['exists']): ?>exists<?php else: ?>does not exist<?php endif ?>)
<?php endforeach; ?><?php endforeach; ?>
<?php else: ?>Bjork couldn't find any templates because your TEMPLATE_LOADERS setting is empty!
<?php endif ?>
<?php endif ?><?php if ($template_info): ?>
Template error:
In template <?=$template_info['name']?>, error at line <?=$template_info['line']?>
   <?=$template_info['message']?><?php if (false): ?>{% for source_line in template_info.source_lines %}{% ifequal source_line.0 template_info.line %}
   {{ source_line.0 }} : {{ template_info.before }} {{ template_info.during }} {{ template_info.after }}
{% else %}
   {{ source_line.0 }} : {{ source_line.1 }}
{% endifequal %}{% endfor %}<?php endif ?>{% endif %}<?php endif ?>
Traceback:
<?php foreach ($frames as $frame): ?>
  File "<?=$self->escape($frame['filename'])?>":<?=$frame['lineno']?> in <?=$self->escape($frame['function'])?>(<?=implode(', ', array_map('htmlentities', $frame['funcargs']))?>)<?php if (false): ?>
  {% if frame.context_line %}  {{ frame.lineno }}. {{ frame.context_line|escape }}{% endif %}<?php endif ?> 
<?php endforeach ?>
Exception Type: <?=$self->escape($exception_type)?><?php if (isset($request)): ?> at <?=$self->escape($request->getPathInfo())?><?php endif; ?> 
Exception Value: <?=$self->force_escape($exception_value)?> 
</textarea>
  <br><br>
  <?php if (false): ?><input type="submit" value="Share this traceback on a public Web site"><?php endif ?>
  </div>
</form>
<?php endif; ?>
</div>
<?php endif; ?>

<div id="requestinfo">
  <h2>Request information</h2>

<?php if (isset($request)): ?>
  <h3 id="get-info">GET</h3>
  <?php if (count($request->GET)): ?>
    <table class="req">
      <thead>
        <tr>
          <th>Variable</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($request->GET as $key => $value): ?>
          <tr>
            <td><?=$key?></td>
            <td class="code"><pre><?=$self->escape($self->pprint($value))?></pre></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No GET data</p>
  <?php endif; ?>

  <h3 id="post-info">POST</h3>
  <?php if (count($request->POST)): ?>
    <table class="req">
      <thead>
        <tr>
          <th>Variable</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($request->POST as $key => $value): ?>
          <tr>
            <td><?=$key?></td>
            <td class="code"><pre><?=$self->escape($self->pprint($value))?></pre></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No POST data</p>
  <?php endif; ?>
  <h3 id="files-info">FILES</h3>
  <?php if (count($request->FILES)): ?>
    <table class="req">
      <thead>
        <tr>
          <th>Variable</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($request->FILES as $key => $value): ?>
          <tr>
            <td><?=$key?></td>
            <td class="code"><pre><?=$self->escape($self->pprint($value))?></pre></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No FILES data</p>
  <?php endif; ?>


  <h3 id="cookie-info">COOKIES</h3>
  <?php if (count($request->COOKIES)): ?>
    <table class="req">
      <thead>
        <tr>
          <th>Variable</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($request->COOKIES as $key => $value): ?>
          <tr>
            <td><?=$key?></td>
            <td class="code"><pre><?=$self->escape($self->pprint($value))?></pre></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No COOKIES data</p>
  <?php endif; ?>

  <h3 id="meta-info">META</h3>
    <table class="req">
      <thead>
        <tr>
          <th>Variable</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php $meta = array_merge(array(), (array)$request->META); ksort($meta); ?>
        <?php foreach ($meta as $key => $value): ?>
          <tr>
            <td><?=$key?></td>
            <td class="code"><pre><?=$self->escape($self->pprint($value))?></pre></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
<?php else: ?>
  <p>Request data not supplied</p>
<?php endif; ?>

  <h3 id="settings-info">Settings</h3>
  <h4>Using settings module <code><?=$settings_module?></code></h4>
  <table class="req">
    <thead>
      <tr>
        <th>Setting</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
      <?php $ss = array_merge(array(), (array)$settings); ksort($ss); ?>
      <?php foreach ($ss as $key => $value): ?>
        <tr>
          <td><?=$key?></td>
          <td class="code"><pre><?=$self->escape($self->pprint($value))?></pre></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

</div>
<?php if (!$is_email): ?>
  <div id="explanation">
    <p>
      You're seeing this error because you have <code>DEBUG = true</code> in your
      Bjork settings file. Change that to <code>false</code>, and Bjork will
      display a standard 500 page.
    </p>
  </div>
<?php endif ?>
</body>
</html>