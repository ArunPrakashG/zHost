<?php
class Functions
{
    public static function Redirect($path)
    {
        echo "<script type='text/javascript'>window.location.href = '" . $path . "';</script>";
    }

    public static function Alert($message)
    {
        return "<script type='text/javascript'>alert('$message');</script>";
    }

    public static function SAlert($title, $message, $level = "warning")
    {
        return "<script>Swal.fire({
            icon: '$level',
            title: '$title',
            text: '$message'
        });</script>";
    }
}
?>