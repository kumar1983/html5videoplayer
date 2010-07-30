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
                "hello",
                "hello1"
            ),
        );

        //print_r($flashvars);

        $flashvars = json_encode($flashvars);
        //echo $flashvars;

        print_r(json_decode('{"url":["http://cj-jackson.com/wp-content/uploads/2010/07/big_buck_bunny.m4v","http://cj-jackson.com/wp-content/uploads/2010/07/big_buck_bunny.ogv"],"poster":"http://cj-jackson.com/wp-content/uploads/2010/07/big_buck_bunny.jpg","width":640,"height":368}', true));
        ?>
    </body>
</html>
