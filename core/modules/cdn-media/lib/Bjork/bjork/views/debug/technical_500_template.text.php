<?php if (isset($exception_type)):?><?=$exception_type?><?php else: ?>Report<?php endif; ?><?php if (isset($request)): ?> at <?=$request->getPathInfo()?><?php endif; ?> 
<?php if (isset($exception_value)):?><?=$exception_value?><?php else: ?>No exception supplied<?php endif; ?> 
<?php if (isset($request)): ?> 

Request Method: <?=$request->getMethod()?> 
Request URL: <?=$request->buildAbsoluteURI()?> 
<?php endif; ?> 
Bjork Version: <?=$bjork_version?> 
PHP Version: <?=$sys_version?> 
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
  File "<?=$frame['filename']?>":<?=$frame['lineno']?> in <?=$frame['function']?>(<?=implode(', ', $frame['funcargs'])?>)<?php if (false): ?>
  {% if frame.context_line %}  {{ frame.lineno }}. {{ frame.context_line|escape }}{% endif %}<?php endif ?> 
<?php endforeach ?>
Exception Type: <?=$exception_type?><?php if (isset($request)): ?> at <?=$request->getPathInfo()?><?php endif; ?> 
Exception Value: <?=$exception_value?> 
<?php if (isset($request)): ?> 
Request information:
GET:<?php if (count($request->GET)): ?><?php foreach ($request->GET as $key => $value): ?> 
<?=$key?> = <?=$self->pprint($value)?><?php endforeach ?>
<?php else: ?> No GET data<?php endif; ?>

POST:<?php if (count($request->POST)): ?><?php foreach ($request->POST as $key => $value): ?> 
<?=$key?> = <?=$self->pprint($value)?><?php endforeach ?>
<?php else: ?> No POST data<?php endif; ?>

FILES:<?php if (count($request->FILES)): ?><?php foreach ($request->FILES as $key => $value): ?> 
<?=$key?> = <?=$self->pprint($value)?><?php endforeach ?>
<?php else: ?> No FILES data<?php endif; ?>

COOKIES:<?php if (count($request->COOKIES)): ?><?php foreach ($request->COOKIES as $key => $value): ?> 
<?=$key?> = <?=$self->pprint($value)?><?php endforeach ?>
<?php else: ?> No COOKIES data<?php endif; ?>

META:<?php $meta = array_merge(array(), (array)$request->META); ksort($meta); ?>
<?php foreach ($meta as $key => $value): ?> 
<?=$key?> = <?=$self->pprint($value)?><?php endforeach ?>
<?php else: ?>
Request data not supplied
<?php endif; ?> 

Settings:
Using settings module <?=$settings_module?><?php $ss = array_merge(array(), (array)$settings); ksort($ss); ?>
<?php foreach ($ss as $key => $value): ?> 
<?=$key?> = <?=$self->pprint($value)?><?php endforeach ?> 

You're seeing this error because you have DEBUG = True in your
Bjork settings file. Change that to False, and Bjork will
display a standard 500 page.
