<div class="modal fade" id="modal_cover" tabindex="-1" role="dialog" aria-hidden="true">
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
              <label for="inputTitle" class="control-label">标题</label>
              <input type="text" class="form-control" id="inputTitle" name="title" autocomplete="off" required>
            </div>

            <div class="form-group">
              <label for="inputCover" class="control-label">当天封面大图</label>
              <input type="text" class="form-control" id="inputCover" name="cover" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputSource" class="control-label">来源</label>
              <input type="text" class="form-control" id="inputSource" name="source" autocomplete="off">
            </div>

            <div class="form-group">
              <label for="inputType" class="control-label">类型</label>
              <input type="text" class="form-control" id="inputType" name="type" autocomplete="off">
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