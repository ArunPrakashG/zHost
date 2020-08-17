<?php
    class Functions {
        public static function Redirect($path){
            echo "<script type='text/javascript'>window.location.href = '". $path ."';</script>";
            //header("Location: " . $path , true, 302);
        }
    }
?>