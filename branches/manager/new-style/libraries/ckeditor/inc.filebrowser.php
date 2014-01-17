<?php

require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/init.php');

//*** Global Variables.
$intElmntId = request('eid', 0);
$type = request('type');
$treeType = '';
if($type === 'elements') {
    $treeType = 'elements-ckeditor';
    $targetId = 'elementurl';
    $title = $objLang->get("pcmsElements", "menu");
}
else if($type === 'media') {
    $treeType = 'media-ckeditor';
    $targetId = 'mediaurl';
    $title = $objLang->get("pcmsStorage", "menu");
}

//*** Take care of any login requests.
require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/inc.login.php');

?>
<html>
<head>

    <script type="text/javascript" src="/libraries/jquery.js"></script>
    <script type="text/javascript" src="/libraries/jquery.debug.js"></script>
    <script type="text/javascript" src="/includes/inc.common.js"></script>

    <link rel="stylesheet" type="text/css" href="/css/xmlTree.css" media="all" />
    <script type="text/javascript" src="/libraries/dhtmlx/dhtmlXCommon.js"></script>
    <script type="text/javascript" src="/libraries/dhtmlx/dhtmlXTree.js"></script>
    <script type="text/javascript" src="/libraries/overlib/overlib_config.js"></script>
    <script type="text/javascript" src="/libraries/overlib/overlib.js"></script>
    <script type="text/javascript" src="/libraries/overlib/overlib_cssstyle.js"></script>
    <script type="text/javascript" src="/libraries/pintree.js"></script>
    <style>
        body, html {margin:0;padding:0;font-size:11px;font-family:Verdana;color:#333;background:#EAEAEA;}
        body {padding:10px;}
        .wrapper {display:block;padding:10px;background:#F7F8FC;}
        #treeContainer {margin-top:10px;}
        h1 {font-size:12px;margin:0;padding:4px 10px;background:#2C457C;color:#FFF;display:block;}
    </style>


    <script type="text/javascript">
    var funcnum = parseInt(<?php echo $_GET["CKEditorFuncNum"]; ?> +'');
    // load tree with correct starting element
    var dialog = window.opener.CKEDITOR.dialog.getCurrent();
    var elementId = dialog.getValueOf('info', '<?php echo $targetId ?>');
    if(elementId) {
        elementId = elementId.replace('?eid=','');
        elementId = elementId.replace('?mid=','');
        elementId = parseInt(elementId);
    }

    <?php
        echo Tree::treeRender($treeType, $intElmntId);
    ?>


    function loadTree(){
		objTree = new dhtmlXTreeObject('treeContainer', '100%', '100%', -1);
		objTree.setXMLAutoLoading('/ajaxtree.php?type=<?php echo $treeType ?>');
		objTree.setImagePath('/images/xmltree/');
		objTree.setOnClickHandler(doOnSelect);
		objTree.setOnOpenEndHandler(doOnOpenEnd);
        objTree.setOnImageClickHandler(doOnSelect);
        objTree.setOnClickHandler(doOnSelect);
		objTree.loadXML('/ajaxtree.php?cmd=init&type=<?php echo $treeType ?>&id='+ elementId, function(){
            objTree.openItem(elementId);
            objTree.selectItem(elementId,false,false);
        });
		window._treeApi = objTree; // new stuff depends on this
    }

    // insert into ckeditor, close window
    function insertIntoCkEditor(file){
        window.opener.CKEDITOR.tools.callFunction(funcnum, file);
        window.close();
    }

    $(document).ready(function(){
        $('.containerTableStyle').css('height','auto');
    });
    </script>

</head>
<body>
    <div class="wrapper">
        <h1><?php echo $title ?></h1>

        <div id="treeContainer">

        </div>
    </div>

    </body>

</html>