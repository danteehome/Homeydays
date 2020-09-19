<?php
/**
    Template Name: two VR
*/
?>
<html>
<?php $url1=" https://beyond.3dnest.cn/house/?m=61ea6918_rnDU_b6f9"?>
<?php $url2=" https://yun.kujiale.com/design/3FO4D5L07EJD/airoaming"?>





















































<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,height=device-height, initial-scale=1">
    <title><?php wp_title( '|', true, 'right' ); bloginfo('url'); ?></title>
    <style>
    .html,
    .body {
        margin: 0px !important;
        padding: 0;
        width: 100%;
        height: 100%;
        overflow-y: hidden;
    }

    .iframebox {
        border: none;
        overflow: hidden;
        width: 100%;
        height: 100%
    }

    /* .agent-container {
        position: absolute;
        top: 50%;
        left: 0%;
        transform: translate(0, -50%);
        background-color: aqua;
        height: 25%;
        width: 10%;
        overflow: hidden;
        display: none;

    } */


    /* ----------- iPad Pro ----------- */
    /* Portrait and Landscape */
    @media only screen and (min-width: 1024px) and (max-height: 1366px) and (-webkit-min-device-pixel-ratio: 1.5) {
        .double-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            width: 100%;
            height: 100%;
        }
    }

    /* Portrait */
    @media only screen and (min-width: 1024px) and (max-height: 1366px) and (orientation: portrait) and (-webkit-min-device-pixel-ratio: 1.5) {
        .double-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            width: 100%;
            height: 100%;
        }
    }

    /* Landscape */
    @media only screen and (min-width: 1024px) and (max-height: 1366px) and (orientation: landscape) and (-webkit-min-device-pixel-ratio: 1.5) {
        .double-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            width: 100%;
            height: 100%;
        }

    }



    .container1,
    .container2 {
        height: 100%;
        width: 50%;
    }
    </style>
</head>

<body scrolling="no">
    <div class=double-container>
        <div class="container1">
            <iframe class="iframebox" src="<?php echo $url1?>" frameborder="0" allowfullscreen="0" scrolling="no"
                style="overflow:hidden;height:100%;width:100%; "></iframe>
        </div>
        <!-- <div class="agent-container">
            <p>Agent Info</p>
            <img src="https://sg2-cdn.pgimgs.com/agent/412764/APHO.102325804.V120B.jpg" alt="agent img"> </img>

        </div> -->
        <div class="container2">
            <iframe class="iframebox" src="<?php echo $url2?>" frameborder="0" allowfullscreen="0" scrolling="no"
                style="overflow:hidden;height:100%;width:100%; "></iframe>
        </div>
    </div>
</body>

</html>