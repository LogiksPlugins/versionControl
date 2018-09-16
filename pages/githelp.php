<?php
$infoArr = [
    "https://help.github.com/articles/connecting-to-github-with-ssh/"=>"Connecting to GitHub with SSH",
    "https://git-scm.com/docs/"=>"Git Reference",
    "https://guides.github.com/introduction/git-handbook/"=>"Git Handbook",
    "https://help.github.com/articles/set-up-git/"=>"Set up git",
    "https://backlog.com/git-tutorial/"=>"Git Tutorial",
];
?>
<div style='padding:20px;'>
    <table class="table table-hover">
        <tbody>
            <?php
                foreach($infoArr as $a=>$b) {
                ?>
                <tr>
                    <td><?=$b?></td>
                    <td><a href='<?=$a?>' target=_blank><?=$a?></a></td>
                </tr>
                <?php
                }
            ?>
        </tbody>
    </table>
</div>