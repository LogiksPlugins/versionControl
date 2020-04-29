var currentPath = "/";
var currentBranch = 'master';
var currentRemote = 'origin';
$(function() {
    // $("#pgsidebar").attr("class","pageCompSidebar col-xs-12 col-sm-2 col-md-2");
    // $("#pgcontent").attr("class","pageCompContent col-xs-12 col-sm-10 col-md-10");
    // $("#pgcontent").attr("class","pageCompContent col-xs-4 col-sm-4 col-md-4");
    // $("#pgworkspace").append("<div id='pgpreview' class='pageCompPreview col-xs-4 col-sm-4 col-md-4'><div>Preview</div></div>");
    
    $("#pgtoolbar .onrepoload").addClass("hidden");
    
    $("#sidebarArea").delegate(".list-group-item","click", function() {
        currentPath = $(this).data("path");
        $("#sidebarArea .list-group-item.active").removeClass("active");
        $(this).addClass("active");
        loadRepo();
    });
    
    $("#pgcontent").delegate("#branchList","change", function() {
    	lgksConfirm("Do you want to check out branch :"+$(this).val(), "Checkout", function(ans) {
    		if(ans) {
    			processAJAXPostQuery(_service("versionControl","checkout")+"&path="+currentPath+"&branch="+currentBranch, "checkoutbranch="+$("#branchList").val(), function(ans) {
                            if(Array.isArray(ans.Data)) {
                              ans.Data = ans.Data.join("\n");  
                            }
                            loadRepo();
                        },"json");
    		} else {
    			$("#branchList").val($("#branchList").find("option.selected").val());
    		}
    	});
    });
    
    listREPOS();
});

function showHelp() {
    $("#contentArea").load(_service("versionControl","githelp")+"&path="+currentPath, function() {
        
    });
}

function listREPOS() {
    if($("#pgsidebar").length<=0) return;
    
    $("#sidebarArea").html("<div class='ajaxloading ajaxloading3'></div>");
    $("#contentArea").html("<h2 align=center><br><br>Load repository <br><br><i class='fa fa-git-square fa-3x'></i></h2>");
    
    processAJAXQuery(_service("versionControl","listrepo"), function(ans) {
        html = "<ul class='list-group'>";
        $.each(ans.Data, function(a,b) {
            html += "<li class='list-group-item' data-path='"+b.path+"'><span class='title'>"+a+
                    "</span><i class='fa fa-chevron-right pull-right'></i> <citie class='label label-info'>"+b.timestamp+"</citie></li>";
        });
        html += "</ul>";
        $("#sidebarArea").html(html);
        
        if($("#sidebarArea>.list-group").children().length<=0) {
            $("#sidebarArea").html("<h3 align=center>No repo found</h3>");
            lgksConfirm("No repo found. Initialize the app to Repo?", "Initialize AppRepo?", function(ans) {
                	if(ans) {
                		processAJAXPostQuery(_service("versionControl","gitinit"),"path=/", function(ans) {
                                lgksToast(ans.Data);
                                listREPOS();
                            },"json");
                	}
                });
        } else {
            checkRepoStatus();
        }
    },"json");
}
function checkRepoStatus() {
    $("#pgtoolbar .nav.navbar-left").append("<li class='navloading'><div class='pull-left ajaxloading ajaxloading8' style='padding: 13px;'>Detecting Changes</div></li>")
    checkCount = 0; 
    $(".list-group-item","#sidebarArea").each(function(a) {
        path = $(this).data("path");
        processAJAXQuery(_service("versionControl","gitstatus","json")+"&path="+path, function(ans) {
            if(ans.Data.status.length>4) {
                $(".list-group-item[data-path='"+ans.Data.repo+"']","#sidebarArea").find(".label").attr("class","label label-warning");
            }
            checkCount++;
            if(checkCount>=$(".list-group-item","#sidebarArea").length) {
                $("#pgtoolbar .nav.navbar-left .navloading").detach();
            }
        }, "json")
    });
}
function addGitRepo() {
    lgksPrompt("Relative path to git folder (/ for app, plugins/modules/<moduleName>, etc)","Create Repo", function(ans) {
        	if(ans!=null && ans.length>0) {
        		processAJAXPostQuery(_service("versionControl","gitinit"),"path="+ans, function(ans) {
                        lgksToast(ans.Data);
                        listREPOS();
                    },"json");
        	}
        });
}
function loadRepo() {
    $("#contentArea").load(_service("versionControl","repodetails")+"&path="+currentPath, function() {
        
    });
}

function addAllFiles() {
    displayLoader("<pre>Adding all files ...</pre>");
    processAJAXQuery(_service("versionControl","gitaddall")+"&path="+currentPath+"&branch="+currentBranch, function(ans) {
        $($("#repoDetailsTab .nav a")[0]).tab("show");
        loadChanges();
    });
}

function commitGitRepo() {
    lgksPrompt("Commit Message","Commit Repo", function(ans) {
        	if(ans!=null && ans.length>0) {
        		displayLoader("<pre>Commiting Added Files ...</pre>");
                processAJAXPostQuery(_service("versionControl","gitcommit"),"&path="+currentPath+"&branch="+currentBranch+"&msg="+ans, function(ans) {
                    displayResults(ans.Data);
                },"json");
        	}
        });
}

function pushGitRepo() {
    lgksPrompt("Remote Source Name To Push Commits To","Remote Push", function(ans) {
        	if(ans!=null && ans.length>0) {
        	    displayLoader("<pre>Pushing Commits to remote ...</pre>");
                processAJAXPostQuery(_service("versionControl","gitpush"),"&path="+currentPath+"&branch="+currentBranch+"&srcname="+ans, function(ans) {
                    displayResults(ans.Data);
                },"json");
        	}
        });
}
function pullGitRepo() {
    lgksPrompt("Remote Source Name To Pull Commits From","Remote Pull", function(ans) {
        	if(ans!=null && ans.length>0) {
        	    displayLoader("<pre>Pulling Commits from remote ...</pre>");
                processAJAXPostQuery(_service("versionControl","gitpull"),"&path="+currentPath+"&branch="+currentBranch+"&srcname="+ans, function(ans) {
                    displayResults(ans.Data);
                },"json");
        	}
        });
}

function showRepoToolButtons() {
    $("#pgtoolbar .onrepoload").removeClass("hidden");
}
function hideRepoToolButtons() {
    $("#pgtoolbar .onrepoload").addClass("hidden");
}

function displayLoader(msg) {
    $($("#repoDetailsTab .nav a")[0]).tab("show");
    $("#changes").html(msg);
}

function displayResults(data) {
    $($("#repoDetailsTab .nav a")[0]).tab("show");
    if(Array.isArray(data)) {
      data = data.join("\n");
    }
    $("#changes").html("<pre>"+data+"</pre>");
}