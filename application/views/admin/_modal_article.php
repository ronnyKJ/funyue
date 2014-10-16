<div class="modal fade" id="modal_article" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg article-panel">
    <div class="modal-content">
      <form role="form" name="messageForm" id="messageForm" method="post" enctype="multipart/form-data" action="/admin/article/save">
      <div class="modal-body">
        <!-- <div class="alert alert-success">保存成功！</div> -->
        <div class="alert alert-warning hide">失败了，请再试一次吧。</div>
        <div class="row">
          <div class="col-xs-5">

            <div class="form-group" style="height: 0;width: 0;overflow: hidden;">
              <label for="inputId" class="control-label">id</label>
              <input type="text" class="form-control" id="inputId" name="id" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputDate" class="control-label">日期</label>
              <input type="text" style="width:100px;display: inline-block;" class="form-control" id="inputDate" name="created_date" autocomplete="off" required data-default="<?echo date('md')?>" />
              <label for="inputTop" class="control-label">权重</label>
              <input type="text" style="width:100px;display: inline-block;" class="form-control" id="inputTop" name="top" autocomplete="off" data-default="0" />
            </div>

            <div class="form-group">
              <label for="inputTitle" class="control-label">标题</label>
              <input type="text" class="form-control" id="inputTitle" name="title" autocomplete="off" required>
            </div>

            <div class="form-group">
              <label for="inputCover" class="control-label">本文封面</label>
              <input type="text" class="form-control" id="inputCover" name="cover" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputSource" class="control-label">来源</label>
              <input type="text" class="form-control" id="inputSource" name="source" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputSourceLink" class="control-label">来源链接</label>
              <input type="text" class="form-control" id="inputSourceLink" name="source_link" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputType" class="control-label">文章类型</label><a style="margin-left:20px" target="_blank" href="/admin/article/articleConfig">查看类型</a>
              <input type="text" class="form-control" id="inputType" name="type" autocomplete="off">

              合作方:<input type="text" id="inputFrom" name="from" autocomplete="off">
              <span style="margin-left:10px">请填写lebo或news或不填</span>
              <br>乐播时长:<input type="text" id="inputExt" name="ext" autocomplete="off">
              <span style="margin-left:10px">不是乐播暂时不填</span>
            </div>

            <div class="form-group" style="display:none;">
              <label for="inputScore" class="control-label">比分牌</label>
              <textarea class="form-control" id="inputScore" name="score_mod" autocomplete="off" style="height:100px"></textarea>
            </div>

            <div class="form-group">
              <label for="inputVideo" class="control-label">音频/视频链接</label>
              <textarea type="text" class="form-control" id="inputVideo" name="media_mod" autocomplete="off"></textarea>
            </div>

            <div class="form-group" style="display:none;">
              <label for="inputEvent" class="control-label">关键事件</label>
              <textarea type="text" class="form-control" id="inputEvent" name="event_mod" autocomplete="off" style="height:100px"></textarea>
            </div>

            <div class="form-group">
              <label for="inputAdImg" class="control-label">广告图片</label>
              <input type="text" class="form-control" id="inputAdImg" name="ad_img" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputAdLink" class="control-label">广告链接</label>
              <input type="text" class="form-control" id="inputAdLink" name="ad_link" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputShareImg" class="control-label">分享的图片</label>
              <input type="text" class="form-control" id="inputShareImg" name="share_img" autocomplete="off">
            </div>

          </div>
          <div class="col-xs-7">
            <div class="form-group">
              <label for="inputContent" class="control-label">正文</label>
              <textarea class="form-control" id="inputContent" style="height:750px" name="content" rows="20" data-editor="true"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">提交</button>
      </div>
      </form>
    </div>
  </div>
</div>