<?php
session_start();
session_destroy();
?>
<html>

<head>
    <link href="/src/css/common.css" rel="stylesheet">
</head>

<body class="h-screen flex flex-col items-center justify-center">
    <div class="font-bold text-8xl text-red-400 mb-6">ERROR</div>
    <img src="/src/assets/error_page.png" alt="Error Page" class="max-w-full h-auto" />
</body>

</html>
