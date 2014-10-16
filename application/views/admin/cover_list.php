<div class="row">
    <div class="col-md-12">
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <h3>FUN阅世界杯2014</h3>
        </nav>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <?php include '_sidebar.php' ?>
        </div>
        <div class="col-md-10">
            <nav class="navbar navbar-default" role="navigation">
                <div class="collapse navbar-collapse" style="padding-top: 11px;">
                    <input id="today-date" value="<?php echo date('md')?>" style="width:40px;" />
                    <input class="today-img" type="text" style="width:400px;" />
                    <a href="javascript:;" class="add">发布今天封面</a>        
                </div>
            </nav>

            <table id="articles" class="table table-striped with-options">
                <?php if(!empty($result)):?>
                <tbody>
                    <?php foreach($result as $row):?>
                    <tr>
                        <td class="d_<?php echo $row['date']?>">
                            <a href="javascript:;" data-action="edit" data-params="<?php echo $row['id']?>"><?php echo $row['date']?></a>
                            <input type="text" value="<?php echo $row['img']?>" style="width:300px;" />
                            <a href="<?php echo $row['img']?>" target="_blank">查看封面</a>
                            <a class="update" href="javascript:;" data-id="<?php echo $row['id']?>" target="_blank">更新封面</a>
                        </td>
                    </tr>
                    <?php endforeach?>
                </tbody>
                <?php endif?>
            </table>
        </div>
    </div>
</div>
