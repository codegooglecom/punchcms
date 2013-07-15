<?php

require_once('includes/init.php');

//*** Global Variables.
$intCatId 	= request('cid');
$intElmntId = request('eid', 0);
$strCommand = request('cmd', CMD_LIST);
$strOutput  = "";

//*** Take care of any login requests.
require_once('includes/inc.login.php');

?>

<html>
    
    <head>
        
        <link type="text/css" rel="stylesheet" href="css/jquery.treeview.css">
        
        <script type="text/javascript" src="/libraries/jquery.js"></script>
        <script type="text/javascript" src="/libraries/jquery.treeview.js"></script>
        <script type="text/javascript" src="/includes/inc.common.js"></script>
        
        <script>
        $(document).ready(function(){
            
            $('#files').treeview({
                collapsed: true,
                control: "#treecontrol"
            });
            
            $('#links').treeview({
                collapsed: true,
                control: "#treecontrol"
            });
        
        });
        </script>
        
    </head>
    <body>
<?php

function arrayToUl($array, $first = true) {
    if ($first == false){
    
        $out="<ul>";
        
    }else{
        
        $out='<ul id="links" class="treeview">';
        
    }
    foreach($array as $key => $elem){
        if(!is_array($elem["children"])){
                $out=$out.'<li><a class="select" href="?eid='.$elem["id"].'">'.$elem["name"] . '</a></li>';
        }
        else $out=$out.'<li><a class="select" href="?eid='.$elem["id"].'">'.$elem["name"] . '</a>'.arrayToUl($elem["children"], false).'</li>';
    }
    $out=$out."</ul>";
    return $out; 
}

function arrayToUlImage($array, $first = true) {
    
    $extensionImages = array(
        'jpg' => 'image',
        'gif' => 'image',
        'png' => 'image',
        'bmp' => 'image',
        'doc' => 'doc',
        'docx' => 'doc',
        'pdf' => 'pdf',
        'xls' => 'xls',
        'xlsx' => 'xls',
        'zip' => 'rar',
        'exe' => 'exe',

    );
    
    if ($first == false){
    
        $out="<ul>";
        
    }else{
        
        $out='<ul id="files" class="filetree">';
        
    }
    foreach($array as $elem){
        
        if ($elem["extension"] != ""){
            
            if (array_key_exists($elem["extension"],$extensionImages)){
                
                $image = $extensionImages[$elem["extension"]];
                
            }
            
            
            
        }
        
        if(!is_array($elem["children"]) && $elem["extension"] != ""){
                $out=$out.'<li><a class="select" href="?mid='.$elem["id"].'"><span class="file ' . $image . '">'.$elem["name"] . '.'. $elem["extension"] . '</span></a></li>';
        }
        else $out=$out.'<li><span class="folder">'.$elem["name"] . $elem["extension"] .'</span>'.arrayToUlImage($elem["children"], false).'</li>';
    }
    $out=$out."</ul>";
    return $out; 
}

?>

<div id="treecontrol">
        <a title="Collapse the entire tree below" href="#"><img src="../images/minus.gif" /> Collapse All</a>
        <a title="Expand the entire tree below" href="#"><img src="../images/plus.gif" /> Expand All</a>
</div>

        
<?php

if ($_GET["data"] == "media"){

    echo arrayToUlImage(StorageItems::getMediaArray());
    
}else{

    echo arrayToUl(Elements::getPagesArray());
    
}


?>
        
        <script>
            
        var funcnum = <?php echo $_GET["CKEditorFuncNum"]; ?>;

        function invoeren (file){

            window.opener.CKEDITOR.tools.callFunction(funcnum, file)
            window.close();

        }

        </script>
        
    </body>
    
</html>