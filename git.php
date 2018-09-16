<?php
if(!defined('ROOT')) exit('No direct script access allowed');

/**
 * Git Library for runnig git commands
 * 
 * @author Bismay M <bismay4u@gmail.com>
 */
 
loadModuleLib("logiksShell","api");
 
class GitRepo {
    
    protected $repoPath = false;
    public $printFormat = "raw";
    
    public function __construct($folderPath) {
        if(file_exists($folderPath) && is_dir($folderPath)) {
            $this->repoPath = $folderPath;
            
            if(is_dir($this->repoPath.".git") || is_dir($this->repoPath."/.git")) {
                $this->configure();
            }
        } else {
            throw new Exception("{$folderPath} does not exist");
        }
    }
    
    public function configure() {
        //if(isset($_SESSION['VCS-CONFIGURED'])) return;
        
        $ans1 = $this->run("git config user.name \"{$_SESSION['SESS_USER_NAME']}\"");
        $ans2 = $this->run("git config user.email {$_SESSION['SESS_USER_EMAIL']}");
        
        // var_dump([$ans1,$ans2]);
        
        $_SESSION['VCS-CONFIGURED'] = true;
    }
    
    public function init() {
        $ans = $this->run("git init");
        
        return $this->processResults($ans);
    }
    
    public function gitStatus() {
        $ans = $this->run("git status");
        
        return $this->processResults($ans);
    }
    public function gitLog($limit = 100, $format = "%h:::%s:::%an:::%ci") {
        $ans = $this->run("git log --oneline --format='{$format}' -{$limit}");
        $this->printFormat = "html";
        return $this->processResults($ans);
    }
    
    public function commit($msg, $summary=false) {
        $ans = $this->run("git commit -m \"{$msg}\"");
        
        return $this->processResults($ans);
    }
    
    public function push($remote,$branch) {
        $ans = $this->run("GIT_SSH_COMMAND=\"ssh -i /var/www/.ssh/id_rsa\" git push -u {$remote} {$branch}");
        
        return $this->processResults($ans);
    }
    
    public function pull($remote,$branch) {
        $ans = $this->run("git pull {$remote} {$branch}");
        
        return $this->processResults($ans);
    }
    
    public function checkout($branchName) {
        $ans = $this->run("git checkout {$branchName}");
        
        return $this->processResults($ans);
    }
    
    //Tags
    public function tagList() {
        $ans = $this->run("git tag");
        $this->printFormat = "list";
        return $this->processResults($ans);
    }
    public function tagAdd($srcName) {
        $ans = $this->run("git tag {$srcName}");
        $ans = $this->processResults($ans);
        return $ans;
    }
    public function tagRemove($srcName) {
        $ans = $this->run("git tag -d {$srcName}");
        $ans = $this->processResults($ans);
        return $ans;
    }
    
    //Branches
    public function branchList() {
        $ans = $this->run("git branch");
        $branches = $this->processResults($ans);
        if(!$branches || !is_array($branches) || count($branches)<=0) {
            $branches = ["* master"];
        }
        return $branches;
    }
    public function branchAdd($srcName) {
        $ans = $this->run("git branch {$srcName}");
        $ans = $this->processResults($ans);
        return $ans;
    }
    public function branchRemove($srcName) {
        $ans = $this->run("git branch -d {$srcName}");
        $ans = $this->processResults($ans);
        return $ans;
    }
    
    //Remotes
    public function remoteList() {
        $ans = $this->run("git remote -v");
        $ans = $this->processResults($ans);
        return $ans;
    }
    public function remoteAdd($srcURI, $srcName='origin') {
        $ans = $this->run("git remote add {$srcName} {$srcURI}");
        $ans = $this->processResults($ans);
        return $ans;
    }
    public function remoteRemove($srcName) {
        $ans = $this->run("git remote remove {$srcName}");
        $ans = $this->processResults($ans);
        return $ans;
    }
    
    //RAW Cmd
    public function gitcmd($cmd) {
        $ans = $this->run($cmd);
        $ans = $this->processResults($ans);
        return $ans;
    }
    
    //Process string results before sending out as array
    protected function processResults($output) {
        switch(strtolower($this->printFormat)) {
            case "list":
                $output = explode("\n",$output);
                if(strlen($output[count($output)-1])==0) {
                    unset($output[count($output)-1]);
                }
                
                $html = ["<ul class='list-group'>"];
                foreach($output as $row) {
                    $html[] = "<li class='list-group-item'>{$row}</li>";
                }
                $html[] ="</ul>";
                $output = implode("",$html);
                break;
            case "html":
                $output = explode("\n",$output);
                if(strlen($output[count($output)-1])==0) {
                    unset($output[count($output)-1]);
                }
                
                $html = ["<table class='table table-bordered table-hover'>"];
                foreach($output as $row) {
                    $rowArr = explode(":::",$row);
                    $tr = ["<tr>"];
                    foreach($rowArr as $td) {
                        $tr[] = "<td>{$td}</td>";
                    }
                    $tr[] = "</tr>";
                    $html[] = implode("",$tr);
                }
                $html[] = "</table>";
                $output = implode("",$html);
                break;
            case "raw":
                $output = str_replace(':::',", ",$output);
                $output = explode("\n",$output);
                if(strlen($output[count($output)-1])==0) {
                    unset($output[count($output)-1]);
                }
                if(count($output)==1 && isset($output[0])) $output = $output[0];
                break;
        }
        return $output;
    }
    
    //Run commands at proper path
    public function run($command) {
		return logiksRunCmd($command,$this->repoPath);
	}
}
?>