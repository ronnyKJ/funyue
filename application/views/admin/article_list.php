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
                <div class="collapse navbar-collapse" style="position:relative;">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="#" data-action="create" role="button">发布</a>
                            <div style="position:absolute;width:300px;left:170px;top:12px;">
                                日期：<input id="date-input" type="text" placeholder="日期格式，如0409" />
                                <input id="date-btn" type="button" value="查看" />
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <ul class="nav nav-tabs">
              <li<?php if(preg_match("/show/i", $this->uri->uri_string())):?> class="active"<?php endif?>><a href="<?php echo $uri['show']?>">线上</a></li>
              <li<?php if(preg_match("/hide/i", $this->uri->uri_string())):?> class="active"<?php endif?>><a href="<?php echo $uri['hide']?>">线下</a></li>
            </ul>
            <h3>当前日期：<?php echo $date?></h3>
            <table id="articles" class="table table-striped with-options">
                <thead>
                    <tr>
                        <td></td>
                        <td>标题</td>
                        <td>来源</td>
                        <td>类型</td>
                        <td>更新时间</td>
                        <td>阅读数</td>
                        <td>权重</td>
                    </tr>
                </thead>
                <?php if(!empty($result)):?>
                <tbody>
                    <?php foreach($result as $row):?>
                    <tr data-id="<?php echo $row['id']?>">
                        <td><button type="button" class="btn <?php echo $row['hide'] == 1 ? 'btn-info' : 'btn-default'; ?>" data-action="hide" data-params="<?php echo $row['hide'] == 1 ? 0 : 1; ?>"><?php echo $row['hide'] == 1 ? '显示' : '隐藏'; ?></button></td>
                        <td class="title">
                            <a href="#<?php echo $row['id']?>" data-action="edit" data-params="<?php echo $row['id']?>"><?php echo $row['title']?></a>
                            <div class="options">
                                <a href="#" data-action="edit" data-params="<?php echo $row['id']?>">编辑</a>
                                <a href="<?php if( $this->config->item('env') == 'production' ) echo 'http://funyue.duapp.com' ?>/admin/preview/index/<?php echo md5(date('YmdH'))?>#news/<?php echo $date?>/<?php echo $row['id']?>" target="_blank">预览</a>
                            </div>
                        </td>
                        <td><?php if(!empty($row['link'])):?><a href="<?php echo $row['link']?>" target="_blank"><?php endif?><?php echo $row['source']?><?php if(!empty($row['link'])):?></a><?php endif?></td>
                        <td><span style="color:<?php echo $row['typeColor']?>"><?php echo $row['typeStr']?></span></td>
                        <td><?php echo date('m/d H:i', $row['create_at'])?></td>
                        <td><?php echo $row['views']?></td>
                        <td><?php echo $row['top']?></td>
                    </tr>
                    <?php endforeach?>
                </tbody>
                <?php endif?>
            </table>
        </div>
    </div>
</div>
<?php include '_modal_article.php' ?>
