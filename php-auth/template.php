<html>
<head>
    <title>Audiomack Third Party Auth Example Application</title>
    <style>
        body {
            font-family: Arial;
            width: 50%;
            text-align: center;
            margin: 2em auto 2em auto;
        }
        input, button {
            font-size: 2em;
            background-color: orange;
        }
    </style>
</head>
<body>
<h1>Audiomack Third Party Auth Example Application</h1>
<p>Application running on host <strong><?php echo htmlspecialchars($_SERVER['HTTP_HOST']) ?></strong></p>
<?php if (!$success): ?>
<hr>
<h2>Inline Flow</h2>
<form action="index.php" method="post">
    <input type="submit" value="Login with Audiomack" />
</form>
<hr>
<h2>Pop-up Flow</h2>
    <button id="popup">Login with Audiomack</button>
    <script type="text/javascript">
        const authWindow = () => {
            window.open(
                'index.php?popup=true',
                'Login with Audiomack',
                'width=600,height=800'
                );
        }
        const el = document.getElementById('popup');
        el.addEventListener('click', authWindow, false);
    </script>
<hr>
<?php endif; ?>
<?php if ($error): ?>
<h2>Error</h2>
<p>The following error message was returned: <?php echo htmlspecialchars($error) ?></p>
    <?php if ($errorContent): ?>
    <p>The following JSON structure was retured from the API <?= $apiUrl ?>:</p>
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
<?php endif; ?>
</body>
</html>
