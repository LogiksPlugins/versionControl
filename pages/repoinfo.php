<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$remoteList = $gitrepo->remoteList();
$branchList = $gitrepo->branchList();
$lastCommit = $gitrepo->gitcmd("git log -1");

?>
<div class='' style='padding:10px;'>
    <ul class="list-group">
        <li class="list-group-item list-group-item-info"><h3 style='padding:0px;margin:0px;'>Remote repositories
        
            <button class='btn btn-primary pull-right' style='margin-top: -3px;' onclick="addRemoteRepo(this)" ><i class='fa fa-plus'></i> Add Remote</button>
            <button class='btn btn-danger pull-right' style='margin-top: -3px;margin-right:10px;' onclick="removeRemoteRepo(this)" ><i class='fa fa-times'></i> Remove Remote</button>
        </h3></li>
        <?php
            if(count($remoteList)>0) {
                foreach($remoteList as $a) {
                    echo "<li class='list-group-item'>{$a}</li>";
                }
            } else {
                echo "<li class='list-group-item'><pre>No remote repository configured</pre></li>";
            }
        ?>
        <li class="list-group-item list-group-item-info"><h3 style='padding:0px;margin:0px;'>Branches
        
            <button class='btn btn-primary pull-right' style='margin-top: -3px;' onclick="addBranch(this)" ><i class='fa fa-plus'></i> Add Branch</button>
            <button class='btn btn-danger pull-right' style='margin-top: -3px;margin-right:10px;' onclick="removeBranch(this)" ><i class='fa fa-times'></i> Remove Branch</button>
        </h3></li>
        <?php
            foreach($branchList as $a) {
                echo "<li class='list-group-item'>{$a}</li>";
            }
        ?>
        <li class="list-group-item list-group-item-info"><h3 style='padding:0px;margin:0px;'>Last Commit</h3></li>
        <li class='list-group-item'>
            <?php
                echo "<pre>";
                echo implode("\n",$lastCommit);
                echo "</pre>";
            ?>
        </li>
    </ul>
</div>
<div id='modal-remote-add' class='hidden'>
    <div class="clearfix"></div>
    <div class="row clearfix">
        <div class="col-md-6 form-group">
            <label for="name">Remote Name *</label>
            <input type="text" class="form-control" name="name">
        </div>
        <div class="col-md-6 form-group">
            <label for="uri">Remote URI *</label>
            <input type="text" class="form-control" name="uri">
        </div>
        <div class='col-md-12 text-right'>
            <hr>
            <button class='btn btn-success' onclick='addRemoteRepoData(this)'>Submit</button>
        </div>
    </div>
</div>
<script>
function addRemoteRepo() {
    bootbox.dialog({
    	message: $("#modal-remote-add").html(),
    	title: "Remote Repo URI",
    	buttons: false
    });
}
function removeRemoteRepo() {
    lgksPrompt("Remote Source Name","Remote Remote", function(ans) {
        	if(ans!=null && ans.length>0) {
                processAJAXPostQuery(_service("versionControl","remote-remove"),"&path="+currentPath+"&branch="+currentBranch+"&name="+ans, function(ans) {
                    if(ans.Data.length>0) lgksToast("Error Occured");
                    else lgksToast("Remote source removed successfully");
                    
                    loadRepoInfo();
                },"json");
        	}
        });
}
function addBranch() {
    lgksPrompt("New Branch Name","New Branch", function(ans) {
        	if(ans!=null && ans.length>0) {
                processAJAXPostQuery(_service("versionControl","branch-add"),"&path="+currentPath+"&branch="+currentBranch+"&name="+ans, function(ans) {
                    if(ans.Data.length>0) lgksToast("Error Occured");
                    else lgksToast("Branch added successfully");
                    loadRepoInfo();
                },"json");
        	}
        });
}
function removeBranch() {
    lgksPrompt("Branch Name to remove","Remove Branch", function(ans) {
        	if(ans!=null && ans.length>0) {
                processAJAXPostQuery(_service("versionControl","branch-remove"),"&path="+currentPath+"&branch="+currentBranch+"&name="+ans, function(ans) {
                    lgksToast(ans.Data);
                    loadRepoInfo();
                },"json");
        	}
        });
}

function addRemoteRepoData(btn) {
    rowDiv = $(btn).closest(".row");
    q1=rowDiv.find("input[name='name']").val();
    q2=rowDiv.find("input[name='uri']").val();
    if(q1==null || q1.length<=0) {
        lgksToast("Remote Repo Name can not be blank");
        return;
    }
    if(q2==null || q2.length<=0) {
        lgksToast("Remote Repo URI can not be blank");
        return;
    }
    bootbox.hideAll()
    q = ["name="+q1,"uri="+q2];
	processAJAXPostQuery(_service("versionControl","remote-add"),"&path="+currentPath+"&branch="+currentBranch+"&"+q.join("&"), function(ans) {
        if(ans.Data.length>0) lgksToast("Error Occured");
        else lgksToast("Remote added successfully");
        loadRepoInfo();
    },"json");
}
</script>