<ul>
    <?php
        foreach($requirements as $requirement) {
            
            if ($requirement['status']) {
                ?>
                    <li>
                        <i class="icons slicon-check pdf-light-viewer-requirement-success"></i>
                        <?php echo $requirement['name'];?> <?php echo $requirement['success'];?>
                        
                        <?php if (isset($requirement['description']) && $requirement['description']) { ?>
                            <a href="#!" class="js-tip tip" title="<?php echo $requirement['description'] ?>"><span class="icons slicon-question"></span></a>
                        <?php } ?>
                    </li>
                <?php
            }
            else {
                ?>
                    <li>
                        <i class="icons slicon-close pdf-light-viewer-requirement-fail"></i>
                        <?php echo $requirement['name'];?> <?php echo $requirement['fail'];?>
                        
                        <?php if (isset($requirement['description']) && $requirement['description']) { ?>
                            <a href="#!" class="js-tip tip" title="<?php echo $requirement['description'] ?>"><span class="icons slicon-question"></span></a>
                        <?php } ?>
                    </li>
                <?php
            }
        }
    ?>
</ul>
