<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$branches = $gitrepo->branchlist();

?>
<div id='repoDetailsTab' class='repo-details'>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#changes" aria-controls="changes" role="tab" data-toggle="tab" onclick="loadChanges()">Changes</a></li>
    <li role="presentation"><a href="#history" aria-controls="history" role="tab" data-toggle="tab" onclick="loadCommitHistory()">History</a></li>
    <li role="presentation"><a href="#tags" aria-controls="tags" role="tab" data-toggle="tab" onclick="loadTaglist()">Tags</a></li>
    <li role="presentation"><a href="#repoinfo" aria-controls="repoinfo" role="tab" data-toggle="tab" onclick="loadRepoInfo()">Info</a></li>
    <li role="presentation"><a href="#gitignore" aria-controls="gitignore" role="tab" data-toggle="tab" onclick="loadGitIgnore()">GitIgnore</a></li>
    <li role="presentation" style='float: right;margin-right: 2px;width: 200px;'>
        <select id='branchList' class='form-control select'>
            <?php
                foreach($branches as $a) {
                    if(substr(trim($a),0,1)=="*") {
                        $a = str_replace("* ","",$a);
                        echo "<option value='{$a}' selected class='selected'>".toTitle("{$a} branch")."</option>";
                    } else {
                        echo "<option value='{$a}'>".toTitle("{$a} branch")."</option>";
                    }
                }
            ?>
        </select>
    </li>
  </ul>

  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="changes">Loading ...</div>
    <div role="tabpanel" class="tab-pane" id="history">Loading ...</div>
    <div role="tabpanel" class="tab-pane" id="tags">Loading ...</div>
    <div role="tabpanel" class="tab-pane" id="repoinfo">Loading ...</div>
    <div role="tabpanel" class="tab-pane" id="gitignore">Loading ...</div>
  </div>
</div>
<script>
showRepoToolButtons();
currentBranch = $("#branchList").val();

loadChanges();

function loadRepoInfo() {
    $("#repoinfo").load(_service("versionControl","repoinfo")+"&path="+currentPath+"&branch="+currentBranch, function() {
        
    });
    // processAJAXQuery(_service("versionControl","info")+"&path="+currentPath+"&branch="+currentBranch, function(ans) {
    //     if(Array.isArray(ans.Data)) {
    //       ans.Data = ans.Data.join("\n");  
    //     }
    //     $("#repoinfo").html("<pre>"+ans.Data+"</pre>");
    // },"json");
}

function loadTaglist() {
    processAJAXQuery(_service("versionControl","listtags")+"&path="+currentPath+"&branch="+currentBranch, function(ans) {
        if(Array.isArray(ans.Data)) {
          html = ["<ul class='list-group' style='padding:10px;'>"];
            $.each(ans.Data, function(a,b) {
                html.push("<li class='list-group-item'>"+b+"</li>");
            });
            html.push("</ul>");
            $("#tags").html(html.join(""));
        } else {
            $("#tags").html(ans.Data);
        }
        
        $("#tags").append("<div class='text-center'><button class='btn btn-success btn-sm' onclick='addTag()'><i class='fa fa-plus'></i> Add Tag</button></div>");
    },"json");
}
function loadCommitHistory() {
    processAJAXQuery(_service("versionControl","history")+"&path="+currentPath+"&branch="+currentBranch, function(ans) {
        if(Array.isArray(ans.Data)) {
          ans.Data = ans.Data.join("\n");  
        }
        $("#history").html(ans.Data);
    },"json");
}
function loadChanges() {
    processAJAXQuery(_service("versionControl","changes")+"&path="+currentPath+"&branch="+currentBranch, function(ans) {
        if(ans.Data.status!=null && Array.isArray(ans.Data.status)) {
          ans.Data = ans.Data.status.join("\n");  
        }
        $("#changes").html("<pre>"+ans.Data+"</pre>");
    },"json");
}
function loadGitIgnore() {
    processAJAXQuery(_service("versionControl","gitignore")+"&path="+currentPath+"&branch="+currentBranch, function(ans) {
        if(Array.isArray(ans.Data)) {
          ans.Data = ans.Data.join("\n");  
        }
        if(ans.Data.length<=0) {
            $("#gitignore").html("<div class='text-center'><br><br><br><button class='btn btn-success btn-lg' onclick='openGitignoreFile()'>Create Gitignore File</button></div>");
        } else {
            $("#gitignore").html("<pre>"+ans.Data+"</pre>");
            $("#gitignore").append("<div class='text-center'><button class='btn btn-success btn-sm' onclick='openGitignoreFile()'><i class='fa fa-pencil'></i> Edit Gitignore File</button></div>");
        }
    },"json");
}
function openGitignoreFile() {
    lx = _link("modules/cmsEditor") + "&type=autocreate&src=" + encodeURIComponent(currentPath+".gitignore");
    top.openLinkFrame("gitignore", lx);
}
function addTag() {
    lgksPrompt("Add Tag to current Commit","New Tag", function(ans) {
        	if(ans!=null && ans.length>0) {
                processAJAXPostQuery(_service("versionControl","tag-add"),"&path="+currentPath+"&branch="+currentBranch+"&name="+ans, function(ans) {
                    if(ans.Data.length>0) lgksToast("Error Occured");
                    else lgksToast("Tag added successfully");
                    loadTaglist();
                },"json");
        	}
        });
}
</script>