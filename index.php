<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=US-ASCII">
        <title></title>
    </head>
    <body>
        <?php
        $flashvars = array(
            "playlist" => array(
                array(
                    "url" => "test"
                ),
                array(
                    "url" => "test",
                    "autoPlay" => false
                ),
            ),
        );

        //print_r($flashvars);

        $flashvars = json_encode($flashvars);
        echo $flashvars;
        ?>
    </body>
</html>
