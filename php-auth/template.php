<!DOCTYPE html>
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
    <script type="text/javascript">
        const handleAuth = () => {
            return new Promise((resolve) => {
              /**
               * Set up message listener before opening the window
               * to handle the OAuth response
               */
              const handleMessage = (event) => {
                if (
                  [
                    "https://audiomack.com",
                    "https://am-next.aws.audiomack.com",
                  ].includes(event.origin) === false
                )
                  return;

                if (event.data.type === "OAUTH_SUCCESS") {
                  /**
                   * Remove the message listener once the OAuth is successful
                   */
                  window.removeEventListener("message", handleMessage);
                  console.log(event.data.redirectUrl);
                  resolve(event.data.redirectUrl);
                }
              };

              window.addEventListener("message", handleMessage);

              const authWindow = window.open(
                "index.php?popup=true",
                "Login with Audiomack",
                "width=600,height=800",
              );

              // Handle if user closes the window without completing auth
              const checkClosed = setInterval(() => {
                if (authWindow?.closed) {
                  clearInterval(checkClosed);
                  window.removeEventListener("message", handleMessage);
                  resolve(null); // Resolve with null if the window is closed
                }
              }, 1000);
            });
          };

          const handleClick = async () => {
            try {
              const redirectUrl = await handleAuth();
              if (redirectUrl) {
                window.location.href = redirectUrl;
              } else {
                console.log("Authentication was cancelled or failed");
              }
            } catch (error) {
              console.error("Authentication error:", error);
            }
          };
    </script>
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
        const el = document.getElementById('popup');
        el.addEventListener('click', handleClick, false);
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
