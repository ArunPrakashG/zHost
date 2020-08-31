<?php
class Functions
{
    public static function Redirect($path)
    {
        echo "<script type='text/javascript'>window.location.href = '" . $path . "';</script>";
    }

    public static function Alert($message)
    {
        echo '<script type="text/javascript">';
        echo 'alert("' . $message . '");';
        echo '</script>';
    }

    public static function SAlert($title, $message, $level = "warning")
    {
        echo '<script src="../includes/js/sweetalert2.min.js">';
        echo "Swal.fire({
            icon: '$level',
            title: '$title',
            text: '$message'
        });";
        echo '</script>';
    }
}
?>