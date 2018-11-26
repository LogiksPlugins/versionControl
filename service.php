<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$appPath = ROOT.APPS_FOLDER.CMS_SITENAME."/";
$repoPath = ROOT.APPS_FOLDER.CMS_SITENAME."/";

$_ENV['NOSCAN']=[
        "usermedia",
		"tmp",
		"temp",
		".git",
		"vendor",
		"vendors",
	];

if(isset($_REQUEST['path']) && strlen($_REQUEST['path'])>0) {
    if($_REQUEST['path']=="/") $_REQUEST['path']="";
    
    $repoPath.=$_REQUEST['path'];
    $repoPath = str_replace("//","/",$repoPath);

    if(!file_exists($repoPath) || !is_dir($repoPath)) {
        printServiceMsg("Given repo folder does not exist");
        return;
    }
}

if(!isset($_REQUEST['branch']) || strlen($_REQUEST['branch'])<=0) {
    $_REQUEST['branch'] = "master";
}

include_once __DIR__."/git.php";

$gitrepo = new GitRepo($repoPath);

switch ($_REQUEST['action']) {
    case "generate-key":
//         $ans = logiksRunCmd("ssh-keyscan -t rsa github.com >> ~/.ssh/known_hosts");
//         $ans = logiksRunCmd("pwd");
//         printServiceMsg($ans);
        // $ans = logiksRunCmd("ssh-keygen -b 2048 -t rsa -f /var/www/.ssh/id_rsa -q -N \"\"");
        // printServiceMsg($ans);
        // echo file_get_contents("/var/www/.ssh/id_rsa.pub");
        // $ans = logiksRunCmd("eval \"$(ssh-agent -s)\";ssh-add /var/www/.ssh/id_rsa");
        // printServiceMsg($ans);
        break;
    case "gitinit":
        if(file_exists($repoPath.".git")) {
            $ans = "The repo is already initiated.";
        } else {
            $ans = $gitrepo->init();
        }
        printServiceMsg($ans);
        break;
    case "listrepo":
        $fs = scanFolderTree($repoPath,$appPath);
        printServiceMsg($fs);
        break;
    case "listbranches":
        $ans = $gitrepo->branchList();
        printServiceMsg($ans);
        break;
    case "githelp":
        include_once __DIR__."/pages/githelp.php";
        break;
    case "repodetails":
        include_once __DIR__."/pages/repodetails.php";
        break;
    case "repoinfo":
        include_once __DIR__."/pages/repoinfo.php";
        break;
    case "info":
        $ans[]="Remotes";
        $ans = array_merge($ans,$gitrepo->remoteList());
        $ans[]="<hr>Branches";
        $ans = array_merge($ans,$gitrepo->branchList());
        $ans[]="<hr>Last Commit";
        $ans = array_merge($ans,$gitrepo->gitcmd("git log -1"));
        
        printServiceMsg($ans);
        break;    
    case "changes":case "gitstatus":
        $ans = $gitrepo->gitStatus();
        printServiceMsg($ans);
        break;
    case "history":
        $ans = $gitrepo->gitLog();
        printServiceMsg($ans);
        break;
    case "listtags":
        $ans = $gitrepo->tagList();
        printServiceMsg($ans);
        break;
    case "listremotes":
        $ans = $gitrepo->remoteList();
        printServiceMsg($ans);
        break;
    case "listremotes-names":
        $ans = $gitrepo->gitcmd("git remote");
        printServiceMsg($ans);
        break;
    case "gitaddall":
        $ans = $gitrepo->gitcmd("git add -A");
        printServiceMsg($ans);
        break;
    case "gitcommit":
        if(isset($_POST['msg']) && strlen($_POST['msg'])>0) {
            $ans = $gitrepo->commit($_POST['msg']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Message not found");
        }
        break;
    case "gitpush":
        if(isset($_POST['srcname']) && strlen($_POST['srcname'])>0) {
            $ans = $gitrepo->push($_POST['srcname'],$_POST['branch']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote Source not found");
        }
        break;
    case "gitpull":
        if(isset($_POST['srcname']) && strlen($_POST['srcname'])>0) {
            $ans = $gitrepo->pull($_POST['srcname'],$_POST['branch']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote Source not found");
        }
        break;
    case "checkout":
        if(isset($_POST['checkoutbranch']) && strlen($_POST['checkoutbranch'])>0) {
            $ans = $gitrepo->checkout($_POST['checkoutbranch']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote Source not found");
        }
        break;
        
    case "diffcommits":
        
        break;
    case "difffiles":
        
        break;
    case "stash":
        
        break;
    case "gitignore":
        if(file_exists($repoPath.".gitignore")) {
            $ans = file_get_contents($repoPath.".gitignore");
        } else {
            $ans = "";
        }
        printServiceMsg($ans);
        break;
    
    case "remote-add":
        if(isset($_POST['uri']) && strlen($_POST['uri'])>0) {
            if(!isset($_POST['name']) || strlen($_POST['name'])<=0) {
                $_POST['name'] = "origin";
            }
            $ans = $gitrepo->remoteAdd($_POST['uri'],$_POST['name']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote not found");
        }
        break;
    case "remote-remove":
        if(isset($_POST['name']) && strlen($_POST['name'])>0) {
            $ans = $gitrepo->remoteRemove($_POST['name']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote Name not found");
        }
        break;
    
    case "branch-add":
        if(isset($_POST['name']) && strlen($_POST['name'])>0) {
            $ans = $gitrepo->branchAdd($_POST['name']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote not found");
        }
        break;
    case "branch-remove":
        if(isset($_POST['name']) && strlen($_POST['name'])>0) {
            $ans = $gitrepo->branchRemove($_POST['name']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote Name not found");
        }
        break;
    
    case "tag-add":
        if(isset($_POST['name']) && strlen($_POST['name'])>0) {
            $ans = $gitrepo->tagAdd($_POST['name']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote Name not found");
        }
        break;
    case "tag-remove":
        if(isset($_POST['name']) && strlen($_POST['name'])>0) {
            $ans = $gitrepo->tagRemove($_POST['name']);
            printServiceMsg($ans);
        } else {
            printServiceMsg("Remote Name not found");
        }
        break;
}


function scanFolderTree($folder,$appPath) {
    $folders=array();
    
    if(is_dir($folder)) {
        if(file_exists($folder."/.git") && is_dir($folder."/.git")) {
            $folders[basename($folder)]=[
                    "path"=>str_replace($appPath,"/",$folder),
                    "timestamp"=>_pDate(date("Y-m-d", filectime($appPath)))
                ];
        }
        
		$out=scandir($folder);
		$out=array_splice($out, 2);
		asort($out);
		foreach($out as $key => $value) {
		    if(in_array($value,$_ENV['NOSCAN'])) continue;
			$bf=$folder.$value;
			$bf=str_replace(ROOT.APPS_FOLDER."{$_GET['forsite']}/", "", $bf);
			if(is_dir($folder.$value)) {
			    $folders=array_merge($folders,scanFolderTree($folder.$value."/",$appPath));
			}
		}
	}
	return ($folders);
}
?>