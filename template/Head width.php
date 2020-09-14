<?php
/**
    Template Name: two VR
*/
?>
<html>

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
        overflow:hidden;
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


    /*
* Prefixed by https://autoprefixer.github.io
* PostCSS: v7.0.29,
* Autoprefixer: v9.7.6
* Browsers: last 4 version
*/


    @media screen and (max-width:1023px) {
        .double-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

    }


    @media screen and (min-width:768px) and (max-width:1024px) and (orientation:landscape) {

        .double-container {
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            width: 100%;
            height: 100%;
        }

    }

    @media screen and (min-width:1px) and (max-width:767px) and (orientation:landscape) {
        .double-container {
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            width: 100%;
            height: 100%;
        }
    }

    @media screen and (min-width:1024px) {
        .double-container {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            width: 100%;
            height: 100%;
        }

    }

    .container1{
        height:100%;
        width: 100%;
    }
    .container2 {
        height: 100%;
        width: 100%;
    }
    </style>
</head>

<body scrolling="no">
    <div class=double-container>
        <div class="container1">
            <iframe class="iframebox" src="https://beyond.3dnest.cn/house/?m=61ea6918_rnDU_b6f9" frameborder="0" allowfullscreen="0"
                scrolling="no"></iframe>
        </div>
        <!-- <div class="agent-container">
            <p>Agent Info</p>
            <img src="https://sg2-cdn.pgimgs.com/agent/412764/APHO.102325804.V120B.jpg" alt="agent img"> </img>

        </div> -->
        <div class="container2">
            <iframe class="iframebox" src="https://yun.kujiale.com/design/3FO4D5L07EJD/airoaming" frameborder="0" allowfullscreen="0"
                scrolling="no"></iframe>
        </div>
    </div>
</body>

</html>