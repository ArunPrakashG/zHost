<?php
    if(!isset($_SESSION)){
        session_start();
    }
?>

<head>
	<link href="https://fonts.googleapis.com/css?family=Averia+Serif+Libre|Noto+Serif|Tangerine" rel="stylesheet">
	<link rel="stylesheet" href="../includes/css/styling.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">    
	<meta charset="UTF-8">
	<title>zHost - <?php echo $_SESSION['PageTitle'] ?></title>
</head>

