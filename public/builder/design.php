<?php
    error_reporting(0);
    session_start();
    if ($_SESSION['editor_status']!="live_editor")
    {
        die('You are not authorize to access this page');
    }

    //$url = '/builder/html/templates/' . $_GET['type'] . '/' . $_GET['id'];
    $vType = $_GET['type'];
    $idPage = $_GET['id'];

    $url = 'http://localhost/polbangtanmedan_ac_id/html5/public/index.php/admin2011/mypage/getcontent/'.$idPage;
    //$url = '/builder/html/templates/' . $_GET['type'] . '/' . $_GET['id'];

?>
<!doctype html>
<html>
    <head>
        <title>Live Editor - Administrator</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="assets/image/builderjs_color_logo.png" rel="icon" type="image/x-icon" />
        <link rel="stylesheet" href="dist/builder.css">
        <script src="dist/builder.js"></script>

        <script>
            var editor;
            var params = new URLSearchParams(window.location.search);
            var templates = [
                {"name":"Blank","url":"design.php?id=6037a0a8583a7&type=default","thumbnail":"templates\/default\/6037a0a8583a7\/thumb.png"},
                {"name":"Pricing Table","url":"design.php?id=6037a2135b974&type=default","thumbnail":"templates\/default\/6037a2135b974\/thumb.png"},
                {"name":"Listing & Tables","url":"design.php?id=6037a2250a3a3&type=default","thumbnail":"templates\/default\/6037a2250a3a3\/thumb.png"},
                {"name":"Forms Building","url":"design.php?id=6037a23568208&type=default","thumbnail":"templates\/default\/6037a23568208\/thumb.png"},
                {"name":"1-2-1 column layout","url":"design.php?id=6037a2401b055&type=default","thumbnail":"templates\/default\/6037a2401b055\/thumb.png"},
                {"name":"1-2 column layout","url":"design.php?id=6037a24ebdbd6&type=default","thumbnail":"templates\/default\/6037a24ebdbd6\/thumb.png"},
                {"name":"1-3-1 column layout","url":"design.php?id=6037a25ddce80&type=default","thumbnail":"templates\/default\/6037a25ddce80\/thumb.png"},
                {"name":"1-3-2 column layout","url":"design.php?id=6037a26b0a286&type=default","thumbnail":"templates\/default\/6037a26b0a286\/thumb.png"},
                {"name":"1-3 column layout","url":"design.php?id=6037a275bf375&type=default","thumbnail":"templates\/default\/6037a275bf375\/thumb.png"},
                {"name":"One column layout","url":"design.php?id=6037a28418c95&type=default","thumbnail":"templates\/default\/6037a28418c95\/thumb.png"},
                {"name":"2-1-2 column layout","url":"design.php?id=6037a29a35e05&type=default","thumbnail":"templates\/default\/6037a29a35e05\/thumb.png"},
                {"name":"2-1 column layout","url":"design.php?id=6037a2aa315d4&type=default","thumbnail":"templates\/default\/6037a2aa315d4\/thumb.png"},
                {"name":"Two columns layout","url":"design.php?id=6037a2b67ed27&type=default","thumbnail":"templates\/default\/6037a2b67ed27\/thumb.png"},
                {"name":"3-1-3 column layout","url":"design.php?id=6037a2c3d7fa1&type=default","thumbnail":"templates\/default\/6037a2c3d7fa1\/thumb.png"},
                {"name":"Three columns layout","url":"design.php?id=6037a2dcb6c56&type=default","thumbnail":"templates\/default\/6037a2dcb6c56\/thumb.png"}
            ];

            var tags = [
                {type:'label', tag: 'No Tag Avaiable'},
                // {type: 'label', tag: '{CONTACT_FIRST_NAME}'},
                // {type: 'label', tag: '{CONTACT_LAST_NAME}'},
                // {type: 'label', tag: '{CONTACT_FULL_NAME}'},
                // {type: 'label', tag: '{CONTACT_EMAIL}'},
                // {type: 'label', tag: '{CONTACT_PHONE}'},
                // {type: 'label', tag: '{CONTACT_ADDRESS}'},
                // {type: 'label', tag: '{ORDER_ID}'},
                // {type: 'label', tag: '{ORDER_DUE}'},
                // {type: 'label', tag: '{ORDER_TAX}'},
                // {type: 'label', tag: '{PRODUCT_NAME}'},
                // {type: 'label', tag: '{PRODUCT_PRICE}'},
                // {type: 'label', tag: '{PRODUCT_QTY}'},
                // {type: 'label', tag: '{PRODUCT_SKU}'},
                // {type: 'label', tag: '{AGENT_NAME}'},
                // {type: 'label', tag: '{AGENT_SIGNATURE}'},
                // {type: 'label', tag: '{AGENT_MOBILE_PHONE}'},
                // {type: 'label', tag: '{AGENT_ADDRESS}'},
                // {type: 'label', tag: '{AGENT_WEBSITE}'},
                // {type: 'label', tag: '{AGENT_DISCLAIMER}'},
                // {type: 'label', tag: '{CURRENT_DATE}'},
                // {type: 'label', tag: '{CURRENT_MONTH}'},
                // {type: 'label', tag: '{CURRENT_YEAR}'},
                // {type: 'button', tag: '{PERFORM_CHECKOUT}', 'text': 'Checkout'},
                // {type: 'button', tag: '{PERFORM_OPTIN}', 'text': 'Subscribe'}
            ];

            $( document ).ready(function() {
                var buildMode = true;
                var legacyMode = false;

                if(params.get('type') == 'custom') {
                    buildMode = false;
                    legacyMode = true;
                } else {
                    buildMode = true;
                    legacyMode = false;
                }

                editor = new Editor({
                    buildMode: buildMode, // default == true
                    legacyMode: legacyMode, // default == false
                    root: '/builder/html/dist/',
                    url: '<?php echo $url ?>',
                    urlBack: window.location.origin,
                    uploadAssetUrl: 'asset.php',
                    uploadAssetMethod: 'POST',
                    uploadTemplateUrl: 'upload.php',
                    uploadTemplateCallback: function(response) {
                        window.location = response.url;
                    },
                    saveUrl: 'save.php',
                    saveMethod: 'POST',
                    data: {
                        _token: 'CSRF_TOKEN',
                        type: '<?php echo $_GET['type'] ?>',
                        template_id: '<?php echo $_GET['id'] ?>'
                    },
                    templates: templates,
                    tags: tags,
                    changeTemplateCallback: function(url) {
                        window.location = url;
                    },

                    /*
                        Disable features: 
                        change_template|export|save_close|footer_exit|help
                    */
                    // disableFeatures: [ 'change_template', 'export', 'save_close', 'footer_exit', 'help' ],
                    disableFeatures: [ 'change_template', 'export', 'save_close', 'footer_exit', 'help', 'menu_design' ], 

                    // disableWidgets: [ 'HeaderBlockWidget' ], // disable widgets
                    export: {
                        url: 'export.php'
                    },
                    backgrounds: [
                        '/assets/image/backgrounds/images1.jpg',
                        '/assets/image/backgrounds/images2.jpg',
                        '/assets/image/backgrounds/images3.jpg',
                        '/assets/image/backgrounds/images4.png',
                        '/assets/image/backgrounds/images5.jpg',
                        '/assets/image/backgrounds/images6.jpg',
                        '/assets/image/backgrounds/images9.jpg',
                        '/assets/image/backgrounds/images11.jpg',
                        '/assets/image/backgrounds/images12.jpg',
                        '/assets/image/backgrounds/images13.jpg',
                        '/assets/image/backgrounds/images14.jpg',
                        '/assets/image/backgrounds/images15.jpg',
                        '/assets/image/backgrounds/images16.jpg',
                        '/assets/image/backgrounds/images17.png'
                    ]
                });

                editor.init();
            });
        </script>

        <style>
            .lds-dual-ring {
                display: inline-block;
                width: 80px;
                height: 80px;
            }
            .lds-dual-ring:after {
                content: " ";
                display: block;
                width: 30px;
                height: 30px;
                margin: 4px;
                border-radius: 80%;
                border: 2px solid #aaa;
                border-color: #007bff transparent #007bff transparent;
                animation: lds-dual-ring 1.2s linear infinite;
            }
            @keyframes lds-dual-ring {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>
    <body class="overflow-hidden">
        <div style="text-align: center;
            height: 100vh;
            vertical-align: middle;
            padding: auto;
            display: flex;">
            <div style="margin:auto" class="lds-dual-ring"></div>
        </div>

        <script>
            switch(window.location.protocol) {
                case 'http:':
                case 'https:':
                  //remote file over http or https
                  break;
                case 'file:':
                  alert('Please put the builderjs/ folder into your document root and open it through a web URL');
                  window.location.href = "./index.php";
                  break;
                default:
                  //some other protocol
            }
        </script>
    </body>
</html>
