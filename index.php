<?php
if(!defined('ROOT')) exit('No direct script access allowed');
loadModule("pages");

$appPath = ROOT.APPS_FOLDER.CMS_SITENAME;//."/.git";

if(!file_exists($appPath) || !is_dir($appPath)) {
    echo "<h2 align=center>REPO Path not found</h2>";
    return;
}

if(!is_writable(dirname($appPath))) {
    echo "<h2 align=center>APPROOT is not writable</h2>";
    return;
}



echo _css(["versionControl"]);
echo _js(["versionControl"]);

function pageContentArea() {
    return "<div id='contentArea'></div>";
}
function pageSidebar() {
    return "<div id='sidebarArea'></div>";
}

printPageComponent(false,[
		"toolbar"=>[
			"listREPOS"=>["icon"=>"<i class='fa fa-refresh'></i>"],
			"showHelp"=>["icon"=>"<i class='fa fa-question-circle'></i>"],
			['type'=>"bar"],
			"addGitRepo"=>["icon"=>"<i class='fa fa-plus'></i>","title"=>"Add Repo"],
			['type'=>"bar"],
			"addAllFiles"=>["icon"=>"<i class='fa fa-file'></i>","title"=>"Add All","class"=>"onrepoload","align"=>"right"],
			"commitGitRepo"=>["icon"=>"<i class='fa fa-bolt'></i>","title"=>"Commit","class"=>"onrepoload","align"=>"right"],
// 			"scrapGitRepo"=>["icon"=>"<i class='fa fa-broom'></i>","title"=>"Scrap","class"=>"onrepoload","align"=>"right"],
// 			"stashGitRepo"=>["icon"=>"<i class='fa fa-angle-double-right'></i>","title"=>"Stash","class"=>"onrepoload","align"=>"right"],
// 			['type'=>"bar"],
// 			"pushGitRepo"=>["icon"=>"<i class='fa fa-upload'></i>","title"=>"Push","class"=>"onrepoload","align"=>"right"],
// 			"pullGitRepo"=>["icon"=>"<i class='fa fa-download'></i>","title"=>"Pull","class"=>"onrepoload","align"=>"right"],
			
// 			"generateRoles"=>["icon"=>"<i class='fa fa-gears'></i>","tips"=>"Generate New Roles"],
			//"createTemplate"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create New"],
			//"openExternal"=>["icon"=>"<i class='fa fa-external-link'></i>","class"=>"onsidebarSelect"],
			//"preview"=>["icon"=>"<i class='fa fa-eye'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Preview Content"],
			
			//"rename"=>["icon"=>"<i class='fa fa-terminal'></i>","class"=>"onsidebarSelect onOnlyOneSelect","tips"=>"Rename Content"],
// 			"deleteTemplate"=>["icon"=>"<i class='fa fa-trash'></i>","class"=>"onsidebarSelect"],
		],
		"sidebar"=>"pageSidebar",
		"contentArea"=>"pageContentArea"
	]);
?>
<script>

</script>