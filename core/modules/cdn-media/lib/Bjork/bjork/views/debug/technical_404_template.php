<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Page not found at <?=$self->escape($request->getPathInfo())?></title>
  <meta name="robots" content="NONE,NOARCHIVE">
  <style type="text/css">
    html * { padding:0; margin:0; }
    body * { padding:10px 20px; }
    body * * { padding:0; }
    body { font:small sans-serif; background:#eee; }
    body>div { border-bottom:1px solid #ddd; }
    h1 { font-weight:normal; margin-bottom:.4em; }
    h1 span { font-size:60%; color:#666; font-weight:normal; }
    table { border:none; border-collapse: collapse; width:100%; }
    td, th { vertical-align:top; padding:2px 3px; }
    th { width:12em; text-align:right; color:#666; padding-right:.5em; }
    #info { background:#f6f6f6; }
    #info ol { margin: 0.5em 4em; }
    #info ol li { font-family: monospace; }
    #summary { background: #ffc; }
    #explanation { background:#eee; border-bottom: 0px none; }
  </style>
</head>
<body>
  <div id="summary">
    <h1>Page not found <span>(404)</span></h1>
    <table class="meta">
      <tr>
        <th>Request Method:</th>
        <td><?=$request->getMethod()?></td>
      </tr>
      <tr>
        <th>Request URL:</th>
      <td><?=$self->escape($request->buildAbsoluteURI())?></td>
      </tr>
    </table>
  </div>
  <div id="info">
<?php if (!empty($urlpatterns)): ?>
    <p>
    Using the URLconf defined in <code><?=$urlconf?></code>,
    Bjork tried these URL patterns, in this order:
    </p>
    <ol>
<?php foreach ($urlpatterns as $pattern): ?>
      <li>
<?php $i = count($pattern) - 1; ?>
<?php foreach ($pattern as $pat): ?>
        <?=$self->escape($pat->pattern)?> 
        <?php if ($i == 0 && !empty($pat->name)): ?>[name='<?=$pat->name?>']<?php endif; ?>
<?php $i--; endforeach; ?>
      </li>
<?php endforeach; ?>
    </ol>
    <p>The current URL, <code><?=$self->escape($request->getPath())?></code>, didn't match any of these.</p>
<?php else: ?>
    <p><?=$reason?></p>
<?php endif; ?>
  </div>

  <div id="explanation">
    <p>
      You're seeing this error because you have <code>DEBUG = true</code> in
      your Bjork settings file. Change that to <code>false</code>, and Bjork
      will display a standard 404 page.
    </p>
  </div>
</body>
</html>
