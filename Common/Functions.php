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

    public static function size_as_kb($size)
    {
        if ($size < 1024) {
            return "{$size} bytes";
        } elseif ($size < 1048576) {
            $size_kb = round($size / 1024);
            return "{$size_kb} KB";
        } else {
            $size_mb = round($size / 1048576, 1);
            return "{$size_mb} MB";
        }
    }
}
?>