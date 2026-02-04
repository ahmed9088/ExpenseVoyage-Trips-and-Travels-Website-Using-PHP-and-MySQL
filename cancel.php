<?php
include 'chatbot-loader.php'; 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment Canceled</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
        .cancel-container { max-width: 600px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); text-align: center; }
        h2 { color: red; }
        p { font-size: 18px; }
        .btn-home { background-color: #427c00; color: white; padding: 15px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; margin-top: 20px; display: inline-block; }
        .btn-home:hover { background-color: #7AB730; }
    </style>
</head>
<body>
<style>
    
    body {
      --sb-track-color: #232E33;
      --sb-thumb-color: #7AB730;
      --sb-size: 14px;
    }
    
    body::-webkit-scrollbar {
      width: 12px;
    }
    
    body::-webkit-scrollbar-track {
      background: var(--sb-track-color);
      border-radius: 1px;
    }
    
    body::-webkit-scrollbar-thumb {
      background: var(--sb-thumb-color);
      border-radius: 3px;
      
    }
    
    @supports not selector(::-webkit-scrollbar) {
      body {
        scrollbar-color: var(--sb-thumb-color)
                         var(--sb-track-color);
      }
    }
    </style>
    <div class="cancel-container">
        <h2>Payment Canceled</h2>
        <p>Your payment has been canceled. If you need assistance, please contact support.</p>
        <a href="index.php" class="btn-home">Return to Home</a>
    </div>
</body>
</html>
