<style>
    body {
        font-family: Arial;
        width: 50%;
        text-align: center;
        margin: 2em auto 2em auto;
    }
    input {
        font-size: 2em;
        background-color: orange;
    }
</style>
<h1>Audiomack Third Party Auth Example Application</h1>
<p>Application running on host <strong><?php echo htmlspecialchars($_SERVER['HTTP_HOST']) ?></strong></p>
<?php if (!$success): ?>
<hr>
<form action="index.php" method="post">
    <input type="submit" value="Login with Audiomack" />
</form>
<hr>
<?php endif; ?>
<?php if ($error): ?>
<h2>Error</h2>
<p>The following error message was returned: <?php echo htmlspecialchars($error) ?></p>
    <?php if ($errorContent): ?>
    <p>The following JSON structure was retured from the API:</p>
    <pre>
        <?php echo htmlspecialchars($errorContent) ?>
    </pre>
    <?php endif; ?>
<?php endif; ?>

<?php if ($success): ?>
    <?php
        $res = json_decode($response->getBody());
    ?>
    <h2>Success</h2>
    <p>Authenticated as <strong><?php echo htmlspecialchars($res->name) ?></strong></p>
    <pre style="text-align: left;"><?php echo htmlspecialchars(print_r($res, true)); ?></pre>
<?php endif;
