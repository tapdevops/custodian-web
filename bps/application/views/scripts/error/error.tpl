<h3><?php echo $this->message; ?></h3>
<?php if (isset($this->exception)): ?>
<h4>Exception message:</h4>
<p>
<?php echo $this->exception->getMessage() . "\n"; ?>
<?php if ($this->sqlError != '') : ?>
<br /><br />
<b>SQL error:</b>
<br />
<?php echo $this->sqlError . "\n"; ?>
<?php endif; ?>
</p>
<h4>Exception trace:</h4>
<pre>
<?php echo $this->exception->getTraceAsString() . "\n"; ?>
</pre>
<?php endif; ?>
<h4>Request parameters:</h4>
<pre>
<?php echo var_export($this->request->getParams(), true) . "\n"; ?>
</pre>
