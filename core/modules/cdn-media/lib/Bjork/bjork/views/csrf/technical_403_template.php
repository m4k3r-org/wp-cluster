<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="robots" content="NONE,NOARCHIVE">
  <title>403 Forbidden</title>
  <style type="text/css">
    html * { padding:0; margin:0; }
    body * { padding:10px 20px; }
    body * * { padding:0; }
    body { font:small sans-serif; background:#eee; }
    body>div { border-bottom:1px solid #ddd; }
    h1 { font-weight:normal; margin-bottom:.4em; }
    h1 span { font-size:60%; color:#666; font-weight:normal; }
    #info { background:#f6f6f6; }
    #info ul { margin: 0.5em 4em; }
    #info p, #summary p { padding-top:10px; }
    #summary { background: #ffc; }
    #explanation { background:#eee; border-bottom: 0px none; }
  </style>
</head>
<body>
<div id="summary">
  <h1>Forbidden <span>(403)</span></h1>
  <p>CSRF verification failed. Request aborted.</p>
<?php if ($no_referer): ?>
  <p>You are seeing this message because this HTTPS site requires a 'Referer
   header' to be sent by your Web browser, but none was sent. This header is
   required for security reasons, to ensure that your browser is not being
   hijacked by third parties.</p>

  <p>If you have configured your browser to disable 'Referer' headers, please
   re-enable them, at least for this site, or for HTTPS connections, or for
   'same-origin' requests.</p>
<?php endif; ?>
</div>
<?php if ($DEBUG): ?>
<div id="info">
  <h2>Help</h2>
<?php if (!empty($reason)): ?>
    <p>Reason given for failure:</p>
    <pre>
    <?=$self->escape($reason)?>
    </pre>
<?php endif; ?>

  <p>In general, this can occur when there is a genuine Cross Site Request Forgery, or when
  Bjork's CSRF mechanism has not been used correctly.  For POST forms, you need to
  ensure:</p>

  <ul>
    <li>The view function uses <code>RequestContext</code>
    for the template, instead of <code>Context</code>.</li>

    <li>In the template, there is a <code>csrf_token</code> template tag
    inside each POST form that targets an internal URL.</li>

  </ul>

  <p>You're seeing the help section of this page because you have <code>DEBUG =
  true</code> in your Bjork settings file. Change that to <code>false</code>,
  and only the initial error message will be displayed.  </p>

  <p>You can customize this page using the CSRF_FAILURE_VIEW setting.</p>
</div>
<?php else: ?>
<div id="explanation">
  <p><small>More information is available with DEBUG=True.</small></p>
</div>
<?php endif; ?>
</body>
</html>